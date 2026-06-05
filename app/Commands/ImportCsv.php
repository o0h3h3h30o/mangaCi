<?php

namespace App\Commands;

use CodeIgniter\CLI\BaseCommand;
use CodeIgniter\CLI\CLI;
use App\Controllers\ImportController;

/**
 * Bulk-import manga from a CSV file, on the command line.
 *
 * Usage:
 *   php spark import:csv <path-to-csv> [options]
 *
 * Options:
 *   --public          Publish imported manga (is_public=1). Default: draft.
 *   --no-chapters     Skip chapter import.
 *   --no-cover        Skip cover download.
 *   --start=N         Skip the first N URLs (resume a run).
 *   --sleep=SECONDS   Delay between rows (default 0).
 *
 * Designed for nohup:
 *   nohup php spark import:csv public/csv2.csv --public > writable/import.log 2>&1 &
 */
class ImportCsv extends BaseCommand
{
    protected $group       = 'Import';
    protected $name        = 'import:csv';
    protected $description = 'Bulk-import manga from a CSV of source URLs.';
    protected $usage       = 'import:csv <path> [--public] [--no-chapters] [--no-cover] [--start=N] [--sleep=S]';
    protected $arguments   = ['path' => 'Path to the CSV file (absolute or relative to project root).'];

    public function run(array $params)
    {
        $path = $params[0] ?? null;
        if (!$path) {
            CLI::error('Missing CSV path. Usage: php spark import:csv <path> [--public]');
            return EXIT_ERROR;
        }
        // Resolve relative paths against project root.
        if (!is_file($path) && is_file(ROOTPATH . ltrim($path, '/'))) {
            $path = ROOTPATH . ltrim($path, '/');
        }
        if (!is_file($path)) {
            CLI::error("File not found: {$path}");
            return EXIT_ERROR;
        }

        $isPublic       = (bool) CLI::getOption('public');
        $importChapters = !CLI::getOption('no-chapters');
        $downloadCover  = !CLI::getOption('no-cover');
        $start          = (int) (CLI::getOption('start') ?? 0);
        $sleep          = (float) (CLI::getOption('sleep') ?? 0);

        $importer = new ImportController();
        $urls = $importer->readCsvUrls($path);
        $total = count($urls);
        if ($total === 0) {
            CLI::error('No valid URLs found in the CSV.');
            return EXIT_ERROR;
        }

        @set_time_limit(0);
        CLI::write("Found {$total} URLs. public=" . ($isPublic ? 'yes' : 'no')
            . " chapters=" . ($importChapters ? 'yes' : 'no')
            . " cover=" . ($downloadCover ? 'yes' : 'no')
            . ($start > 0 ? " start={$start}" : ''), 'yellow');

        $created = 0; $updated = 0; $failed = 0;

        foreach ($urls as $i => $url) {
            $n = $i + 1;
            if ($i < $start) {
                CLI::write(sprintf('[%d/%d] skip %s', $n, $total, $url), 'dark_gray');
                continue;
            }

            $t0 = microtime(true);
            try {
                $res = $importer->processImport([
                    'url'             => $url,
                    'import_chapters' => $importChapters,
                    'download_cover'  => $downloadCover,
                    'is_public'       => $isPublic ? 1 : 0,
                ]);
            } catch (\Throwable $e) {
                // Never let one bad row kill the whole batch.
                $res = ['ok' => false, 'error' => $e->getMessage()];
                log_message('error', "import:csv row {$n} ({$url}) threw: " . $e->getMessage());
            }
            $ms = round((microtime(true) - $t0) * 1000);

            if (!$res['ok']) {
                $failed++;
                CLI::write(sprintf('[%d/%d] FAIL %s -> %s', $n, $total, $url, $res['error'] ?? '?'), 'red');
            } elseif (!empty($res['already_exists'])) {
                $updated++;
                CLI::write(sprintf('[%d/%d] UPD  #%d "%s" (+%d ch) %dms',
                    $n, $total, $res['manga_id'], $res['name'] ?? '', $res['new_chapters'] ?? 0, $ms), 'cyan');
            } else {
                $created++;
                CLI::write(sprintf('[%d/%d] NEW  #%d "%s" (%d ch, %d genres) %dms',
                    $n, $total, $res['manga_id'], $res['name'] ?? '',
                    $res['chapter_count'] ?? 0, $res['genre_count'] ?? 0, $ms), 'green');
            }

            if ($sleep > 0) usleep((int) ($sleep * 1_000_000));
        }

        CLI::newLine();
        CLI::write("Done. created={$created} updated={$updated} failed={$failed} total={$total}", 'yellow');
        return EXIT_SUCCESS;
    }
}
