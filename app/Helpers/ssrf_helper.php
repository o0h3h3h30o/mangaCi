<?php

if (!function_exists('is_safe_public_url')) {
    /**
     * Validate that a URL is safe to fetch from a server (defeats SSRF).
     * Rejects non-http(s) schemes, private/loopback/link-local IPs, and
     * cloud metadata endpoints.
     *
     * @return array{ok:bool, error?:string, ip?:string, host?:string, port?:int}
     */
    function is_safe_public_url(string $url): array
    {
        if (!filter_var($url, FILTER_VALIDATE_URL)) {
            return ['ok' => false, 'error' => 'Invalid URL'];
        }
        $parts = parse_url($url);
        if (!$parts || empty($parts['scheme']) || empty($parts['host'])) {
            return ['ok' => false, 'error' => 'Malformed URL'];
        }
        $scheme = strtolower($parts['scheme']);
        if (!in_array($scheme, ['http', 'https'], true)) {
            return ['ok' => false, 'error' => 'Unsupported scheme: ' . $scheme];
        }
        $host = $parts['host'];
        $port = (int) ($parts['port'] ?? ($scheme === 'https' ? 443 : 80));
        if ($port !== 80 && $port !== 443) {
            return ['ok' => false, 'error' => 'Port not allowed: ' . $port];
        }

        $ips = [];
        if (filter_var($host, FILTER_VALIDATE_IP)) {
            $ips[] = $host;
        } else {
            $records = @dns_get_record($host, DNS_A + DNS_AAAA);
            if (!$records) {
                $resolved = @gethostbynamel($host);
                if (is_array($resolved)) $ips = $resolved;
            } else {
                foreach ($records as $r) {
                    if (!empty($r['ip']))    $ips[] = $r['ip'];
                    if (!empty($r['ipv6']))  $ips[] = $r['ipv6'];
                }
            }
            if (!$ips) {
                return ['ok' => false, 'error' => 'DNS resolution failed'];
            }
        }

        foreach ($ips as $ip) {
            if (!filter_var(
                $ip,
                FILTER_VALIDATE_IP,
                FILTER_FLAG_NO_PRIV_RANGE | FILTER_FLAG_NO_RES_RANGE
            )) {
                return ['ok' => false, 'error' => 'Private or reserved IP: ' . $ip];
            }
            // Explicit cloud metadata endpoints (AWS/GCP/Azure/Alibaba)
            if (in_array($ip, ['169.254.169.254', 'fd00:ec2::254', '100.100.100.200'], true)) {
                return ['ok' => false, 'error' => 'Blocked metadata IP'];
            }
        }

        return ['ok' => true, 'host' => $host, 'port' => $port, 'ip' => $ips[0]];
    }
}

if (!function_exists('ssrf_safe_curl_fetch')) {
    /**
     * cURL fetch with SSRF guards. Returns [body, httpCode, contentType] or
     * throws \RuntimeException on validation failure.
     *
     * @param array<int,mixed> $extraOpts
     */
    function ssrf_safe_curl_fetch(string $url, int $timeout = 30, array $extraOpts = []): array
    {
        $check = is_safe_public_url($url);
        if (!$check['ok']) {
            throw new \RuntimeException('Unsafe URL: ' . ($check['error'] ?? 'unknown'));
        }
        $ch = curl_init($url);
        if ($ch === false) {
            throw new \RuntimeException('cURL initialization failed');
        }
        curl_setopt_array($ch, [
            CURLOPT_RETURNTRANSFER  => true,
            CURLOPT_FOLLOWLOCATION  => false, // prevent redirect to internal
            CURLOPT_TIMEOUT         => $timeout,
            CURLOPT_CONNECTTIMEOUT  => 10,
            CURLOPT_USERAGENT       => 'Mozilla/5.0 (compatible; MangaBot/1.0)',
            CURLOPT_SSL_VERIFYPEER  => true,
            CURLOPT_SSL_VERIFYHOST  => 2,
            CURLOPT_PROTOCOLS       => defined('CURLPROTO_HTTP') ? (CURLPROTO_HTTP | CURLPROTO_HTTPS) : 3,
            CURLOPT_REDIR_PROTOCOLS => defined('CURLPROTO_HTTP') ? (CURLPROTO_HTTP | CURLPROTO_HTTPS) : 3,
        ]);
        foreach ($extraOpts as $k => $v) curl_setopt($ch, $k, $v);
        $body     = curl_exec($ch);
        if ($body === false) {
            throw new \RuntimeException('cURL error: ' . curl_error($ch));
        }
        $httpCode = (int) curl_getinfo($ch, CURLINFO_HTTP_CODE);
        $ct       = (string) curl_getinfo($ch, CURLINFO_CONTENT_TYPE);
        return [$body, $httpCode, $ct];
    }
}
