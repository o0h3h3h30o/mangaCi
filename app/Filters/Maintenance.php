<?php

namespace App\Filters;

use CodeIgniter\Filters\FilterInterface;
use CodeIgniter\HTTP\RequestInterface;
use CodeIgniter\HTTP\ResponseInterface;

/**
 * Maintenance mode filter.
 *
 * When the `maintenance_mode` site setting is "1", every request returns a
 * 503 maintenance page EXCEPT:
 *   - admin area (`/admin/*`) and login/logout (so admins can disable it)
 *   - the request comes from a logged-in admin user
 *   - the client IP is in `maintenance_allow_ips` (comma-separated)
 */
class Maintenance implements FilterInterface
{
    public function before(RequestInterface $request, $arguments = null)
    {
        helper(['site_settings']);

        if (site_setting('maintenance_mode', '0') !== '1') {
            return;
        }

        $uri = '/' . ltrim($request->getUri()->getPath(), '/');

        // Always let admin / auth routes through so the admin can toggle it off.
        $bypassPrefixes = ['/admin', '/login', '/logout', '/install', '/api/admin'];
        foreach ($bypassPrefixes as $prefix) {
            if ($uri === $prefix || strpos($uri, $prefix . '/') === 0) {
                return;
            }
        }

        // Logged-in admins bypass too.
        if ($this->isAdmin()) {
            return;
        }

        // Optional IP allowlist.
        $allowIps = trim(site_setting('maintenance_allow_ips', ''));
        if ($allowIps !== '') {
            $clientIp = $_SERVER['HTTP_CF_CONNECTING_IP']
                ?? $_SERVER['HTTP_X_FORWARDED_FOR']
                ?? $_SERVER['REMOTE_ADDR']
                ?? '';
            $allowed = array_filter(array_map('trim', explode(',', $allowIps)));
            if (in_array($clientIp, $allowed, true)) {
                return;
            }
        }

        $message = site_setting('maintenance_message', '');
        $retry   = (int) (site_setting('maintenance_retry_after', '3600') ?: 3600);

        $body = view('maintenance', [
            'message' => $message,
            'siteTitle' => site_setting('site_title', 'Site'),
            'siteLogo'  => site_setting('site_logo', ''),
        ]);

        return service('response')
            ->setStatusCode(503, 'Service Unavailable')
            ->setHeader('Retry-After', (string) $retry)
            ->setHeader('Cache-Control', 'no-store, no-cache, must-revalidate')
            ->setBody($body);
    }

    public function after(RequestInterface $request, ResponseInterface $response, $arguments = null)
    {
        // no-op
    }

    /** Lightweight admin check using the same logic as Admin::guard(). */
    private function isAdmin(): bool
    {
        $session = session();
        if (!$session->get('isLoggedIn')) {
            return false;
        }
        $userId = (int) $session->get('user_id');
        if ($userId <= 0) {
            return false;
        }
        try {
            return \Config\Database::connect()
                ->table('users_groups ug')
                ->join('groups g', 'g.id = ug.group_id')
                ->where('ug.user_id', $userId)
                ->where('g.name', 'admin')
                ->countAllResults() > 0;
        } catch (\Throwable $e) {
            return false;
        }
    }
}
