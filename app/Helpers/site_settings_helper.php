<?php

if (!function_exists('manga_cover_url')) {
    /**
     * Trả về URL ảnh bìa manga.
     * - cover == 1 → ảnh đã trên CDN S3, dùng pattern CDN
     * - cover != 1 → dùng trường image (URL tự upload), fallback CDN nếu image rỗng
     */
    function manga_cover_url(array $manga, string $cdnBase = ''): string
    {
        if (!$cdnBase) {
            $cdnBase = rtrim(env('CDN_COVER_URL', ''), '/');
        }
        $cdnUrl = $cdnBase . '/' . ($manga['slug'] ?? '') . '-thumb.jpg';
        if (($manga['cover'] ?? 0) == 1) {
            return $cdnUrl;
        }
        return !empty($manga['image']) ? $manga['image'] : $cdnUrl;
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
