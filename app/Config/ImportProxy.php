<?php

namespace Config;

use CodeIgniter\Config\BaseConfig;

/**
 * Proxy pool for the manga import crawler (ImportController).
 *
 * Used to fetch source pages through rotating proxies so Cloudflare
 * doesn't block the server's datacenter IP. Edit the list / credentials
 * here. env() values (PROXY_ENABLED / PROXY_USER / PROXY_PASS) override
 * these when present.
 */
class ImportProxy extends BaseConfig
{
    /** Master on/off switch. */
    public bool $enabled = true;

    /** Proxy host IPs. */
    public array $hosts = [
        '87.98.97.14',
        '151.245.245.195',
        '62.192.172.221',
        '200.234.138.192',
        '109.111.36.108',
        '109.111.37.190',
        '150.241.251.128',
        '216.180.245.224',
        '146.103.51.77',
        '138.36.95.221',
        '138.36.93.13',
        '168.196.236.95',
        '95.164.150.5',
        '89.19.59.88',
        '185.228.192.57',
        '95.164.206.102',
        '151.247.124.155',
        '66.93.51.72',
        '66.93.162.187',
        '185.228.195.39',
    ];

    /** HTTP proxy port (SOCKS would be 50101). */
    public int $port = 50100;

    /** Credentials. */
    public string $user = 'liarhoang';
    public string $pass = 'xCyzCidVsm';

    /** How many distinct proxies to try before falling back to direct. */
    public int $attempts = 4;
}
