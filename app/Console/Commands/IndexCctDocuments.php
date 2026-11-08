<?php

namespace App\Console\Commands;

use App\Services\GeminiEmbeddingService;
use Exception;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\File;

class IndexCctDocuments extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'cct:index {--fresh : Trunca la tabla cct_chunks antes de indexar}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Parsea los archivos Markdown del CCT desde storage/app/cct y almacena sus fragmentos y embeddings en la base de datos';

    /**
     * Execute the console command.
     */
    public function handle(GeminiEmbeddingService $embeddingService): int
    {
        $dirPath = storage_path('app/cct');

        if (!File::isDirectory($dirPath)) {
            $this->error("El directorio no existe: {$dirPath}");
            return self::FAILURE;
        }

        if ($this->option('fresh')) {
            DB::table('cct_chunks')->truncate();
            $this->info('Tabla cct_chunks truncada correctamente.');
            $this->call('cct:cache-clear');
        }

        $files = File::glob("{$dirPath}/*.md");
        sort($files, SORT_NATURAL);

        if (empty($files)) {
            $this->warn("No se encontraron archivos Markdown (.md) en {$dirPath}");
            return self::SUCCESS;
        }

        $totalIndexed = 0;
        $totalErrors = 0;

        foreach ($files as $filePath) {
            $fileName = pathinfo($filePath, PATHINFO_FILENAME);
            $this->info("Procesando archivo: {$fileName}.md");

            $rawContent = File::get($filePath);
            // Normalizar saltos de línea CRLF a LF para garantizar el corte exacto por \n---\n
            $normalizedContent = str_replace("\r\n", "\n", $rawContent);

            $blocks = explode("\n---\n", $normalizedContent);

            foreach ($blocks as $rawBlock) {
                $block = trim($rawBlock);
                // Limpiar posibles delimitadores '---' residuales al inicio o final del bloque
                $block = trim(preg_replace('/^(?:---\s*)+|(?:\s*---)+$/', '', $block));

                if ($block === '') {
                    continue;
                }

                // Omitir cabeceras de archivo que solo contienen títulos Markdown (# / ##) sin cuerpo ni metadatos
                $lines = array_values(array_filter(array_map('trim', explode("\n", $block)), fn($l) => $l !== ''));
                $allHeaders = !empty($lines) && count(array_filter($lines, fn($l) => str_starts_with($l, '#'))) === count($lines);
                if ($allHeaders) {
                    continue;
                }

                try {
                    // a) Extracción de metadatos (soporta formato [TAG]: valor y [TAG: valor])
                    $documento = 'CCT IMSS-SNTSS';
                    if (preg_match('/\[DOCUMENTO\]:\s*(.+)/', $block, $docMatch)) {
                        $documento = trim($docMatch[1]);
                    } elseif (preg_match('/\[DOCUMENTO:\s*([^\]]+)\]/', $block, $docMatchAlt)) {
                        $documento = trim($docMatchAlt[1]);
                    }

                    $capitulo = $fileName;
                    if (preg_match('/\[CAPITULO\]:\s*(.+)/', $block, $capMatch)) {
                        $capitulo = trim($capMatch[1]);
                    } elseif (preg_match('/\[CAPITULO:\s*([^\]]+)\]/', $block, $capMatchAlt)) {
                        $capitulo = trim($capMatchAlt[1]);
                    }

                    $clausula = null;
                    if (preg_match('/\[CLAUSULA\]:\s*(.+)/', $block, $clausulaMatch)) {
                        $clausula = trim($clausulaMatch[1]);
                    } elseif (preg_match('/\[CLAUSULA:\s*([^\]]+)\]/', $block, $clausulaMatchAlt)) {
                        $clausula = trim($clausulaMatchAlt[1]);
                    }

                    $tema = null;
                    if (preg_match('/\[TEMA\]:\s*(.+)/', $block, $temaMatch)) {
                        $tema = trim($temaMatch[1]);
                    } elseif (preg_match('/\[TEMA:\s*([^\]]+)\]/', $block, $temaMatchAlt)) {
                        $tema = trim($temaMatchAlt[1]);
                    } else {
                        // Soporte para etiquetas [TITULO: ...] y [SECCION: ...] en los archivos actuales
                        $parts = [];
                        if (preg_match('/\[TITULO\]:\s*(.+)|\[TITULO:\s*([^\]]+)\]/', $block, $tituloMatch)) {
                            $parts[] = trim($tituloMatch[1] ?: ($tituloMatch[2] ?? ''));
                        }
                        if (preg_match('/\[SECCION\]:\s*(.+)|\[SECCION:\s*([^\]]+)\]/', $block, $seccionMatch)) {
                            $parts[] = trim($seccionMatch[1] ?: ($seccionMatch[2] ?? ''));
                        }
                        if (!empty($parts)) {
                            $tema = implode(' - ', array_filter($parts));
                        }
                    }

                    // b) Generar embedding del bloque completo
                    $vector = $embeddingService->getEmbedding($block);

                    // c) Insertar registro en cct_chunks
                    DB::table('cct_chunks')->insert([
                        'documento'  => trim($documento),
                        'capitulo'   => trim($capitulo),
                        'clausula'   => $clausula !== null ? trim($clausula) : null,
                        'tema'       => $tema !== null ? trim($tema) : null,
                        'contenido'  => $block,
                        'embedding'  => json_encode($vector),
                        'created_at' => now(),
                        'updated_at' => now(),
                    ]);

                    $totalIndexed++;

                    // d) Retroalimentación visual en consola
                    $clausulaLabel = $clausula ?? 'S/C';
                    $temaLabel = $tema ?? 'Sin tema';
                    $this->line("Indexado: Cláusula {$clausulaLabel} - {$temaLabel}");

                    // e) Retardo de 150ms entre llamadas a la API para prevenir rate-limits
                    usleep(150000);
                } catch (Exception $e) {
                    $totalErrors++;
                    $this->error("Error indexando bloque en {$fileName}.md: " . $e->getMessage());
                }
            }
        }

        $this->info("Proceso finalizado. Fragmentos indexados: {$totalIndexed}. Errores: {$totalErrors}.");

        return self::SUCCESS;
    }
}

