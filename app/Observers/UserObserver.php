<?php

namespace App\Observers;

use App\Models\User;
use App\Services\AttendanceMigrationService;
use Illuminate\Support\Facades\Log;

class UserObserver
{
    protected $migrationService;

    public function __construct(AttendanceMigrationService $migrationService)
    {
        $this->migrationService = $migrationService;
    }

    /**
     * Handle the User "created" event.
     * This automatically migrates pending attendances when a new user registers.
     *
     * @param User $user
     * @return void
     */
    public function created(User $user): void
    {
        // Only attempt migration if user has an employee_id
        if (empty($user->employee_id)) {
            return;
        }

        try {
            $result = $this->migrationService->migratePendingAttendancesForUser($user);

            if ($result['migrated'] > 0) {
                Log::info("Auto-migrated {$result['migrated']} pending attendances for new user {$user->id} (employee_id: {$user->employee_id})");
            }

            if (!empty($result['failed'])) {
                Log::warning("Failed to migrate some attendances for user {$user->id}: " . json_encode($result['failed']));
            }
        } catch (\Exception $e) {
            Log::error("Error auto-migrating pending attendances for user {$user->id}: {$e->getMessage()}");
        }
    }

    /**
     * Handle the User "updated" event.
     * This migrates pending attendances if employee_id was added/changed.
     *
     * @param User $user
     * @return void
     */
    public function updated(User $user): void
    {
        // Check if employee_id was changed
        if (!$user->isDirty('employee_id')) {
            return;
        }

        // Only attempt migration if new employee_id is not empty
        if (empty($user->employee_id)) {
            return;
        }

        try {
            $result = $this->migrationService->migratePendingAttendancesForUser($user);

            if ($result['migrated'] > 0) {
                Log::info("Auto-migrated {$result['migrated']} pending attendances for updated user {$user->id} (new employee_id: {$user->employee_id})");
            }
        } catch (\Exception $e) {
            Log::error("Error auto-migrating pending attendances for updated user {$user->id}: {$e->getMessage()}");
        }
    }
}
