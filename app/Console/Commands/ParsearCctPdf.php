<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Smalot\PdfParser\Parser;

class ParsearCctPdf extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'cct:parsear-pdf {--path= : Ruta alternativa al archivo PDF (por defecto storage/app/cct.pdf)}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Extrae y divide en cláusulas o artículos el texto de storage/app/cct.pdf usando smalot/pdfparser';

    /**
     * Execute the console command.
     */
    public function handle(): int
    {
        $this->info('📄 Iniciando proceso de extracción y parseo del CCT...');

        // 1. Resolver la ruta del archivo PDF
        $customPath = $this->option('path');

        if ($customPath) {
            $filePath = $customPath;
        } else {
            // Verificar primero en storage/app/cct.pdf
            $defaultPath = storage_path('app/cct.pdf');
            $privatePath = storage_path('app/private/cct.pdf');

            if (file_exists($defaultPath)) {
                $filePath = $defaultPath;
            } elseif (file_exists($privatePath)) {
                $filePath = $privatePath;
            } else {
                $filePath = $defaultPath;
            }
        }

        $this->line("🔍 Buscando archivo en: <comment>{$filePath}</comment>");

        // 2. Validar existencia del archivo
        if (!file_exists($filePath)) {
            $this->newLine();
            $this->error("❌ El archivo no existe en la ruta: {$filePath}");
            $this->line("💡 Por favor coloca el archivo <comment>cct.pdf</comment> dentro de <info>storage/app/</info>");
            $this->line("   O proporciona una ruta personalizada usando: <comment>--path=\"ruta/al/archivo.pdf\"</comment>");
            return self::FAILURE;
        }

        // 3. Extraer texto usando smalot/pdfparser
        $this->line('⏳ Leyendo y extrayendo texto del documento PDF...');
        $startTime = microtime(true);

        try {
            $parser = new Parser();
            $pdf = $parser->parseFile($filePath);
            $pagesCount = count($pdf->getPages());
            $rawText = $pdf->getText();
        } catch (\Throwable $e) {
            $this->error("❌ Error al procesar el archivo PDF: {$e->getMessage()}");
            return self::FAILURE;
        }

        $elapsedTime = round(microtime(true) - $startTime, 2);

        if (empty(trim($rawText))) {
            $this->warn('⚠️ El documento PDF se leyó correctamente pero no contiene texto extraíble.');
            $this->line('   Es posible que el PDF contenga imágenes escaneadas sin capa de texto OCR.');
            return self::FAILURE;
        }

        $this->info("✅ Extracción completada en {$elapsedTime} s. Páginas: {$pagesCount} | Caracteres extraídos: " . number_format(mb_strlen($rawText)));
        $this->newLine();

        // 4. Normalizar saltos de línea
        $normalizedText = str_replace(["\r\n", "\r"], "\n", $rawText);

        // 5. Expresión regular para detectar divisiones de 'CLÁUSULA [Número]' o 'ARTÍCULO [Número]'
        // Soporta números arábigos (1, 2, 10), romanos (I, IV, X) u ordinales escritos (PRIMERA, SEGUNDO, etc.)
        $regex = '/(?=(?:^|\n)\s*(?:CL[AÁ]USULA|ART[IÍ]CULO)\s+(?:[0-9]+[a-zA-Zªº\.\-]*|[IVXLCDM]+|[A-ZÁÉÍÓÚa-z]+)\b)/iu';

        $rawSections = preg_split($regex, $normalizedText, -1, PREG_SPLIT_NO_EMPTY);

        $sections = [];
        $preamble = null;

        foreach ($rawSections as $rawSection) {
            $trimmed = trim($rawSection);
            if (empty($trimmed)) {
                continue;
            }

            // Comprobar si la sección inicia con CLÁUSULA o ARTÍCULO
            if (preg_match('/^(?:CL[AÁ]USULA|ART[IÍ]CULO)\s+/iu', $trimmed)) {
                // Dividir la primera línea para usarla como título y el resto como contenido
                $lines = explode("\n", $trimmed, 2);
                $title = trim($lines[0]);
                $content = isset($lines[1]) ? trim($lines[1]) : '';

                // Limpiar espacios y saltos de línea repetidos en el contenido
                $content = preg_replace("/[ \t]+/", ' ', $content);
                $content = preg_replace("/\n{3,}/", "\n\n", $content);

                $sections[] = [
                    'title' => $title,
                    'content' => $content,
                    'char_count' => mb_strlen($content),
                ];
            } else {
                // Texto inicial antes de la primera cláusula (declaraciones, encabezado, introducción)
                $preamble = $trimmed;
            }
        }

        $totalSections = count($sections);

        // 6. Imprimir resumen de secciones detectadas
        $this->line('====================================================');
        $this->info("📊 RESULTADOS DEL PARSEO:");
        $this->line("   • Total de cláusulas/artículos detectados: <options=bold>{$totalSections}</>");
        if ($preamble) {
            $this->line("   • Preámbulo / Declaraciones iniciales: <comment>Detectado (" . number_format(mb_strlen($preamble)) . " caracteres)</comment>");
        }
        $this->line('====================================================');
        $this->newLine();

        if ($totalSections === 0) {
            $this->warn('⚠️ No se encontraron cláusulas ni artículos con el patrón esperado.');
            $this->line('   Revisa si el documento utiliza otra nomenclatura (por ejemplo "CAPÍTULO" o "SECCIÓN").');
            return self::SUCCESS;
        }

        // 7. Mostrar muestra de las primeras 3 cláusulas extraídas
        $sampleCount = min(3, $totalSections);
        $this->info("🔍 MUESTRA DE LAS PRIMERAS {$sampleCount} CLÁUSULAS EXTRAÍDAS:");
        $this->newLine();

        for ($i = 0; $i < $sampleCount; $i++) {
            $sec = $sections[$i];
            $num = $i + 1;
            $preview = mb_substr($sec['content'], 0, 150);
            if (mb_strlen($sec['content']) > 150) {
                $preview .= '...';
            }

            $this->line("<fg=cyan;options=bold>┌─ [Muestra #{$num}]─────────────────────────────────────────</>");
            $this->line("<fg=cyan>│</> <options=bold>Título:</> <comment>{$sec['title']}</comment>");
            $this->line("<fg=cyan>│</> <options=bold>Longitud total:</> {$sec['char_count']} caracteres");
            $this->line("<fg=cyan>│</> <options=bold>Primeros 150 caracteres:</>");
            $this->line("<fg=cyan>│</> \"{$preview}\"");
            $this->line("<fg=cyan;options=bold>└────────────────────────────────────────────────────────────</>");
            $this->newLine();
        }

        $this->info('✨ Parseo y verificación finalizados con éxito.');

        return self::SUCCESS;
    }
}
