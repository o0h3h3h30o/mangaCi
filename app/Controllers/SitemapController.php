<?php

namespace App\Controllers;

class SitemapController extends BaseController
{
    private function db(): \CodeIgniter\Database\BaseConnection
    {
        return \Config\Database::connect();
    }

    /** Return cached XML or generate + cache */
    private function cached(string $key, int $ttl, callable $generate): \CodeIgniter\HTTP\ResponseInterface
    {
        $cache = \Config\Services::cache();
        $xml = $cache->get($key);
        if (!$xml) {
            $xml = $generate();
            $cache->save($key, $xml, $ttl);
        }
        return $this->response
            ->setHeader('Content-Type', str_contains($key, 'feed') ? 'application/rss+xml; charset=UTF-8' : 'application/xml; charset=UTF-8')
            ->setBody($xml);
    }

    /**
     * GET /sitemap.xml — Sitemap index
     */
    public function index(): \CodeIgniter\HTTP\ResponseInterface
    {
        return $this->cached('sitemap_index', 3600, function () {
            $base = rtrim(site_url(), '/');
            $lastManga = $this->db()->table('manga')
                ->select('update_at')
                ->where('is_public', 1)
                ->orderBy('update_at', 'DESC')
                ->limit(1)
                ->get()->getRowArray();

            $lastmod = $lastManga ? date('c', strtotime($lastManga['update_at'])) : date('c');

            $xml  = '<?xml version="1.0" encoding="UTF-8"?>' . "\n";
            $xml .= '<sitemapindex xmlns="http://www.sitemaps.org/schemas/sitemap/0.9">' . "\n";
            $xml .= "  <sitemap>\n";
            $xml .= "    <loc>{$base}/sitemap-manga.xml</loc>\n";
            $xml .= "    <lastmod>{$lastmod}</lastmod>\n";
            $xml .= "  </sitemap>\n";
            $xml .= "  <sitemap>\n";
            $xml .= "    <loc>{$base}/sitemap-chapters.xml</loc>\n";
            $xml .= "    <lastmod>{$lastmod}</lastmod>\n";
            $xml .= "  </sitemap>\n";
            $xml .= '</sitemapindex>';
            return $xml;
        });
    }

    /**
     * GET /sitemap-manga.xml — Tất cả manga public
     */
    public function manga(): \CodeIgniter\HTTP\ResponseInterface
    {
        return $this->cached('sitemap_manga', 3600, function () {
            $base = rtrim(site_url(), '/');
            $rows = $this->db()->table('manga')
                ->select('slug, update_at')
                ->where('is_public', 1)
                ->orderBy('update_at', 'DESC')
                ->get()->getResultArray();

            $xml  = '<?xml version="1.0" encoding="UTF-8"?>' . "\n";
            $xml .= '<urlset xmlns="http://www.sitemaps.org/schemas/sitemap/0.9">' . "\n";

            $xml .= "  <url>\n";
            $xml .= "    <loc>{$base}/</loc>\n";
            $xml .= "    <changefreq>daily</changefreq>\n";
            $xml .= "    <priority>1.0</priority>\n";
            $xml .= "  </url>\n";

            foreach ($rows as $m) {
                $loc     = $base . '/manga/' . htmlspecialchars($m['slug'], ENT_XML1);
                $lastmod = $m['update_at'] ? date('c', strtotime($m['update_at'])) : '';
                $xml .= "  <url>\n";
                $xml .= "    <loc>{$loc}</loc>\n";
                if ($lastmod) {
                    $xml .= "    <lastmod>{$lastmod}</lastmod>\n";
                }
                $xml .= "    <changefreq>weekly</changefreq>\n";
                $xml .= "    <priority>0.8</priority>\n";
                $xml .= "  </url>\n";
            }

            $xml .= '</urlset>';
            return $xml;
        });
    }

