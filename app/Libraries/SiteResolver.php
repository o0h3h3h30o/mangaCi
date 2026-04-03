<?php

namespace App\Libraries;

class SiteResolver
{
    private static ?int $siteId = null;

    /**
     * Resolve current domain → site_id.
     * Cached per request (static).
     */
    public static function resolve(): int
    {
        if (self::$siteId !== null) {
            return self::$siteId;
        }

        $hostRaw = strtolower($_SERVER['HTTP_HOST'] ?? $_SERVER['SERVER_NAME'] ?? 'localhost');
        $hostOnly = explode(':', $hostRaw)[0];

        $db = \Config\Database::connect();

        // Try full host:port first, then domain only
        $row = $db->table('sites')
                  ->where('domain', $hostRaw)
                  ->where('is_active', 1)
                  ->get()
                  ->getRowArray();

        if (!$row) {
            $row = $db->table('sites')
                      ->where('domain', $hostOnly)
                      ->where('is_active', 1)
                      ->get()
                      ->getRowArray();
        }

        self::$siteId = $row ? (int) $row['id'] : 1;

        return self::$siteId;
    }

    /**
     * Reset cache (for testing).
     */
    public static function reset(): void
    {
        self::$siteId = null;
    }
}
