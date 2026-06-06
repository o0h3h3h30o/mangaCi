<?php

namespace App\Controllers;

use CodeIgniter\Controller;
use CodeIgniter\HTTP\ResponseInterface;
use Config\Database;

/**
 * Manga import API.
 *
 * POST /api/admin/import-manga
 *   Auth: none (open endpoint).
 *   Body (JSON):
 *     { "url": "https://submanhwa.com/serie/conexion-suave",
 *       "html":            "<...>",     // optional: page HTML; skips the
 *                                       //   server fetch (use when the host
 *                                       //   Cloudflare-403s the server IP)
 *       "import_chapters": true,        // default true
 *       "download_cover":  true,        // default true
 *       "is_public":       0 }          // default 0 (draft)
 *
 * Currently knows how to scrape submanhwa.com. Other sources can be added
 * in importFrom*().
 */
class ImportController extends Controller
{
    private const UA = 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/124.0.0.0 Safari/537.36';

    /** Only these hosts (and their subdomains) may be fetched. */
    private const ALLOWED_HOSTS = ['submanhwa.com', 'submanhwa.net'];

    /** Diagnostic string from the most recent httpGet failure. */
    private string $lastFetchError = '';

    /** Proxy config (app/Config/ImportProxy.php). Lazy-loaded. */
    private ?\Config\ImportProxy $proxyCfg = null;

    private function proxyCfg(): \Config\ImportProxy
    {
        return $this->proxyCfg ??= config('ImportProxy');
    }

    /** CORS preflight for POST /api/admin/import-manga. */
    public function importOptions(): ResponseInterface
    {
        return $this->cors($this->response)->setStatusCode(204);
    }

    public function importManga(): ResponseInterface
    {
        // ── Input ─────────────────────────────────────────────────
        $body = $this->request->getJSON(true) ?? [];
        $url  = trim((string) ($body['url'] ?? ''));
        $result = $this->processImport([
            'url'             => $url,
            'html'            => (string) ($body['html'] ?? ''),
            'import_chapters' => (bool) ($body['import_chapters'] ?? true),
            'download_cover'  => (bool) ($body['download_cover']  ?? true),
            'is_public'       => (int)  ($body['is_public']       ?? 0),
        ]);

        return $this->json($result, $result['ok'] ? 200 : ($result['status'] ?? 400));
    }

    /**
     * Bulk import from an uploaded CSV.
     *
     * POST /api/admin/import-csv  (multipart/form-data)
     *   file:            the CSV (field name "file" or "csv")
     *   import_chapters: 1/0  (default 1)
     *   download_cover:  1/0  (default 1)
     *   is_public:       1/0  (default 0)
     *
     * CSV format: one manga per row. The URL is taken from a column named
     * "url" (case-insensitive) if a header row exists, otherwise from the
     * first column. Blank lines and a leading header are skipped.
     */
    public function importCsv(): ResponseInterface
    {
        $file = $this->request->getFile('file') ?? $this->request->getFile('csv');
        if (!$file || !$file->isValid()) {
            return $this->json(['ok' => false, 'error' => 'Upload a CSV file in the `file` field.'], 400);
        }

        $rows = $this->readCsvUrls($file->getTempName());
        if (empty($rows)) {
            return $this->json(['ok' => false, 'error' => 'No URLs found in the CSV.'], 422);
        }

        $importChapters = $this->boolParam('import_chapters', true);
        $downloadCover  = $this->boolParam('download_cover', true);
        $isPublic       = $this->boolParam('is_public', false) ? 1 : 0;

        // Avoid PHP timing out on big lists.
        @set_time_limit(0);

        $results = [];
        $created = 0; $updated = 0; $failed = 0;
        foreach ($rows as $u) {
            try {
                $res = $this->processImport([
                    'url'             => $u,
                    'import_chapters' => $importChapters,
                    'download_cover'  => $downloadCover,
                    'is_public'       => $isPublic,
                ]);
            } catch (\Throwable $e) {
                $res = ['ok' => false, 'error' => $e->getMessage()];
                log_message('error', "import-csv row ({$u}) threw: " . $e->getMessage());
            }
            if (!$res['ok'])                      $failed++;
            elseif (!empty($res['already_exists'])) $updated++;
            else                                    $created++;

            $results[] = [
                'url'           => $u,
                'ok'            => $res['ok'],
                'manga_id'      => $res['manga_id']      ?? null,
                'name'          => $res['name']          ?? null,
                'already_exists'=> $res['already_exists'] ?? false,
                'chapter_count' => $res['chapter_count'] ?? ($res['new_chapters'] ?? 0),
                'error'         => $res['error']         ?? null,
            ];
        }

        return $this->json([
            'ok'      => true,
            'total'   => count($rows),
            'created' => $created,
            'updated' => $updated,
            'failed'  => $failed,
            'results' => $results,
        ]);
    }

