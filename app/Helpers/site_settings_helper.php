<?php

if (!function_exists('manga_cover_url')) {
    /**
     * Trả về URL ảnh bìa manga.
     * - cover == 1 → ảnh đã trên CDN S3, dùng pattern CDN
     * - cover != 1 → dùng trường image (URL tự upload), fallback CDN nếu image rỗng
     */
    function manga_cover_url(array $manga, string $cdnBase = ''): string
    {
        static $cachedCdnBase = null;
        static $cachedCoverDir = null;
        static $coverFileCache = [];

        if ($cachedCdnBase === null) {
            $cachedCdnBase = rtrim(env('CDN_COVER_URL', ''), '/');
        }
        if ($cachedCoverDir === null) {
            $cachedCoverDir = rtrim(env('COVER_SAVE_DIR', FCPATH . 'cover'), '/') . '/';
        }

        $base = $cdnBase ?: $cachedCdnBase;
        $id = $manga['id'] ?? '';
        $cdnUrl = $base . '/' . $id . '-thumb.jpg';

        if (($manga['cover'] ?? 0) == 1) {
            return $cdnUrl;
        }
        if (!empty($manga['image'])) {
            return $manga['image'];
        }

        // Check local cover dir with cache
        if (isset($coverFileCache[$id])) {
            return $coverFileCache[$id];
        }

        foreach (['-thumb', ''] as $suffix) {
            foreach (['jpg', 'png', 'webp'] as $ext) {
                if (is_file($cachedCoverDir . $id . $suffix . '.' . $ext)) {
                    $url = base_url('cover/' . $id . $suffix . '.' . $ext);
                    $coverFileCache[$id] = $url;
                    return $url;
                }
            }
        }

        $coverFileCache[$id] = $cdnUrl;
        return $cdnUrl;
    }
}

if (!function_exists('site_setting')) {
    /**
     * Lấy giá trị setting theo key, có static cache để chỉ query DB 1 lần/request.
     */
    function site_setting(string $key, string $default = ''): string
    {
        static $settings = null;

        if ($settings === null) {
            try {
                $rows = \Config\Database::connect()
                    ->table('site_settings')
                    ->get()
                    ->getResultArray();
                $settings = array_column($rows, 'value', 'key');
            } catch (\Throwable $e) {
                $settings = [];
            }
        }

        return $settings[$key] ?? $default;
    }
}
