<?php

namespace App\Commands;

use CodeIgniter\CLI\BaseCommand;
use CodeIgniter\CLI\CLI;
use App\Controllers\ImportController;

/**
 * Delete manga (+ chapters + pages + relations) for every CSV row whose
 * `type` column is a Novel.
 *
 * Usage:
 *   php spark import:purge-novels <path-to-csv> [--dry-run]
 *
 *   --dry-run   List what WOULD be deleted without touching the DB.
 *
 * Example:
 *   php spark import:purge-novels public/csv2.csv --dry-run
 *   php spark import:purge-novels public/csv2.csv
 */
class PurgeNovels extends BaseCommand
{
    protected $group       = 'Import';
    protected $name        = 'import:purge-novels';
    protected $description = 'Delete manga whose CSV type is Novel (with chapters & pages).';
    protected $usage       = 'import:purge-novels <path> [--dry-run]';
    protected $arguments   = ['path' => 'Path to the CSV (absolute or relative to project root).'];

    public function run(array $params)
    {
        $path = $params[0] ?? null;
        if (!$path) {
            CLI::error('Missing CSV path. Usage: php spark import:purge-novels <path> [--dry-run]');
            return EXIT_ERROR;
        }
        if (!is_file($path) && is_file(ROOTPATH . ltrim($path, '/'))) {
            $path = ROOTPATH . ltrim($path, '/');
        }
        if (!is_file($path)) {
            CLI::error("File not found: {$path}");
            return EXIT_ERROR;
        }

        $dryRun   = (bool) CLI::getOption('dry-run');
        $importer = new ImportController();

        @set_time_limit(0);

        if ($dryRun) {
            // Just list the Novel rows + whether a matching manga exists.
            $rows = $importer->readCsvRows($path);
            $db   = \Config\Database::connect();
            $n = 0;
            foreach ($rows as $row) {
                if (!$importer->isNovelType((string) ($row['type'] ?? ''))) continue;
                $n++;
                CLI::write(sprintf('[novel] %s  (type="%s")', $row['url'] ?? '', $row['type'] ?? ''), 'yellow');
            }
            CLI::newLine();
            CLI::write("DRY RUN: {$n} Novel rows found. Nothing deleted.", 'yellow');
            return EXIT_SUCCESS;
        }

        $res = $importer->purgeNovelsFromCsv($path);
        if (empty($res['ok'])) {
            CLI::error($res['error'] ?? 'Failed.');
            return EXIT_ERROR;
        }

        foreach ($res['results'] as $r) {
            if (!empty($r['deleted'])) {
                CLI::write(sprintf('DEL  #%d "%s"  %s', $r['manga_id'] ?? 0, $r['name'] ?? '', $r['url']), 'green');
            } else {
                CLI::write(sprintf('SKIP %s  (%s)', $r['url'], $r['reason'] ?? '?'), 'dark_gray');
            }
        }

        CLI::newLine();
        CLI::write(sprintf('Done. novel_rows=%d deleted=%d not_found=%d',
            $res['novel_rows'], $res['deleted'], $res['not_found']), 'yellow');
        return EXIT_SUCCESS;
    }
}