    /**
     * Core import for a single URL. Returns a plain result array
     * (with an 'ok' flag and, on failure, 'error' + 'status'). Used by
     * the single-URL JSON endpoint, the CSV bulk importer and the
     * `import:csv` spark command. Self-contained (no $this->request/
     * response), so it is safe to call from CLI.
     */
    public function processImport(array $opts): array
    {
        $url = trim((string) ($opts['url'] ?? ''));
        if ($url === '' || !filter_var($url, FILTER_VALIDATE_URL)) {
            return ['ok' => false, 'status' => 400, 'error' => 'A valid `url` is required.'];
        }
        if (!$this->isAllowedHost($url)) {
            return ['ok' => false, 'status' => 400, 'error' => 'Only submanhwa.com URLs are allowed.'];
        }

        $importChapters = (bool) ($opts['import_chapters'] ?? true);
        $downloadCover  = (bool) ($opts['download_cover']  ?? true);
        $isPublic       = (int)  ($opts['is_public']       ?? 0);
        $html           = (string) ($opts['html'] ?? '');

        try {
            $data = $this->scrapeSubmanhwa($url, $html !== '' ? $html : null);
        } catch (\Throwable $e) {
            log_message('error', 'Import scrape failed: ' . $e->getMessage());
            return ['ok' => false, 'status' => 500, 'error' => 'Scrape failed: ' . $e->getMessage()];
        }

        if (empty($data['name'])) {
            return ['ok' => false, 'status' => 422, 'error' => 'Could not extract manga name from page.'];
        }

        $db = Database::connect();
        $sourceSlug = $this->slugify($data['slug'] ?? $data['name']);

        // De-dup by name / slug / source URL.
        $existing = $this->findDuplicate($db, $data['name'], $sourceSlug, $url);
        if ($existing) {
            $existingId = (int) $existing['id'];

            $merged = $this->appendSource($existing['from_manga18fx'] ?? '', $url);
            if ($merged !== ($existing['from_manga18fx'] ?? '')) {
                $db->table('manga')->where('id', $existingId)->update(['from_manga18fx' => $merged]);
            }

            $newChapters = 0;
            if ($importChapters && !empty($data['chapters'])) {
                $newChapters = $this->insertChapters($db, $existingId, $data['chapters']);
                if ($newChapters > 0) {
                    $db->table('manga')->where('id', $existingId)->update(['update_at' => date('Y-m-d H:i:s')]);
                }
            }

            return [
                'ok'             => true,
                'already_exists' => true,
                'duplicate_of'   => $existing['match_field'],
                'manga_id'       => $existingId,
                'slug'           => $existing['slug'],
                'name'           => $existing['name'],
                'from_manga18fx' => $merged,
                'new_chapters'   => $newChapters,
                'edit_url'       => '/admin/manga/' . $existingId . '/edit',
            ];
        }

        $slug     = $this->uniqueSlug($db, $sourceSlug);
        $statusId = $this->mapStatus($db, $data['status_raw'] ?? '');
        $typeId   = $this->mapType($db, $data['type_raw'] ?? '');

        $db->table('manga')->insert([
            'name'           => $data['name'],
            'slug'           => $slug,
            'otherNames'     => $data['other_names'] ?? '',
            'summary'        => $data['summary'] ?? '',
            'status_id'      => $statusId,
            'type_id'        => $typeId,
            'is_public'      => $isPublic ? 1 : 0,
            'caution'        => 0,
            'from_manga18fx' => $url,
            'cover'          => 0,
            'image'          => $downloadCover ? '' : ($data['cover_url'] ?? ''),
            'views'          => 0, 'view_day' => 0, 'view_month' => 0,
            'update_at'      => date('Y-m-d H:i:s'),
        ]);
        $mangaId = (int) $db->insertID();
        if ($mangaId <= 0) {
            return ['ok' => false, 'status' => 500, 'error' => 'Insert failed.'];
        }

        // Cover
        $coverSaved = false;
        if ($downloadCover && !empty($data['cover_url'])) {
            $coverSaved = $this->saveCover($mangaId, $data['cover_url']);
            $db->table('manga')->where('id', $mangaId)
               ->update(['image' => $coverSaved ? '' : $data['cover_url']]);
        }

        // Genres (categories)
        $catIds = [];
        foreach (($data['genres'] ?? []) as $genreName) {
            $genreName = trim((string) $genreName);
            if ($genreName === '') continue;
            $cId = $this->findOrCreateCategory($db, $genreName);
            if ($cId > 0) {
                try {
                    $db->table('category_manga')->insert(['manga_id' => $mangaId, 'category_id' => $cId]);
                    $catIds[] = $cId;
                } catch (\Throwable $e) { /* duplicate, skip */ }
            }
        }

        // Chapters
        $chapterCount = 0;
        if ($importChapters && !empty($data['chapters'])) {
            $chapterCount = $this->insertChapters($db, $mangaId, $data['chapters']);
            if ($chapterCount > 0) {
                $db->table('manga')->where('id', $mangaId)->update(['update_at' => date('Y-m-d H:i:s')]);
            }
        }

        return [
            'ok'           => true,
            'manga_id'     => $mangaId,
            'slug'         => $slug,
            'name'         => $data['name'],
            'status_id'    => $statusId,
            'type_id'      => $typeId,
            'genre_count'  => count($catIds),
            'cover_saved'  => $coverSaved,
            'chapter_count'=> $chapterCount,
            'edit_url'     => '/admin/manga/' . $mangaId . '/edit',
        ];
    }

