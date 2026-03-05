<?php

namespace App\Controllers;

use CodeIgniter\Controller;
use CodeIgniter\HTTP\RequestInterface;
use CodeIgniter\HTTP\ResponseInterface;
use Psr\Log\LoggerInterface;

class Install extends Controller
{
    private string $lockFile = WRITEPATH . 'install.lock';

    public function initController(RequestInterface $request, ResponseInterface $response, LoggerInterface $logger)
    {
        parent::initController($request, $response, $logger);
    }

    // GET /install
    public function index(): string|\CodeIgniter\HTTP\RedirectResponse
    {
        if (is_file($this->lockFile)) {
            return view('install/done');
        }
        return view('install/index', ['error' => null, 'old' => []]);
    }

    // POST /install
    public function run(): \CodeIgniter\HTTP\RedirectResponse|string
    {
        if (is_file($this->lockFile)) {
            return redirect()->to('/');
        }

        // Collect input
        $db_host = trim($this->request->getPost('db_host') ?? '127.0.0.1');
        $db_port = (int) ($this->request->getPost('db_port') ?? 3306);
        $db_name = trim($this->request->getPost('db_name') ?? '');
        $db_user = trim($this->request->getPost('db_user') ?? '');
        $db_pass = $this->request->getPost('db_pass') ?? '';

        $admin_name     = trim($this->request->getPost('admin_name') ?? '');
        $admin_username = trim($this->request->getPost('admin_username') ?? '');
        $admin_email    = trim($this->request->getPost('admin_email') ?? '');
        $admin_pass     = $this->request->getPost('admin_pass') ?? '';
        $admin_confirm  = $this->request->getPost('admin_confirm') ?? '';

        $old = compact('db_host', 'db_port', 'db_name', 'db_user', 'admin_name', 'admin_username', 'admin_email');

        // Validate
        if (!$db_name || !$db_user) {
            return view('install/index', ['error' => 'Database name and username are required.', 'old' => $old]);
        }
        if (!$admin_username || !$admin_email || !$admin_pass) {
            return view('install/index', ['error' => 'Admin username, email, and password are required.', 'old' => $old]);
        }
        if (!preg_match('/^[a-zA-Z0-9_]{3,30}$/', $admin_username)) {
            return view('install/index', ['error' => 'Admin username: 3–30 characters, letters/numbers/underscore only.', 'old' => $old]);
        }
        if (!filter_var($admin_email, FILTER_VALIDATE_EMAIL)) {
            return view('install/index', ['error' => 'Invalid admin email address.', 'old' => $old]);
        }
        if (strlen($admin_pass) < 6) {
            return view('install/index', ['error' => 'Admin password must be at least 6 characters.', 'old' => $old]);
        }
        if ($admin_pass !== $admin_confirm) {
            return view('install/index', ['error' => 'Admin passwords do not match.', 'old' => $old]);
        }

        // Connect to MySQL (raw, bypass CI4 config)
        $mysqli = @new \mysqli($db_host, $db_user, $db_pass, '', $db_port ?: 3306);
        if ($mysqli->connect_errno) {
            return view('install/index', [
                'error' => 'Cannot connect to MySQL: ' . $mysqli->connect_error,
                'old'   => $old,
            ]);
        }

        // Create database if not exists
        $dbSafe = preg_replace('/[^a-zA-Z0-9_]/', '', $db_name);
        if (!$mysqli->query("CREATE DATABASE IF NOT EXISTS `{$dbSafe}` CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci")) {
            return view('install/index', ['error' => 'Cannot create database: ' . $mysqli->error, 'old' => $old]);
        }

        if (!$mysqli->select_db($dbSafe)) {
            return view('install/index', ['error' => 'Cannot select database: ' . $mysqli->error, 'old' => $old]);
        }

        $mysqli->set_charset('utf8mb4');

        // Run schema
        $sqls = $this->schema();
        foreach ($sqls as $sql) {
            if (!$mysqli->query($sql)) {
                return view('install/index', ['error' => 'SQL error: ' . $mysqli->error . '<br><pre>' . esc($sql) . '</pre>', 'old' => $old]);
            }
        }

        // Insert default groups
        $mysqli->query("INSERT IGNORE INTO `groups` (`id`, `name`, `description`) VALUES (1, 'admin', 'Site administrators'), (2, 'members', 'General members')");

        // Insert default site_settings
        $mysqli->query("INSERT IGNORE INTO `site_settings` (`key`, `value`) VALUES
            ('site_title', 'MangaCI'),
            ('meta_description', ''),
            ('meta_keywords', ''),
            ('active_theme', 'default'),
            ('site_logo', ''),
            ('footer_logo', ''),
            ('footer_copyright', ''),
            ('footer_url', '/')");

        // Insert admin user
        $now        = date('Y-m-d H:i:s');
        $passHash   = password_hash($admin_pass, PASSWORD_BCRYPT);
        $adminName  = $admin_name ?: $admin_username;
        $adminEmail = $mysqli->real_escape_string($admin_email);
        $adminUname = $mysqli->real_escape_string($admin_username);
        $adminNm    = $mysqli->real_escape_string($adminName);

        $mysqli->query("INSERT INTO `users` (`ip_address`, `username`, `password`, `email`, `name`, `active`, `last_login`, `created_on`, `created_at`, `updated_at`)
            VALUES ('127.0.0.1', '{$adminUname}', '{$passHash}', '{$adminEmail}', '{$adminNm}', 1, '{$now}', " . time() . ", '{$now}', '{$now}')");

        $adminId = (int) $mysqli->insert_id;

        // Add admin to admin group
        $mysqli->query("INSERT IGNORE INTO `users_groups` (`user_id`, `group_id`) VALUES ({$adminId}, 1)");

        $mysqli->close();

        // Write / update .env
        $this->writeEnv($db_host, $db_port ?: 3306, $db_name, $db_user, $db_pass);

        // Run composer install
        $composerLog = $this->runComposer();

        // Write lock file
        file_put_contents($this->lockFile, date('Y-m-d H:i:s'));

        return view('install/done', ['composerLog' => $composerLog]);
    }

    // ─── Private helpers ──────────────────────────────────────────

    private function schema(): array
    {
        return [
            "CREATE TABLE IF NOT EXISTS `users` (
                `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
                `ip_address` varchar(45) NOT NULL DEFAULT '',
                `username` varchar(100) NOT NULL DEFAULT '',
                `password` varchar(255) NOT NULL DEFAULT '',
                `email` varchar(254) NOT NULL DEFAULT '',
                `name` varchar(100) DEFAULT NULL,
                `active` tinyint(1) NOT NULL DEFAULT 0,
                `last_login` datetime DEFAULT NULL,
                `created_on` int(11) unsigned NOT NULL DEFAULT 0,
                `created_at` datetime DEFAULT NULL,
                `updated_at` datetime DEFAULT NULL,
                PRIMARY KEY (`id`),
                UNIQUE KEY `uk_username` (`username`),
                UNIQUE KEY `uk_email` (`email`)
            ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci",

            "CREATE TABLE IF NOT EXISTS `groups` (
                `id` mediumint(8) unsigned NOT NULL AUTO_INCREMENT,
                `name` varchar(20) NOT NULL,
                `description` varchar(100) NOT NULL DEFAULT '',
                PRIMARY KEY (`id`),
                UNIQUE KEY `uk_name` (`name`)
            ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci",

            "CREATE TABLE IF NOT EXISTS `users_groups` (
                `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
                `user_id` int(11) unsigned NOT NULL,
                `group_id` mediumint(8) unsigned NOT NULL,
                PRIMARY KEY (`id`),
                UNIQUE KEY `uk_user_group` (`user_id`, `group_id`)
            ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci",

            "CREATE TABLE IF NOT EXISTS `site_settings` (
                `key` varchar(100) NOT NULL,
                `value` text DEFAULT NULL,
                PRIMARY KEY (`key`)
            ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci",
        ];
    }

    private function writeEnv(string $host, int $port, string $db, string $user, string $pass): void
    {
        $envPath = ROOTPATH . '.env';

        // Read existing .env or start from env template
        $template = is_file($envPath) ? file_get_contents($envPath) : (is_file(ROOTPATH . 'env') ? file_get_contents(ROOTPATH . 'env') : '');

        // Helper: set or replace a key
        $set = function (string $content, string $key, string $value) use (&$set): string {
            $escaped = addslashes($value);
            // Uncomment + update if commented out
            $pattern = '/^#?\s*' . preg_quote($key, '/') . '\s*=.*$/m';
            if (preg_match($pattern, $content)) {
                return preg_replace($pattern, $key . ' = ' . $value, $content);
            }
            return $content . "\n" . $key . ' = ' . $value;
        };

        $template = $set($template, 'database.default.hostname', $host);
        $template = $set($template, 'database.default.database',  $db);
        $template = $set($template, 'database.default.username',  $user);
        $template = $set($template, 'database.default.password',  $pass);
        $template = $set($template, 'database.default.DBDriver',  'MySQLi');
        $template = $set($template, 'database.default.port',      (string) $port);
        $template = $set($template, 'CI_ENVIRONMENT',             'production');

        // Generate encryption key if missing
        if (!preg_match('/^encryption\.key\s*=/m', $template)) {
            $key     = bin2hex(random_bytes(16));
            $template .= "\nencryption.key = hex2bin:{$key}";
        }

        file_put_contents($envPath, $template);
    }

    private function runComposer(): array
    {
        // Tìm composer binary
        $composerBin = '';
        foreach (['/usr/local/bin/composer', '/usr/bin/composer'] as $path) {
            if (is_executable($path)) {
                $composerBin = $path;
                break;
            }
        }
        // Fallback: thử gọi qua PATH
        if (!$composerBin) {
            exec('which composer 2>/dev/null', $out);
            $composerBin = trim($out[0] ?? '');
        }

        if (!$composerBin) {
            return ['ok' => false, 'output' => 'composer not found in PATH. Cài đặt thủ công: composer install --no-dev --optimize-autoloader'];
        }

        if (!is_file(ROOTPATH . 'composer.json')) {
            return ['ok' => false, 'output' => 'composer.json not found.'];
        }

        $cmd    = escapeshellarg($composerBin) . ' install --no-dev --optimize-autoloader --no-interaction 2>&1';
        $cwd    = ROOTPATH;
        $output = [];
        $code   = 0;

        // Chạy trong ROOTPATH
        exec('cd ' . escapeshellarg($cwd) . ' && ' . $cmd, $output, $code);

        return [
            'ok'     => $code === 0,
            'output' => implode("\n", $output),
        ];
    }
}
