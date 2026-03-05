<?php

if (!function_exists('format_chapter_date')) {
    /**
     * Format chapter date từ datetime string hoặc Unix timestamp.
     * Trả về dạng dd/mm/yy.
     */
    function format_chapter_date(?string $datetime): string
    {
        if (empty($datetime)) {
            return '';
        }
        $ts = is_numeric($datetime) ? (int) $datetime : (strtotime($datetime) ?: 0);
        return $ts > 0 ? date('d/m/y', $ts) : '';
    }
}

if (!function_exists('time_ago')) {
    /**
     * Trả về chuỗi "X ago" từ Unix timestamp hoặc datetime string.
     * Không bao giờ trả về số âm.
     */
    function time_ago(int|string $time): string
    {
        $ts   = is_numeric($time) ? (int) $time : (strtotime((string) $time) ?: time());
        $diff = max(0, time() - $ts);

        if ($diff < 60)         return $diff . 's ago';
        if ($diff < 3600)       return floor($diff / 60) . 'm ago';
        if ($diff < 86400)      return floor($diff / 3600) . 'h ago';
        if ($diff < 604800)     return floor($diff / 86400) . 'd ago';
        return floor($diff / 604800) . 'w ago';
    }
}