    /** Read URLs from a CSV: "url" column if header present, else first column. */
    public function readCsvUrls(string $path): array
    {
        $urls = [];
        if (($fh = @fopen($path, 'r')) === false) return $urls;

        $urlCol = 0;
        $first  = true;
        while (($cols = fgetcsv($fh, 0, ',', '"', '')) !== false) {
            if ($cols === [null] || $cols === false) continue; // blank line
            // Detect header row + locate a "url" column.
            if ($first) {
                $first = false;
                $lower = array_map(fn($c) => strtolower(trim((string) $c)), $cols);
                $idx = array_search('url', $lower, true);
                if ($idx !== false) {
                    $urlCol = (int) $idx;
                    continue; // skip header
                }
                // No header — fall through and treat this row as data.
            }
            $candidate = trim((string) ($cols[$urlCol] ?? ''));
            if ($candidate === '') continue;
            if (filter_var($candidate, FILTER_VALIDATE_URL)) {
                $urls[] = $candidate;
            }
        }
        fclose($fh);
        return array_values(array_unique($urls));
    }

    private function boolParam(string $name, bool $default): bool
    {
        $v = $this->request->getPost($name);
        if ($v === null) return $default;
        return filter_var($v, FILTER_VALIDATE_BOOLEAN);
    }

    /**
     * Read full CSV rows keyed by header name (lowercased). Requires a
     * header row. Returns e.g. [['title'=>..,'url'=>..,'type'=>..,'status'=>..], ...].
     */
    public function readCsvRows(string $path): array
    {
        $rows = [];
        if (($fh = @fopen($path, 'r')) === false) return $rows;

        $header = null;
        while (($cols = fgetcsv($fh, 0, ',', '"', '')) !== false) {
            if ($cols === [null] || $cols === false) continue;
            if ($header === null) {
                $header = array_map(fn($c) => strtolower(trim((string) $c)), $cols);
                continue;
            }
            $row = [];
            foreach ($header as $i => $key) {
                $row[$key] = trim((string) ($cols[$i] ?? ''));
            }
            $rows[] = $row;
        }
        fclose($fh);
        return $rows;
    }

    /**
     * Purge manga (and their chapters + pages + relations) for every CSV
     * row whose `type` indicates a Novel. Matches manga by source URL
     * (from_manga18fx) or by slug derived from the URL.
     *
     * Returns a summary array with per-row outcomes.
     */
    public function purgeNovelsFromCsv(string $path): array
    {
        $rows = $this->readCsvRows($path);
        if (empty($rows)) {
            return ['ok' => false, 'error' => 'No rows (CSV needs a header row with url/type columns).'];
        }

        $db = Database::connect();
        $deleted = 0; $notFound = 0; $scanned = 0;
        $results = [];

        foreach ($rows as $row) {
            $type = (string) ($row['type'] ?? '');
            $url  = (string) ($row['url'] ?? '');
            if (!$this->isNovelType($type)) continue;   // only Novel rows
            $scanned++;

            $manga = $this->findMangaByUrl($db, $url);
            if (!$manga) {
                $notFound++;
                $results[] = ['url' => $url, 'type' => $type, 'deleted' => false, 'reason' => 'not found'];
                continue;
            }

            try {
                $this->deleteMangaCascade($db, (int) $manga['id']);
                $deleted++;
                $results[] = ['url' => $url, 'type' => $type, 'deleted' => true,
                              'manga_id' => (int) $manga['id'], 'name' => $manga['name']];
            } catch (\Throwable $e) {
                $results[] = ['url' => $url, 'type' => $type, 'deleted' => false,
                              'manga_id' => (int) $manga['id'], 'reason' => $e->getMessage()];
                log_message('error', 'purgeNovels delete failed #' . $manga['id'] . ': ' . $e->getMessage());
            }
        }

        return [
            'ok'         => true,
            'novel_rows' => $scanned,
            'deleted'    => $deleted,
            'not_found'  => $notFound,
            'results'    => $results,
        ];
    }

    /** True if a CSV type cell denotes a novel (Novel / Novela / Light Novel …). */
    public function isNovelType(string $type): bool
    {
        return (bool) preg_match('/\bnovel/i', $type) || (bool) preg_match('/novela/i', $type);
    }

    /** Locate a manga by its source URL (from_manga18fx token) or slug. */
    private function findMangaByUrl($db, string $url): ?array
    {
        $url = trim($url);
        if ($url === '') return null;

        // 1. Slug from URL path (/serie/<slug>)
        $slug = '';
        if (preg_match('#/serie/([^/?#]+)#', parse_url($url, PHP_URL_PATH) ?? '', $m)) {
            $slug = $this->slugify($m[1]);
        }
        if ($slug !== '') {
            $row = $db->table('manga')->select('id, name, slug, from_manga18fx')
                ->where('slug', $slug)->limit(1)->get()->getRowArray();
            if ($row) return $row;
        }

        // 2. from_manga18fx contains the URL as a comma-separated token.
        $candidates = $db->table('manga')->select('id, name, slug, from_manga18fx')
            ->like('from_manga18fx', $url, 'both', null, true)
            ->limit(10)->get()->getResultArray();
        foreach ($candidates as $cand) {
            $parts = array_map('trim', explode(',', (string) ($cand['from_manga18fx'] ?? '')));
            if (in_array($url, $parts, true)) return $cand;
        }
        return null;
    }

