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

        $migratedCount = 0;
        $failed = [];

        foreach ($pendingAttendances as $pendingAttendance) {
            try {
                DB::beginTransaction();

                // Check if attendance already exists for this event and user
                $existingAttendance = Attendance::where('event_id', $pendingAttendance->event_id)
                    ->where('user_id', $user->id)
                    ->first();

                if ($existingAttendance) {
                    // If already exists, just mark as migrated without creating duplicate
                    $pendingAttendance->update([
                        'migrated_to_attendance_id' => $existingAttendance->id,
                        'migrated_at' => now(),
                    ]);

                    Log::info("Pending attendance {$pendingAttendance->id} already exists as attendance {$existingAttendance->id}");
                } else {
                    // Create new attendance record
                    $attendance = Attendance::create([
                        'event_id' => $pendingAttendance->event_id,
                        'user_id' => $user->id,
                        'user_latitude' => $pendingAttendance->user_latitude,
                        'user_longitude' => $pendingAttendance->user_longitude,
                        'distance_meters' => $pendingAttendance->distance_meters,
                        'verified' => $pendingAttendance->verified,
                        'checked_in_at' => $pendingAttendance->checked_in_at,
                    ]);

                    // Update pending attendance with migration info
                    $pendingAttendance->update([
                        'migrated_to_attendance_id' => $attendance->id,
                        'migrated_at' => now(),
                    ]);

                    Log::info("Migrated pending attendance {$pendingAttendance->id} to attendance {$attendance->id} for user {$user->id}");
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
        $pendingAttendances = PendingAttendance::notMigrated()
            ->select('employee_matricula')
            ->distinct()
            ->get();

        $totalMigrated = 0;
        $totalFailed = [];
        $processedUsers = 0;

        foreach ($pendingAttendances as $pending) {
            $user = User::where('employee_id', $pending->employee_matricula)->first();

            if ($user) {
                $result = $this->migratePendingAttendancesForUser($user);
                $totalMigrated += $result['migrated'];
                $totalFailed = array_merge($totalFailed, $result['failed']);
                $processedUsers++;
            }
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

        $pendingByEmployee = PendingAttendance::notMigrated()
            ->select('employee_matricula', DB::raw('count(*) as count'))
            ->groupBy('employee_matricula')
            ->get();

        $employeesWithUsers = User::whereIn('employee_id', $pendingByEmployee->pluck('employee_matricula'))
            ->count();

        return [
            'total_pending' => $totalPending,
            'total_migrated' => $totalMigrated,
            'unique_employees_pending' => $pendingByEmployee->count(),
            'employees_with_users' => $employeesWithUsers,
            'employees_without_users' => $pendingByEmployee->count() - $employeesWithUsers,
        ];
    }
}
