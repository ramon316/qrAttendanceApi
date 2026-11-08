<?php

namespace App\Services;

use App\Models\Attendance;
use App\Models\PendingAttendance;
use App\Models\User;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class AttendanceMigrationService
{
    /**
     * Migrate pending attendances for a specific user.
     *
     * @param User $user
     * @return array Array with 'migrated' count and 'failed' array
     */
    public function migratePendingAttendancesForUser(User $user): array
    {
        if (empty($user->employee_id)) {
            return [
                'migrated' => 0,
                'failed' => [],
                'message' => 'User does not have an employee_id',
            ];
        }

        $pendingAttendances = PendingAttendance::notMigrated()
            ->where('employee_matricula', $user->employee_id)
            ->get();

        if ($pendingAttendances->isEmpty()) {
            return [
                'migrated' => 0,
                'failed' => [],
                'message' => 'No pending attendances found for this user',
            ];
        }

        // Precargar en memoria las asistencias existentes del usuario para los eventos involucrados (evita N consultas)
        $existingAttendanceMap = Attendance::where('user_id', $user->id)
            ->whereIn('event_id', $pendingAttendances->pluck('event_id'))
            ->pluck('id', 'event_id')
            ->toArray();

        $migratedCount = 0;
        $failed = [];

        foreach ($pendingAttendances as $pendingAttendance) {
            try {
                DB::beginTransaction();

                $existingAttendanceId = $existingAttendanceMap[$pendingAttendance->event_id] ?? null;

                if ($existingAttendanceId) {
                    // Si ya existe la asistencia, marcar como migrada sin duplicar
                    $pendingAttendance->update([
                        'migrated_to_attendance_id' => $existingAttendanceId,
                        'migrated_at' => now(),
                    ]);
                } else {
                    // Crear nuevo registro de asistencia
                    $attendance = Attendance::create([
                        'event_id' => $pendingAttendance->event_id,
                        'user_id' => $user->id,
                        'user_latitude' => $pendingAttendance->user_latitude,
                        'user_longitude' => $pendingAttendance->user_longitude,
                        'distance_meters' => $pendingAttendance->distance_meters,
                        'verified' => $pendingAttendance->verified,
                        'checked_in_at' => $pendingAttendance->checked_in_at,
                    ]);

                    // Actualizar mapa para evitar duplicados en la misma ejecución
                    $existingAttendanceMap[$pendingAttendance->event_id] = $attendance->id;

                    // Actualizar registro pendiente con la referencia migrada
                    $pendingAttendance->update([
                        'migrated_to_attendance_id' => $attendance->id,
                        'migrated_at' => now(),
                    ]);
                }

                DB::commit();
                $migratedCount++;
            } catch (\Exception $e) {
                DB::rollBack();
                Log::error("Failed to migrate pending attendance {$pendingAttendance->id}: {$e->getMessage()}");
                $failed[] = [
                    'pending_attendance_id' => $pendingAttendance->id,
                    'event_id' => $pendingAttendance->event_id,
                    'error' => $e->getMessage(),
                ];
            }
        }

        return [
            'migrated' => $migratedCount,
            'failed' => $failed,
            'message' => "Successfully migrated {$migratedCount} pending attendances",
        ];
    }

    /**
     * Migrate all pending attendances for a specific employee matricula.
     * This is useful when you know the matricula but not the user yet.
     *
     * @param string $matricula
     * @return array|null
     */
    public function migratePendingAttendancesByMatricula(string $matricula): ?array
    {
        $user = User::where('employee_id', $matricula)->first();

        if (!$user) {
            return [
                'migrated' => 0,
                'failed' => [],
                'message' => "No user found with employee_id: {$matricula}",
            ];
        }

        return $this->migratePendingAttendancesForUser($user);
    }

    /**
     * Migrate all pending attendances that have matching users.
     * Useful for bulk migrations.
     *
     * @return array
     */
    public function migrateAllPending(): array
    {
        // Consultar únicamente los usuarios que tienen registros pendientes de migrar
        $usersWithPending = User::whereIn('employee_id', function ($query) {
            $query->select('employee_matricula')
                ->from('pending_attendances')
                ->whereNull('migrated_to_attendance_id');
        })->get();

        $totalMigrated = 0;
        $totalFailed = [];
        $processedUsers = 0;

        foreach ($usersWithPending as $user) {
            $result = $this->migratePendingAttendancesForUser($user);
            $totalMigrated += $result['migrated'];
            $totalFailed = array_merge($totalFailed, $result['failed']);
            $processedUsers++;
        }

        return [
            'users_processed' => $processedUsers,
            'total_migrated' => $totalMigrated,
            'total_failed' => count($totalFailed),
            'failed_details' => $totalFailed,
            'message' => "Processed {$processedUsers} users, migrated {$totalMigrated} pending attendances",
        ];
    }

    /**
     * Get statistics about pending attendances.
     *
     * @return array
     */
    public function getPendingStatistics(): array
    {
        $totalPending = PendingAttendance::notMigrated()->count();
        $totalMigrated = PendingAttendance::migrated()->count();

        // Total de empleados únicos con asistencias pendientes
        $uniqueEmployeesPending = PendingAttendance::notMigrated()
            ->distinct('employee_matricula')
            ->count('employee_matricula');

        // Total de empleados pendientes que ya cuentan con usuario registrado mediante JOIN nativo
        $employeesWithUsers = PendingAttendance::notMigrated()
            ->join('users', 'users.employee_id', '=', 'pending_attendances.employee_matricula')
            ->distinct('pending_attendances.employee_matricula')
            ->count('pending_attendances.employee_matricula');

        return [
            'total_pending' => $totalPending,
            'total_migrated' => $totalMigrated,
            'unique_employees_pending' => $uniqueEmployeesPending,
            'employees_with_users' => $employeesWithUsers,
            'employees_without_users' => max(0, $uniqueEmployeesPending - $employeesWithUsers),
        ];
    }
}