    /** Delete a manga and all dependent rows (pages, chapters, relations …). */
    private function deleteMangaCascade($db, int $id): void
    {
        $chapterIds = array_column(
            $db->query('SELECT id FROM chapter WHERE manga_id = ?', [$id])->getResultArray(),
            'id'
        );

        // Pages of all chapters
        if ($chapterIds) {
            $in = implode(',', array_map('intval', $chapterIds));
            $db->query("DELETE FROM page WHERE chapter_id IN ({$in})");
        }

        // Chapters
        $db->table('chapter')->where('manga_id', $id)->delete();

        // Relations — only the junction/link rows are removed; the shared
        // author / category records themselves are kept.
        foreach ([
            ['category_manga', 'manga_id'],
            ['author_manga',   'manga_id'],
            ['manga_tag',      'manga_id'],
            ['bookmarks',      'manga_id'],
            ['comments',       'manga_id'],
            ['notifications',  'manga_id'],
        ] as [$table, $col]) {
            try { $db->table($table)->where($col, $id)->delete(); } catch (\Throwable $e) {}
        }
        try { $db->table('item_ratings')->where('item_id', $id)->delete(); } catch (\Throwable $e) {}
        try { $db->table('content_likes')->where('content_type', 'manga')->where('content_id', $id)->delete(); } catch (\Throwable $e) {}
        if ($chapterIds) {
            try { $db->table('content_likes')->where('content_type', 'chapter')->whereIn('content_id', $chapterIds)->delete(); } catch (\Throwable $e) {}
            try { $db->table('chapter_reports')->whereIn('chapter_id', $chapterIds)->delete(); } catch (\Throwable $e) {}
        }

        // Manga
        $db->table('manga')->where('id', $id)->delete();
    }

    // ── Source scrapers ──────────────────────────────────────────

    private function scrapeSubmanhwa(string $url, ?string $html = null): array
    {
        if ($html === null) {
            $html = $this->httpGet($url);
        }
        if ($html === null || trim($html) === '') {
            throw new \RuntimeException('Could not fetch page'
                . ($this->lastFetchError !== '' ? ' (' . $this->lastFetchError . ')' : '')
                . '. Tip: pass the page HTML in the `html` field to bypass Cloudflare.');
        }

        $doc = new \DOMDocument();
        $prev = libxml_use_internal_errors(true);
        $doc->loadHTML('<?xml encoding="UTF-8">' . $html);
        libxml_clear_errors();
        libxml_use_internal_errors($prev);
        $xp = new \DOMXPath($doc);

        $first = function (string $query, ?\DOMNode $ctx = null) use ($xp): ?\DOMElement {
            $r = $xp->query($query, $ctx);
            return ($r && $r->length > 0) ? $r->item(0) : null;
        };
        $text = fn(?\DOMElement $el) => $el ? trim(preg_replace('/\s+/u', ' ', $el->textContent)) : '';

        // Title
        $name = $text($first('//h1[contains(@class,"manga-title-centered")]'));

        // Slug from URL path: /serie/<slug>
        $slugFromUrl = '';
        $path = parse_url($url, PHP_URL_PATH) ?? '';
        if (preg_match('#/serie/([^/]+)#', $path, $m)) $slugFromUrl = $m[1];

        // Cover
        $coverUrl = '';
        $img = $first('//img[contains(@class,"img-responsive")]');
        if ($img) {
            $src = $img->getAttribute('src');
            if ($src) $coverUrl = strtok($src, '?') ?: $src; // strip ?cachebust
        }

        // Summary
        $summary = $text($first('//h5[contains(.,"Resumen")]/following-sibling::p[1]'));

        // detail-row scraper helper
        $detail = function (string $label) use ($xp, $text): string {
            $node = $xp->query('//div[contains(@class,"detail-row")][.//div[contains(@class,"detail-label") and normalize-space(.)="' . $label . '"]]/div[contains(@class,"detail-value")]');
            return ($node && $node->length > 0) ? $text($node->item(0)) : '';
        };
        $typeRaw   = $detail('Type');
        $statusRaw = $detail('Estado');

        // Genres (submanhwa renders them as "tag-pill" links)
        $genres = [];
        foreach ($xp->query('//a[contains(@class,"tag-pill")]') as $a) {
            $t = trim($a->textContent);
            if ($t !== '') $genres[] = $t;
        }

        // Chapters
        $chapters = [];
        foreach ($xp->query('//div[contains(@class,"chapter-card-item")]') as $card) {
            $a = $first('.//a[contains(@class,"chapter-link")]', $card);
            if (!$a) continue;
            $href  = $a->getAttribute('href');
            $label = trim(preg_replace('/\s+/u', ' ', $a->textContent)); // "Capítulo 1 :"
            // Extract number from "Capítulo 1.5" / "Capítulo 12 :"
            $number = null;
            if (preg_match('/Cap[ií]tulo\s+([\d.]+)/iu', $label, $m)) {
                $number = (float) $m[1];
            } elseif (preg_match('#/(\d+(?:\.\d+)?)(?:[/?]|$)#', $href, $m)) {
                $number = (float) $m[1];
            }
            // Title = text after the ":" if present. If empty, fall back to
            // the full label ("Capítulo 1") so the chapter always has a name.
            $title = '';
            if (str_contains($label, ':')) {
                $title = trim(substr($label, strpos($label, ':') + 1), " :\t\n\r");
            }
            if ($title === '') $title = trim(rtrim($label, ' :'));

            // Created date — the time span inside the card, e.g. "04 Jun. 2026"
            $dateEl  = $first('.//span[i[contains(@class,"glyphicon-time")]]', $card)
                    ?: $first('.//i[contains(@class,"glyphicon-time")]/..', $card);
            $dateRaw = $dateEl ? trim(preg_replace('/\s+/u', ' ', $dateEl->textContent)) : '';
            $created = $this->parseSpanishDate($dateRaw);

            $chapters[] = [
                'number'     => $number,
                'name'       => $title,
                'url'        => $href,
                'created_at' => $created,   // 'Y-m-d H:i:s' or null
            ];
        }

        return [
            'name'        => $name,
            'slug'        => $slugFromUrl,
            'summary'     => $summary,
            'cover_url'   => $coverUrl,
            'status_raw'  => $statusRaw,
            'type_raw'    => $typeRaw,
            'genres'      => array_values(array_unique($genres)),
            'chapters'    => $chapters,
        ];
    }

