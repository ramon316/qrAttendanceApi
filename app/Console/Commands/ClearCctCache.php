<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

class ClearCctCache extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'cct:cache-clear';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Limpia la caché de consultas del CCT';

    /**
     * Execute the console command.
     */
    public function handle(): int
    {
        $deletedCount = 0;

        // 1. Si el driver soporta tags (Redis / Memcached / Array)
        if (Cache::supportsTags()) {
            Cache::tags(['cct_queries'])->flush();
        }

        // 2. Limpiar llaves registradas en el índice auxiliar (útil para file / array / otros drivers)
        $registeredKeys = Cache::get('cct_query_keys_registry', []);
        if (is_array($registeredKeys)) {
            foreach ($registeredKeys as $key) {
                if (Cache::forget($key)) {
                    $deletedCount++;
                }
            }
            Cache::forget('cct_query_keys_registry');
        }

        // 3. Si se utiliza el driver database, eliminar cualquier registro con prefijo cct_query_
        $defaultStore = config('cache.default');
        $storeDriver = config("cache.stores.{$defaultStore}.driver");

        if ($storeDriver === 'database') {
            $table = config("cache.stores.{$defaultStore}.table", 'cache');
            $connection = config("cache.stores.{$defaultStore}.connection");

            if (Schema::connection($connection)->hasTable($table)) {
                $dbDeleted = DB::connection($connection)
                    ->table($table)
                    ->where('key', 'like', '%cct_query_%')
                    ->delete();

                $deletedCount = max($deletedCount, $dbDeleted);
            }
        }

        $this->info("Caché de consultas del CCT limpiada correctamente ({$deletedCount} registros eliminados).");

        return self::SUCCESS;
    }
}

