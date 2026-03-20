<?php

/**
 * Shortcut for lang() — usage: __('Comixx.login')
 */
if (!function_exists('__')) {
    function __(string $key, array $params = []): string
    {
        return lang($key, $params);
    }
}

/**
 * Localized time-ago string
 */
if (!function_exists('time_ago')) {
    function time_ago($datetime): string
    {
        if (empty($datetime)) return '';
        $now = new \DateTime();
        if (is_numeric($datetime)) {
            $ago = (new \DateTime())->setTimestamp((int)$datetime);
        } else {
            $ago = new \DateTime($datetime);
        }
        $diff = $now->diff($ago);

        if ($diff->y > 0) return lang('ComixxTime.years_ago', ['n' => $diff->y]);
        if ($diff->m > 0) return lang('ComixxTime.months_ago', ['n' => $diff->m]);
        if ($diff->d >= 7) {
            $w = (int) floor($diff->d / 7);
            return lang('ComixxTime.weeks_ago', ['n' => $w]);
        }
        if ($diff->d > 0) return lang('ComixxTime.days_ago', ['n' => $diff->d]);
        if ($diff->h > 0) return lang('ComixxTime.hours_ago', ['n' => $diff->h]);
        if ($diff->i > 0) return lang('ComixxTime.minutes_ago', ['n' => $diff->i]);
        return lang('ComixxTime.now');
    }
}

/**
 * JS time-ago strings as JSON for use in <script> tags
 */
if (!function_exists('time_ago_js')) {
    function time_ago_js(): string
    {
        return json_encode([
            'now'     => lang('ComixxTime.now'),
            'min'     => lang('ComixxTime.js_min'),
            'hour'    => lang('ComixxTime.js_hour'),
            'day'     => lang('ComixxTime.js_day'),
            'week'    => lang('ComixxTime.js_week'),
            'month'   => lang('ComixxTime.js_month'),
            'year'    => lang('ComixxTime.js_year'),
            'format'  => lang('ComixxTime.js_format'), // "ago" or "hace"
        ]);
    }
}