    /**
     * Parse a Spanish short date like "04 Jun. 2026" into 'Y-m-d H:i:s'.
     * Returns null if it can't be parsed.
     */
    private function parseSpanishDate(string $raw): ?string
    {
        $raw = mb_strtolower(trim($raw));
        if ($raw === '') return null;

        $months = [
            'ene' => 1, 'feb' => 2, 'mar' => 3, 'abr' => 4, 'may' => 5, 'jun' => 6,
            'jul' => 7, 'ago' => 8, 'sep' => 9, 'set' => 9, 'oct' => 10, 'nov' => 11, 'dic' => 12,
        ];

        // "04 jun. 2026" / "4 jun 2026"
        if (preg_match('/(\d{1,2})\s+([a-záéíóú]{3,})\.?\s+(\d{4})/u', $raw, $m)) {
            $day = (int) $m[1];
            $mon = $months[substr($m[2], 0, 3)] ?? null;
            $yr  = (int) $m[3];
            if ($mon && $day >= 1 && $day <= 31) {
                return sprintf('%04d-%02d-%02d 00:00:00', $yr, $mon, $day);
            }
        }
        // ISO fallback "2026-06-04"
        if (preg_match('/(\d{4})-(\d{2})-(\d{2})/', $raw, $m)) {
            return "{$m[1]}-{$m[2]}-{$m[3]} 00:00:00";
        }
        return null;
    }

    // ── Insert helpers ───────────────────────────────────────────

    private function mapStatus($db, string $raw): int
    {
        $raw = mb_strtolower(trim($raw));
        if ($raw === '') return 1;

        // Spanish, English, common variants → canonical bucket
        $bucket = 'ongoing';
        if (preg_match('/(curso|ongoing|publicandose|publicando)/u', $raw))       $bucket = 'ongoing';
        elseif (preg_match('/(finaliz|completad|complet|completed|ended)/u', $raw)) $bucket = 'completed';
        elseif (preg_match('/(pausa|hiatus|hiato)/u', $raw))                       $bucket = 'hiatus';
        elseif (preg_match('/(cancel)/u', $raw))                                   $bucket = 'cancelled';

        try {
            $rows = $db->table('status')->get()->getResultArray();
        } catch (\Throwable $e) {
            return 1;
        }
        if (empty($rows)) return 1;

        $needle = [
            'ongoing'   => ['ongoing', 'curso', 'publicando'],
            'completed' => ['completed', 'finalizado', 'completado'],
            'hiatus'    => ['hiatus', 'pausa', 'hiato'],
            'cancelled' => ['cancelled', 'canceled', 'cancelado'],
        ][$bucket];

        foreach ($rows as $s) {
            $sName = mb_strtolower($s['name'] ?? '');
            foreach ($needle as $n) {
                if ($sName !== '' && str_contains($sName, $n)) return (int) $s['id'];
            }
        }
        return (int) ($rows[0]['id'] ?? 1);
    }

    private function mapType($db, string $raw): ?int
    {
        $raw = mb_strtoupper(trim($raw));
        if ($raw === '') return null;
        try {
            $rows = $db->table('comictype')->get()->getResultArray();
        } catch (\Throwable $e) {
            return null;
        }
        foreach ($rows as $t) {
            if (mb_strtoupper($t['name'] ?? '') === $raw) return (int) $t['id'];
        }
        return null;
    }

    private function findOrCreateCategory($db, string $name): int
    {
        $name = trim($name);
        if ($name === '') return 0;

        // Match case-insensitively to avoid duplicate genres differing only
        // in casing ("Smut" vs "smut").
        $existing = $db->table('category')
            ->where('LOWER(name)', mb_strtolower($name))
            ->get()->getRowArray();
        if ($existing) return (int) $existing['id'];

        // Build a slug; if another category already owns it (e.g. "+18" and
        // "18" both fold to "18"), reuse that one rather than inserting a
        // colliding/empty slug (which would hit category_slug_unique).
        $slug = $this->slugify($name);
        $bySlug = $db->table('category')->where('slug', $slug)->get()->getRowArray();
        if ($bySlug) return (int) $bySlug['id'];

        // Ensure uniqueness against the slug column before inserting.
        $base = $slug; $i = 2;
        while ($db->table('category')->where('slug', $slug)->countAllResults() > 0) {
            $slug = $base . '-' . $i++;
            if ($i > 50) { $slug = $base . '-' . substr(md5(uniqid('', true)), 0, 6); break; }
        }

        try {
            $db->table('category')->insert(['name' => $name, 'slug' => $slug]);
            return (int) $db->insertID();
        } catch (\Throwable $e) {
            // Race / re-query: another request may have created it meanwhile.
            $again = $db->table('category')
                ->groupStart()->where('LOWER(name)', mb_strtolower($name))->orWhere('slug', $slug)->groupEnd()
                ->get()->getRowArray();
            if ($again) return (int) $again['id'];
            log_message('error', 'findOrCreateCategory failed for "' . $name . '": ' . $e->getMessage());
            return 0;
        }
    }