    /**
     * GET /sitemap-chapters.xml — Tất cả chapter visible
     */
    public function chapters(): \CodeIgniter\HTTP\ResponseInterface
    {
        return $this->cached('sitemap_chapters', 3600, function () {
            $base = rtrim(site_url(), '/');
            $rows = $this->db()->table('chapter c')
                ->select('m.slug AS manga_slug, c.slug AS chapter_slug, m.update_at')
                ->join('manga m', 'c.manga_id = m.id')
                ->where('c.is_show', 1)
                ->where('m.is_public', 1)
                ->orderBy('m.update_at', 'DESC')
                ->get()->getResultArray();

            $xml  = '<?xml version="1.0" encoding="UTF-8"?>' . "\n";
            $xml .= '<urlset xmlns="http://www.sitemaps.org/schemas/sitemap/0.9">' . "\n";

            foreach ($rows as $r) {
                $loc     = $base . '/manga/' . htmlspecialchars($r['manga_slug'], ENT_XML1)
                         . '/' . htmlspecialchars($r['chapter_slug'], ENT_XML1);
                $lastmod = $r['update_at'] ? date('c', strtotime($r['update_at'])) : '';
                $xml .= "  <url>\n";
                $xml .= "    <loc>{$loc}</loc>\n";
                if ($lastmod) {
                    $xml .= "    <lastmod>{$lastmod}</lastmod>\n";
                }
                $xml .= "    <changefreq>monthly</changefreq>\n";
                $xml .= "    <priority>0.6</priority>\n";
                $xml .= "  </url>\n";
            }

            $xml .= '</urlset>';
            return $xml;
        });
    }

    /**
     * GET /feed — RSS 2.0 feed
     */
    public function feed(): \CodeIgniter\HTTP\ResponseInterface
    {
        return $this->cached('rss_feed', 1800, function () {
            $base      = rtrim(site_url(), '/');
            $siteTitle = site_setting('site_title', 'MangaHub');
            $siteDesc  = site_setting('meta_description', 'Read manga online');
            $cdnBase   = rtrim(env('CDN_COVER_URL', ''), '/');

            $rows = $this->db()->table('manga m')
                ->select('m.id, m.name, m.slug, m.summary, m.image, m.cover, m.update_at')
                ->where('m.is_public', 1)
                ->orderBy('m.update_at', 'DESC')
                ->limit(50)
                ->get()->getResultArray();

            $lastBuild = !empty($rows) ? date('r', strtotime($rows[0]['update_at'])) : date('r');

            $xml  = '<?xml version="1.0" encoding="UTF-8"?>' . "\n";
            $xml .= '<rss version="2.0" xmlns:atom="http://www.w3.org/2005/Atom" xmlns:media="http://search.yahoo.com/mrss/">' . "\n";
            $xml .= "<channel>\n";
            $xml .= "  <title>" . htmlspecialchars($siteTitle, ENT_XML1) . "</title>\n";
            $xml .= "  <link>{$base}</link>\n";
            $xml .= "  <description>" . htmlspecialchars($siteDesc, ENT_XML1) . "</description>\n";
            $xml .= "  <language>es</language>\n";
            $xml .= "  <lastBuildDate>{$lastBuild}</lastBuildDate>\n";
            $xml .= '  <atom:link href="' . $base . '/feed" rel="self" type="application/rss+xml" />' . "\n";

            foreach ($rows as $m) {
                $link    = $base . '/manga/' . htmlspecialchars($m['slug'], ENT_XML1);
                $title   = htmlspecialchars($m['name'], ENT_XML1);
                $desc    = htmlspecialchars(strip_tags($m['summary'] ?? ''), ENT_XML1);
                $pubDate = $m['update_at'] ? date('r', strtotime($m['update_at'])) : '';
                $cover   = htmlspecialchars(manga_cover_url($m, $cdnBase), ENT_XML1);

                $xml .= "  <item>\n";
                $xml .= "    <title>{$title}</title>\n";
                $xml .= "    <link>{$link}</link>\n";
                $xml .= "    <guid isPermaLink=\"true\">{$link}</guid>\n";
                if ($desc) {
                    $xml .= "    <description>{$desc}</description>\n";
                }
                if ($pubDate) {
                    $xml .= "    <pubDate>{$pubDate}</pubDate>\n";
                }
                if ($cover) {
                    $xml .= "    <media:thumbnail url=\"{$cover}\" />\n";
                }
                $xml .= "  </item>\n";
            }

            $xml .= "</channel>\n";
            $xml .= '</rss>';
            return $xml;
        });
    }
}
