<?php

namespace App\Console\Commands;

use App\Services\AttendanceMigrationService;
use Illuminate\Console\Command;

class MigratePendingAttendances extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'attendances:migrate-pending
                            {--matricula= : Migrate only for a specific employee matricula}
                            {--all : Migrate all pending attendances for all users}
                            {--stats : Show statistics about pending attendances}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Migrate pending attendances to actual attendances when users register';

    protected $migrationService;

    /**
     * Create a new command instance.
     */
    public function __construct(AttendanceMigrationService $migrationService)
    {
        parent::__construct();
        $this->migrationService = $migrationService;
    }

    /**
     * Execute the console command.
     */
    public function handle(): int
    {
        // Show statistics if requested
        if ($this->option('stats')) {
            $this->showStatistics();
            return self::SUCCESS;
        }

        // Migrate for specific matricula
        if ($matricula = $this->option('matricula')) {
            $this->migrateByMatricula($matricula);
            return self::SUCCESS;
        }

        // Migrate all pending
        if ($this->option('all')) {
            $this->migrateAll();
            return self::SUCCESS;
        }

        // If no option provided, show help
        $this->error('Please specify an option: --matricula, --all, or --stats');
        $this->info('Examples:');
        $this->line('  php artisan attendances:migrate-pending --stats');
        $this->line('  php artisan attendances:migrate-pending --matricula=12345');
        $this->line('  php artisan attendances:migrate-pending --all');

        return self::FAILURE;
    }

    /**
     * Show statistics about pending attendances.
     */
    protected function showStatistics(): void
    {
        $this->info('📊 Pending Attendances Statistics');
        $this->newLine();

        $stats = $this->migrationService->getPendingStatistics();

        $this->table(
            ['Metric', 'Count'],
            [
                ['Total Pending', $stats['total_pending']],
                ['Total Migrated', $stats['total_migrated']],
                ['Unique Employees Pending', $stats['unique_employees_pending']],
                ['Employees with Users', $stats['employees_with_users']],
                ['Employees without Users', $stats['employees_without_users']],
            ]
        );

        if ($stats['employees_with_users'] > 0) {
            $this->newLine();
            $this->info("💡 You can migrate {$stats['employees_with_users']} employee(s) with: php artisan attendances:migrate-pending --all");
        }
    }

    /**
     * Migrate pending attendances for a specific matricula.
     */
    protected function migrateByMatricula(string $matricula): void
    {
        $this->info("🔄 Migrating pending attendances for matricula: {$matricula}");

        $result = $this->migrationService->migratePendingAttendancesByMatricula($matricula);

        if ($result['migrated'] > 0) {
            $this->info("✅ Successfully migrated {$result['migrated']} attendance(s)");
        } else {
            $this->warn("⚠️  {$result['message']}");
        }

        if (!empty($result['failed'])) {
            $this->error("❌ Failed to migrate " . count($result['failed']) . " attendance(s)");
            $this->table(
                ['Pending ID', 'Event ID', 'Error'],
                collect($result['failed'])->map(function ($fail) {
                    return [
                        $fail['pending_attendance_id'],
                        $fail['event_id'],
                        $fail['error'],
                    ];
                })->toArray()
            );
        }
    }

    /**
     * Migrate all pending attendances.
     */
    protected function migrateAll(): void
    {
        $this->info('🔄 Migrating all pending attendances...');
        $this->newLine();

        $progressBar = $this->output->createProgressBar();
        $progressBar->start();

        $result = $this->migrationService->migrateAllPending();

        $progressBar->finish();
        $this->newLine(2);

        $this->info("✅ Migration completed!");
        $this->newLine();

        $this->table(
            ['Metric', 'Count'],
            [
                ['Users Processed', $result['users_processed']],
                ['Total Migrated', $result['total_migrated']],
                ['Total Failed', $result['total_failed']],
            ]
        );

        if ($result['total_failed'] > 0) {
            $this->newLine();
            $this->error("❌ Some migrations failed. Check the logs for details.");
        }
    }
}