    private function insertChapters($db, int $mangaId, array $chapters): int
    {
        $count = 0;
        // De-dup by number (last wins)
        $byNum = [];
        foreach ($chapters as $c) {
            if (!isset($c['number']) || $c['number'] === null) continue;
            $byNum[(string) $c['number']] = $c;
        }
        ksort($byNum, SORT_NATURAL);

        foreach ($byNum as $c) {
            $number = (float) $c['number'];
            $name   = (string) ($c['name'] ?? '');
            $slug   = $name ? $this->slugify($name) : ('chapter-' . rtrim(rtrim(number_format($number, 2, '.', ''), '0'), '.'));

            // Avoid duplicate (manga_id + number)
            $exists = $db->table('chapter')->where('manga_id', $mangaId)->where('number', $number)->countAllResults() > 0;
            if ($exists) continue;

            $insert = [
                'manga_id'    => $mangaId,
                'number'      => $number,
                'slug'        => $slug,
                'name'        => $name,
                'is_show'     => 0,            // draft — admin reviews before publishing
                'is_crawling' => 0,
                'source_url'  => $c['url'] ?? '',
                'view'        => 0,
            ];
            if (!empty($c['created_at'])) {
                $insert['created_at'] = $c['created_at'];
            }

            try {
                $db->table('chapter')->insert($insert);
                $count++;
            } catch (\Throwable $e) {
                log_message('error', 'Chapter insert failed: ' . $e->getMessage());
            }
        }
        return $count;
    }

    private function saveCover(int $mangaId, string $coverUrl): bool
    {
        $bytes = $this->httpGet($coverUrl, true);
        if ($bytes === null || strlen($bytes) < 100) return false;

        $dir = rtrim(env('COVER_SAVE_DIR', FCPATH . 'cover'), '/') . '/';
        if (!is_dir($dir)) @mkdir($dir, 0755, true);

        $finfo = new \finfo(FILEINFO_MIME_TYPE);
        $mime  = $finfo->buffer($bytes);
        $exts  = ['image/jpeg' => 'jpg', 'image/png' => 'png', 'image/webp' => 'webp', 'image/gif' => 'gif'];
        $ext   = $exts[$mime] ?? null;
        if (!$ext) return false;

        $full  = $dir . $mangaId . '.' . $ext;
        $thumb = $dir . $mangaId . '-thumb.' . $ext;
        if (file_put_contents($full, $bytes) === false) return false;

        // Build thumb (reuses Admin::createThumb logic in-line)
        $info = @getimagesize($full);
        if ($info) {
            [$w, $h] = $info;
            $maxW = 300; $maxH = 450;
            $ratio = min($maxW / max(1, $w), $maxH / max(1, $h), 1);
            $nw = max(1, (int) round($w * $ratio));
            $nh = max(1, (int) round($h * $ratio));

            $srcImg = match ($info[2]) {
                IMAGETYPE_JPEG => @imagecreatefromjpeg($full),
                IMAGETYPE_PNG  => @imagecreatefrompng($full),
                IMAGETYPE_GIF  => @imagecreatefromgif($full),
                IMAGETYPE_WEBP => function_exists('imagecreatefromwebp') ? @imagecreatefromwebp($full) : null,
                default        => null,
            };
            if ($srcImg) {
                $dst = imagecreatetruecolor($nw, $nh);
                imagecopyresampled($dst, $srcImg, 0, 0, 0, 0, $nw, $nh, $w, $h);
                match ($ext) {
                    'png'  => imagepng($dst, $thumb, 6),
                    'gif'  => imagegif($dst, $thumb),
                    'webp' => function_exists('imagewebp') ? imagewebp($dst, $thumb, 80) : imagejpeg($dst, $thumb, 80),
                    default=> imagejpeg($dst, $thumb, 80),
                };
                imagedestroy($srcImg); imagedestroy($dst);
            } else {
                @copy($full, $thumb);
            }
        } else {
            @copy($full, $thumb);
        }

        return true;
    }

    // ── HTTP fetcher ─────────────────────────────────────────────

    /** True if URL host is one of ALLOWED_HOSTS or a subdomain thereof. */
    private function isAllowedHost(string $url): bool
    {
        $host = strtolower(parse_url($url, PHP_URL_HOST) ?? '');
        if ($host === '') return false;
        foreach (self::ALLOWED_HOSTS as $allowed) {
            if ($host === $allowed || str_ends_with($host, '.' . $allowed)) {
                return true;
            }
        }
        return false;
    }

