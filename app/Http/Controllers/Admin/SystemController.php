<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Services\AttendanceMigrationService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Cache;

class SystemController extends Controller
{
    protected $migrationService;

    public function __construct(AttendanceMigrationService $migrationService)
    {
        $this->migrationService = $migrationService;
    }

    /**
     * Show system tools dashboard
     */
    public function index()
    {
        $stats = $this->migrationService->getPendingStatistics();

        return view('admin.system.index', compact('stats'));
    }

    /**
     * Show pending attendances statistics
     */
    public function pendingStats()
    {
        $stats = $this->migrationService->getPendingStatistics();

        return response()->json([
            'success' => true,
            'data' => $stats,
        ]);
    }

    /**
     * Migrate all pending attendances
     */
    public function migrateAll(Request $request)
    {
        try {
            set_time_limit(300); // 5 minutos máximo

            $result = $this->migrationService->migrateAllPending();

            if ($request->expectsJson()) {
                return response()->json([
                    'success' => true,
                    'data' => $result,
                ]);
            }

            return redirect()->route('admin.system.index')
                ->with('success', $result['message']);
        } catch (\Exception $e) {
            if ($request->expectsJson()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Error al migrar: ' . $e->getMessage(),
                ], 500);
            }

            return redirect()->route('admin.system.index')
                ->with('error', 'Error al migrar: ' . $e->getMessage());
        }
    }

    /**
     * Migrate pending attendances by matricula
     */
    public function migrateByMatricula(Request $request)
    {
        $request->validate([
            'matricula' => 'required|string',
        ]);

        try {
            $result = $this->migrationService->migratePendingAttendancesByMatricula($request->matricula);

            if ($request->expectsJson()) {
                return response()->json([
                    'success' => true,
                    'data' => $result,
                ]);
            }

            $messageType = $result['migrated'] > 0 ? 'success' : 'warning';

            return redirect()->route('admin.system.index')
                ->with($messageType, $result['message']);
        } catch (\Exception $e) {
            if ($request->expectsJson()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Error al migrar: ' . $e->getMessage(),
                ], 500);
            }

            return redirect()->route('admin.system.index')
                ->with('error', 'Error al migrar: ' . $e->getMessage());
        }
    }

    /**
     * Clear all cache
     */
    public function clearCache(Request $request)
    {
        try {
            Artisan::call('cache:clear');
            Artisan::call('config:clear');
            Artisan::call('route:clear');
            Artisan::call('view:clear');

            $message = 'Cache limpiado exitosamente (cache, config, routes, views)';

            if ($request->expectsJson()) {
                return response()->json([
                    'success' => true,
                    'message' => $message,
                ]);
            }

            return redirect()->route('admin.system.index')
                ->with('success', $message);
        } catch (\Exception $e) {
            if ($request->expectsJson()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Error al limpiar cache: ' . $e->getMessage(),
                ], 500);
            }

            return redirect()->route('admin.system.index')
                ->with('error', 'Error al limpiar cache: ' . $e->getMessage());
        }
    }

    /**
     * Optimize application
     */
    public function optimize(Request $request)
    {
        try {
            Artisan::call('optimize');
            Artisan::call('config:cache');
            Artisan::call('route:cache');
            Artisan::call('view:cache');

            $message = 'Aplicación optimizada exitosamente (config, routes, views cached)';

            if ($request->expectsJson()) {
                return response()->json([
                    'success' => true,
                    'message' => $message,
                ]);
            }

            return redirect()->route('admin.system.index')
                ->with('success', $message);
        } catch (\Exception $e) {
            if ($request->expectsJson()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Error al optimizar: ' . $e->getMessage(),
                ], 500);
            }

            return redirect()->route('admin.system.index')
                ->with('error', 'Error al optimizar: ' . $e->getMessage());
        }
    }

    /**
     * Clear compiled files and optimize
     */
    public function clearOptimize(Request $request)
    {
        try {
            Artisan::call('optimize:clear');

            $message = 'Optimizaciones limpiadas exitosamente';

            if ($request->expectsJson()) {
                return response()->json([
                    'success' => true,
                    'message' => $message,
                ]);
            }

            return redirect()->route('admin.system.index')
                ->with('success', $message);
        } catch (\Exception $e) {
            if ($request->expectsJson()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Error: ' . $e->getMessage(),
                ], 500);
            }

            return redirect()->route('admin.system.index')
                ->with('error', 'Error: ' . $e->getMessage());
        }
    }
}
