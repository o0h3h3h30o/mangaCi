<?php

namespace App\Controllers;

use CodeIgniter\Controller;
use CodeIgniter\HTTP\ResponseInterface;
use Config\Database;

/**
 * Manga import API.
 *
 * POST /api/admin/import-manga
 *   Headers: X-Api-Key: <key>   (or be logged in as admin)
 *   Body (JSON):
 *     { "url": "https://submanhwa.com/serie/conexion-suave",
 *       "import_chapters": true,        // default true
 *       "download_cover":  true,        // default true
 *       "is_public":       0 }          // default 0 (draft)
 *
 * Currently knows how to scrape submanhwa.com. Other sources can be added
 * in importFrom*().
 */
class ImportController extends Controller
{
    private const UA = 'Mozilla/5.0 (compatible; MangaCI-Importer/1.0)';

    public function importManga(): ResponseInterface
    {
        helper(['site_settings']);

        // ── Auth ──────────────────────────────────────────────────
        if (!$this->authorize()) {
            return $this->json(['ok' => false, 'error' => 'Unauthorized'], 401);
        }

        // ── Input ─────────────────────────────────────────────────
        $body = $this->request->getJSON(true) ?? [];
        $url  = trim((string) ($body['url'] ?? ''));
        if ($url === '' || !filter_var($url, FILTER_VALIDATE_URL)) {
            return $this->json(['ok' => false, 'error' => 'A valid `url` is required.'], 400);
        }

        $importChapters = (bool) ($body['import_chapters'] ?? true);
        $downloadCover  = (bool) ($body['download_cover']  ?? true);
        $isPublic       = (int)  ($body['is_public']       ?? 0);

        // ── Dispatch to source-specific scraper ──────────────────
        $host = strtolower(parse_url($url, PHP_URL_HOST) ?? '');
        try {
            if (str_contains($host, 'submanhwa.com')) {
                $data = $this->scrapeSubmanhwa($url);
            } else {
                return $this->json(['ok' => false, 'error' => "Source `{$host}` is not supported yet."], 400);
            }
        } catch (\Throwable $e) {
            log_message('error', 'Import scrape failed: ' . $e->getMessage());
            return $this->json(['ok' => false, 'error' => 'Scrape failed: ' . $e->getMessage()], 500);
        }

        if (empty($data['name'])) {
            return $this->json(['ok' => false, 'error' => 'Could not extract manga name from page.'], 422);
        }

        // ── Insert ───────────────────────────────────────────────
        $db = Database::connect();
        $sourceSlug = $this->slugify($data['slug'] ?? $data['name']);

        // De-dup: if a manga with the same name OR same source-slug already
        // exists, don't insert again — append the new source URL to its
        // from_manga18fx field and return the existing record.
        $existing = $this->findDuplicate($db, $data['name'], $sourceSlug);
        if ($existing) {
            $merged = $this->appendSource($existing['from_manga18fx'] ?? '', $url);
            if ($merged !== ($existing['from_manga18fx'] ?? '')) {
                $db->table('manga')->where('id', $existing['id'])->update(['from_manga18fx' => $merged]);
            }
            return $this->json([
                'ok'             => true,
                'already_exists' => true,
                'duplicate_of'   => $existing['match_field'],   // "name" or "slug"
                'manga_id'       => (int) $existing['id'],
                'slug'           => $existing['slug'],
                'name'           => $existing['name'],
                'from_manga18fx' => $merged,
                'edit_url'       => '/admin/manga/' . (int) $existing['id'] . '/edit',
            ]);
        }

        $slug = $this->uniqueSlug($db, $sourceSlug);

        $statusId = $this->mapStatus($db, $data['status_raw'] ?? '');
        $typeId   = $this->mapType($db, $data['type_raw'] ?? '');

        $row = [
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
        ];

        $db->table('manga')->insert($row);
        $mangaId = (int) $db->insertID();
        if ($mangaId <= 0) {
            return $this->json(['ok' => false, 'error' => 'Insert failed.'], 500);
        }

        // Cover
        $coverSaved = false;
        if ($downloadCover && !empty($data['cover_url'])) {
            $coverSaved = $this->saveCover($mangaId, $data['cover_url']);
            if ($coverSaved) {
                $db->table('manga')->where('id', $mangaId)->update(['image' => '']);
            } else {
                // fall back to remote URL if download failed
                $db->table('manga')->where('id', $mangaId)->update(['image' => $data['cover_url']]);
            }
        }

        // Tags
        $tagIds = [];
        foreach (($data['tags'] ?? []) as $tagName) {
            $tagName = trim((string) $tagName);
            if ($tagName === '') continue;
            $tId = $this->findOrCreateTag($db, $tagName);
            if ($tId > 0) {
                try {
                    $db->table('manga_tag')->insert(['manga_id' => $mangaId, 'tag_id' => $tId]);
                    $tagIds[] = $tId;
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

        return $this->json([
            'ok'           => true,
            'manga_id'     => $mangaId,
            'slug'         => $slug,
            'name'         => $data['name'],
            'status_id'    => $statusId,
            'type_id'      => $typeId,
            'tag_count'    => count($tagIds),
            'cover_saved'  => $coverSaved,
            'chapter_count'=> $chapterCount,
            'edit_url'     => '/admin/manga/' . $mangaId . '/edit',
        ]);
    }

    // ── Source scrapers ──────────────────────────────────────────

    private function scrapeSubmanhwa(string $url): array
    {
        $html = $this->httpGet($url);
        if ($html === null) throw new \RuntimeException('Could not fetch page.');

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

        // Tags
        $tags = [];
        foreach ($xp->query('//a[contains(@class,"tag-pill")]') as $a) {
            $t = trim($a->textContent);
            if ($t !== '') $tags[] = $t;
        }

        // Chapters
        $chapters = [];
        foreach ($xp->query('//div[contains(@class,"chapter-card-item")]') as $card) {
            $a = $first('.//a[contains(@class,"chapter-link")]', $card);
            if (!$a) continue;
            $href  = $a->getAttribute('href');
            $label = trim($a->textContent);                            // "Capítulo 1 :"
            // Extract number from "Capítulo 1.5" / "Capítulo 12 :"
            $number = null;
            if (preg_match('/Cap[ií]tulo\s+([\d.]+)/iu', $label, $m)) {
                $number = (float) $m[1];
            } elseif (preg_match('#/(\d+(?:\.\d+)?)(?:[/?]|$)#', $href, $m)) {
                $number = (float) $m[1];
            }
            // Name = anything after the ":" if present
            $title = '';
            if (str_contains($label, ':')) {
                $title = trim(substr($label, strpos($label, ':') + 1));
            }
            $chapters[] = [
                'number' => $number,
                'name'   => $title,
                'url'    => $href,
            ];
        }

        return [
            'name'        => $name,
            'slug'        => $slugFromUrl,
            'summary'     => $summary,
            'cover_url'   => $coverUrl,
            'status_raw'  => $statusRaw,
            'type_raw'    => $typeRaw,
            'tags'        => array_values(array_unique($tags)),
            'chapters'    => $chapters,
        ];
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

    private function findOrCreateTag($db, string $name): int
    {
        $existing = $db->table('tag')->where('name', $name)->get()->getRowArray();
        if ($existing) return (int) $existing['id'];
        try {
            $db->table('tag')->insert(['name' => $name, 'slug' => $this->slugify($name)]);
        } catch (\Throwable $e) {
            $db->table('tag')->insert(['name' => $name]);
        }
        return (int) $db->insertID();
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

            try {
                $db->table('chapter')->insert([
                    'manga_id'    => $mangaId,
                    'number'      => $number,
                    'slug'        => $slug,
                    'name'        => $name,
                    'is_show'     => 0,            // draft — admin reviews before publishing
                    'is_crawling' => 0,
                    'source_url'  => $c['url'] ?? '',
                    'view'        => 0,
                ]);
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

    private function httpGet(string $url, bool $binary = false): ?string
    {
        $ch = curl_init($url);
        curl_setopt_array($ch, [
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_FOLLOWLOCATION => true,
            CURLOPT_MAXREDIRS      => 5,
            CURLOPT_TIMEOUT        => 25,
            CURLOPT_CONNECTTIMEOUT => 10,
            CURLOPT_USERAGENT      => self::UA,
            CURLOPT_HTTPHEADER     => [
                'Accept: ' . ($binary ? 'image/*,*/*;q=0.8' : 'text/html,application/xhtml+xml'),
                'Accept-Language: es,en;q=0.8',
            ],
            CURLOPT_SSL_VERIFYPEER => false,
        ]);
        $body = curl_exec($ch);
        $code = curl_getinfo($ch, CURLINFO_RESPONSE_CODE);
        $err  = curl_error($ch);
        curl_close($ch);
        if ($body === false || $code >= 400) {
            log_message('error', "httpGet {$url} failed: HTTP {$code} {$err}");
            return null;
        }
        return $body;
    }

    // ── Misc ─────────────────────────────────────────────────────

    private function slugify(string $text): string
    {
        $text = mb_strtolower(trim($text));
        $text = preg_replace('/[^\p{L}\p{N}\s-]/u', '', $text);
        $text = preg_replace('/[\s]+/', '-', $text);
        return trim($text, '-') ?: ('manga-' . substr(md5(uniqid('', true)), 0, 8));
    }

    /**
     * Find an existing manga that matches by name (case-insensitive, trimmed)
     * or by slug. Returns row + which field matched, or null.
     */
    private function findDuplicate($db, string $name, string $slug): ?array
    {
        $name = trim($name);
        $slug = trim($slug);

        // Name match (case-insensitive)
        if ($name !== '') {
            $row = $db->table('manga')
                ->select('id, name, slug, from_manga18fx')
                ->where('LOWER(name)', mb_strtolower($name))
                ->limit(1)->get()->getRowArray();
            if ($row) { $row['match_field'] = 'name'; return $row; }
        }

        // Slug match (exact, or any "<slug>-<n>" variant produced by uniqueSlug)
        if ($slug !== '') {
            $row = $db->table('manga')
                ->select('id, name, slug, from_manga18fx')
                ->where('slug', $slug)
                ->limit(1)->get()->getRowArray();
            if ($row) { $row['match_field'] = 'slug'; return $row; }
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

    private function authorize(): bool
    {
        // 1) Admin session
        $session = session();
        if ($session->get('isLoggedIn')) {
            $userId = (int) $session->get('user_id');
            if ($userId > 0) {
                try {
                    $isAdmin = Database::connect()
                        ->table('users_groups ug')
                        ->join('groups g', 'g.id = ug.group_id')
                        ->where('ug.user_id', $userId)
                        ->where('g.name', 'admin')
                        ->countAllResults() > 0;
                    if ($isAdmin) return true;
                } catch (\Throwable $e) { /* fall through */ }
            }
        }

        // 2) API key
        $expected = trim((string) site_setting('import_api_key', ''));
        if ($expected === '') return false;
        $provided = (string) ($this->request->getHeaderLine('X-Api-Key')
            ?: $this->request->getGet('api_key')
            ?: '');
        if ($provided === '') return false;
        return hash_equals($expected, $provided);
    }

    private function json(array $payload, int $code = 200): ResponseInterface
    {
        return $this->response
            ->setStatusCode($code)
            ->setContentType('application/json')
            ->setBody(json_encode($payload, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES));
    }
}