    private function httpGet(string $url, bool $binary = false): ?string
    {
        // SSRF guard: never fetch a host outside the whitelist.
        if (!$this->isAllowedHost($url)) {
            log_message('error', "httpGet blocked non-whitelisted host: {$url}");
            return null;
        }

        $this->lastFetchError = '';

        // Build the proxy attempt list: a shuffled subset of the pool.
        // Empty string = direct connection (used as a final fallback when
        // proxies are disabled or all fail).
        $proxies = [];
        if ($this->proxyEnabled()) {
            $pool = $this->proxyCfg()->hosts;
            shuffle($pool);
            $proxies = array_slice($pool, 0, max(1, $this->proxyCfg()->attempts));
        }
        $attempts = $proxies;
        $attempts[] = '';  // always try direct last

        $lastErr = 'no attempts made';
        foreach ($attempts as $proxyHost) {
            [$body, $err] = $this->curlFetch($url, $binary, $proxyHost);
            if ($body !== null) {
                return $body;
            }
            $via = $proxyHost !== '' ? "proxy {$proxyHost}" : 'direct';
            $lastErr = "{$via}: {$err}";
            log_message('warning', "httpGet attempt via {$via} failed for {$url}: {$err}");
        }

        $this->lastFetchError = $lastErr;
        log_message('error', "httpGet {$url} exhausted all attempts: {$lastErr}");
        return null;
    }

    private function proxyUser(): string
    {
        $u = trim((string) env('PROXY_USER', ''));
        return $u !== '' ? $u : $this->proxyCfg()->user;
    }

    private function proxyPass(): string
    {
        $p = trim((string) env('PROXY_PASS', ''));
        return $p !== '' ? $p : $this->proxyCfg()->pass;
    }

    private function proxyEnabled(): bool
    {
        // env override wins; otherwise the config flag.
        $enabled = filter_var(env('PROXY_ENABLED', $this->proxyCfg()->enabled), FILTER_VALIDATE_BOOLEAN);
        if (!$enabled) return false;
        return !empty($this->proxyCfg()->hosts)
            && $this->proxyUser() !== '' && $this->proxyPass() !== '';
    }

    /**
     * Single curl fetch. $proxyHost = '' means direct.
     * Returns [body|null, errorString].
     */
    private function curlFetch(string $url, bool $binary, string $proxyHost): array
    {
        $headers = $binary
            ? [
                'Accept: image/avif,image/webp,image/apng,image/*,*/*;q=0.8',
                'Accept-Language: es-ES,es;q=0.9,en;q=0.8',
                'Referer: https://submanhwa.com/',
            ]
            : [
                'Accept: text/html,application/xhtml+xml,application/xml;q=0.9,image/avif,image/webp,*/*;q=0.8',
                'Accept-Language: es-ES,es;q=0.9,en;q=0.8',
                'Upgrade-Insecure-Requests: 1',
                'Sec-Fetch-Dest: document',
                'Sec-Fetch-Mode: navigate',
                'Sec-Fetch-Site: none',
                'Sec-Fetch-User: ?1',
                'Cache-Control: no-cache',
                'Pragma: no-cache',
            ];

        $ch = curl_init($url);
        $opts = [
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_FOLLOWLOCATION => true,
            CURLOPT_MAXREDIRS      => 5,
            CURLOPT_TIMEOUT        => 30,
            CURLOPT_CONNECTTIMEOUT => 15,
            CURLOPT_USERAGENT      => self::UA,
            CURLOPT_ENCODING       => '',          // accept gzip/deflate/br
            CURLOPT_HTTPHEADER     => $headers,
            CURLOPT_SSL_VERIFYPEER => false,
            CURLOPT_SSL_VERIFYHOST => 0,
        ];

        if ($proxyHost !== '') {
            $opts[CURLOPT_PROXY]           = $proxyHost . ':' . $this->proxyCfg()->port;
            $opts[CURLOPT_PROXYTYPE]       = CURLPROXY_HTTP;
            $opts[CURLOPT_PROXYUSERPWD]    = $this->proxyUser() . ':' . $this->proxyPass();
            $opts[CURLOPT_HTTPPROXYTUNNEL] = true;
        }

        curl_setopt_array($ch, $opts);
        $body  = curl_exec($ch);
        $code  = (int) curl_getinfo($ch, CURLINFO_RESPONSE_CODE);
        $err   = curl_error($ch);
        $errno = curl_errno($ch);
        curl_close($ch);

        if ($body === false || $errno !== 0) {
            return [null, "curl error {$errno}: {$err}"];
        }
        if ($code >= 400) {
            return [null, "HTTP {$code}"];
        }
        if ($body === '' ) {
            return [null, "empty body (HTTP {$code})"];
        }
        return [$body, ''];
    }

    // ── Misc ─────────────────────────────────────────────────────

    private function slugify(string $text): string
    {
        $text = trim($text);
        $text = $this->foldAccents($text);          // í→i, ñ→n, é→e, ü→u …
        $text = mb_strtolower($text);
        $text = preg_replace('/[^a-z0-9\s-]/u', '', $text); // ASCII-only now
        $text = preg_replace('/[\s_]+/', '-', $text);
        $text = preg_replace('/-+/', '-', $text);
        return trim($text, '-') ?: ('manga-' . substr(md5(uniqid('', true)), 0, 8));
    }

    /** Transliterate accented Latin chars to plain ASCII. */
    private function foldAccents(string $text): string
    {
        // Fast path via iconv when available.
        if (function_exists('iconv')) {
            $conv = @iconv('UTF-8', 'ASCII//TRANSLIT//IGNORE', $text);
            if ($conv !== false && $conv !== '') {
                // iconv can emit things like "'e"; strip non-letter residue later.
                return $conv;
            }
        }
        // Fallback map (covers Spanish + common Latin diacritics).
        $map = [
            'á'=>'a','à'=>'a','ä'=>'a','â'=>'a','ã'=>'a','å'=>'a',
            'é'=>'e','è'=>'e','ë'=>'e','ê'=>'e',
            'í'=>'i','ì'=>'i','ï'=>'i','î'=>'i',
            'ó'=>'o','ò'=>'o','ö'=>'o','ô'=>'o','õ'=>'o',
            'ú'=>'u','ù'=>'u','ü'=>'u','û'=>'u',
            'ñ'=>'n','ç'=>'c','ý'=>'y','ÿ'=>'y',
            'Á'=>'A','À'=>'A','Ä'=>'A','Â'=>'A','Ã'=>'A','Å'=>'A',
            'É'=>'E','È'=>'E','Ë'=>'E','Ê'=>'E',
            'Í'=>'I','Ì'=>'I','Ï'=>'I','Î'=>'I',
            'Ó'=>'O','Ò'=>'O','Ö'=>'O','Ô'=>'O','Õ'=>'O',
            'Ú'=>'U','Ù'=>'U','Ü'=>'U','Û'=>'U',
            'Ñ'=>'N','Ç'=>'C',
        ];
        return strtr($text, $map);
    }

    /**
     * Find an existing manga matching by name (case-insensitive, trimmed),
     * by slug, or whose from_manga18fx already contains the source URL.
     * Returns row + which field matched, or null.
     */
    private function findDuplicate($db, string $name, string $slug, string $sourceUrl): ?array
    {
        $name = trim($name);
        $slug = trim($slug);
        $sourceUrl = trim($sourceUrl);

        // 1. Name match (case-insensitive)
        if ($name !== '') {
            $row = $db->table('manga')
                ->select('id, name, slug, from_manga18fx')
                ->where('LOWER(name)', mb_strtolower($name))
                ->limit(1)->get()->getRowArray();
            if ($row) { $row['match_field'] = 'name'; return $row; }
        }

        // 2. Slug match (exact)
        if ($slug !== '') {
            $row = $db->table('manga')
                ->select('id, name, slug, from_manga18fx')
                ->where('slug', $slug)
                ->limit(1)->get()->getRowArray();
            if ($row) { $row['match_field'] = 'slug'; return $row; }
        }

        // 3. from_manga18fx contains this exact URL as a token. We use
        //    FIND_IN_SET on a normalized version of the column so URL
        //    "secret-class" never accidentally matches "secret-class-color".
        //    Normalize handles all our separator styles (",", " ,", ", ").
        if ($sourceUrl !== '') {
            $row = $db->table('manga')
                ->select('id, name, slug, from_manga18fx')
                ->where(
                    "FIND_IN_SET(" . $db->escape($sourceUrl) . ", "
                    . "REPLACE(REPLACE(REPLACE(from_manga18fx, ' , ', ','), ' ,', ','), ', ', ',')"
                    . ") > 0",
                    null, false
                )
                ->limit(1)->get()->getRowArray();
            if ($row) { $row['match_field'] = 'from_manga18fx'; return $row; }
        }

        return null;
    }

    /**
     * Append a new source URL to a comma-separated from_manga18fx list,
     * deduped. Existing entries (which may be slugs/paths from older imports)
     * are preserved as-is.
     */
    private function appendSource(string $current, string $newValue): string
    {
        $newValue = trim($newValue);
        if ($newValue === '') return $current;
        $parts = array_filter(array_map('trim', explode(',', $current)), fn($p) => $p !== '');
        if (!in_array($newValue, $parts, true)) $parts[] = $newValue;
        return implode(' ,', $parts);
    }

    private function uniqueSlug($db, string $slug): string
    {
        $base = $slug; $i = 2;
        while ($db->table('manga')->where('slug', $slug)->countAllResults() > 0) {
            $slug = $base . '-' . $i++;
            if ($i > 50) { $slug = $base . '-' . substr(md5(uniqid('', true)), 0, 6); break; }
        }
        return $slug;
    }

    private function json(array $payload, int $code = 200): ResponseInterface
    {
        return $this->cors($this->response)
            ->setStatusCode($code)
            ->setContentType('application/json')
            ->setBody(json_encode($payload, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES));
    }

    /** Add permissive CORS headers to a response. */
    private function cors(ResponseInterface $response): ResponseInterface
    {
        $origin = $this->request->getHeaderLine('Origin');
        return $response
            ->setHeader('Access-Control-Allow-Origin', $origin !== '' ? $origin : '*')
            ->setHeader('Vary', 'Origin')
            ->setHeader('Access-Control-Allow-Methods', 'POST, OPTIONS')
            ->setHeader('Access-Control-Allow-Headers', 'Content-Type')
            ->setHeader('Access-Control-Max-Age', '86400');
    }
}
