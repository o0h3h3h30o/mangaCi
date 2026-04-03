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

        // Insert default site
        $mysqli->query("INSERT IGNORE INTO `sites` (`id`, `domain`, `name`, `is_active`) VALUES (1, 'localhost', 'Default', 1)");

        // Insert default comic types
        $mysqli->query("INSERT IGNORE INTO `comictype` (`id`, `label`) VALUES (1, 'Manga'), (2, 'Manhwa'), (3, 'Manhua')");

        // Insert default groups
        $mysqli->query("INSERT IGNORE INTO `groups` (`id`, `name`) VALUES (1, 'admin'), (2, 'members')");

        // Insert default site_settings
        $mysqli->query("INSERT IGNORE INTO `site_settings` (`site_id`, `key`, `value`) VALUES
            (1, 'site_title', 'MangaCI'),
            (1, 'meta_description', ''),
            (1, 'meta_keywords', ''),
            (1, 'active_theme', 'default'),
            (1, 'site_language', 'en'),
            (1, 'home_heading', ''),
            (1, 'site_logo', ''),
            (1, 'footer_logo', ''),
            (1, 'footer_copyright', ''),
            (1, 'footer_url', '/'),
            (1, 'ga_id', '')");

        // Insert admin user
        $now        = date('Y-m-d H:i:s');
        $passHash   = password_hash($admin_pass, PASSWORD_BCRYPT);
        $adminName  = $admin_name ?: $admin_username;

        $stmt = $mysqli->prepare("INSERT INTO `users` (`site_id`, `ip_address`, `username`, `password`, `email`, `name`, `active`, `last_login`, `created_on`, `created_at`, `updated_at`)
            VALUES (1, '127.0.0.1', ?, ?, ?, ?, 1, ?, ?, ?, ?)");
        $createdOn = time();
        $stmt->bind_param('sssssiss', $admin_username, $passHash, $admin_email, $adminName, $now, $createdOn, $now, $now);
        $stmt->execute();

        $adminId = (int) $stmt->insert_id;

        // Add admin to admin group
        $mysqli->query("INSERT IGNORE INTO `users_groups` (`site_id`, `user_id`, `group_id`) VALUES (1, {$adminId}, 1)");

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
            "CREATE TABLE IF NOT EXISTS `sites` (
                `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
                `domain` varchar(255) NOT NULL,
                `name` varchar(255) DEFAULT '',
                `is_active` tinyint(1) DEFAULT 1,
                PRIMARY KEY (`id`),
                UNIQUE KEY `uk_domain` (`domain`)
            ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci",

            "CREATE TABLE IF NOT EXISTS `users` (
                `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
                `site_id` int(10) unsigned NOT NULL DEFAULT 1,
                `name` varchar(255) DEFAULT NULL,
                `username` varchar(255) NOT NULL,
                `email` varchar(255) NOT NULL,
                `password` varchar(255) NOT NULL,
                `last_login` timestamp NULL DEFAULT NULL,
                `created_at` timestamp NULL DEFAULT NULL,
                `updated_at` timestamp NULL DEFAULT NULL,
                `ip_address` varchar(45) NOT NULL,
                `created_on` int(11) unsigned NOT NULL DEFAULT 0,
                `active` tinyint(1) unsigned DEFAULT NULL,
                PRIMARY KEY (`id`),
                UNIQUE KEY `users_site_username_unique` (`site_id`, `username`),
                UNIQUE KEY `users_site_email_unique` (`site_id`, `email`)
            ) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci",

            "CREATE TABLE IF NOT EXISTS `groups` (
                `id` mediumint(8) unsigned NOT NULL AUTO_INCREMENT,
                `name` varchar(20) NOT NULL,
                PRIMARY KEY (`id`)
            ) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_general_ci",

            "CREATE TABLE IF NOT EXISTS `users_groups` (
                `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
                `site_id` int(10) unsigned NOT NULL DEFAULT 1,
                `user_id` int(11) unsigned NOT NULL,
                `group_id` mediumint(8) unsigned NOT NULL,
                PRIMARY KEY (`id`),
                UNIQUE KEY `uc_users_groups` (`site_id`, `user_id`, `group_id`),
                KEY `fk_users_groups_users1_idx` (`user_id`),
                KEY `fk_users_groups_groups1_idx` (`group_id`),
                CONSTRAINT `fk_users_groups_groups1` FOREIGN KEY (`group_id`) REFERENCES `groups` (`id`) ON DELETE CASCADE ON UPDATE NO ACTION,
                CONSTRAINT `fk_users_groups_users1` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE ON UPDATE NO ACTION
            ) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_general_ci",

            "CREATE TABLE IF NOT EXISTS `site_settings` (
                `site_id` int(10) unsigned NOT NULL DEFAULT 1,
                `key` varchar(100) NOT NULL,
                `value` text DEFAULT NULL,
                PRIMARY KEY (`site_id`, `key`)
            ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci",

            "CREATE TABLE IF NOT EXISTS `comictype` (
                `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
                `label` varchar(255) DEFAULT NULL,
                `created_at` timestamp NULL DEFAULT NULL,
                `updated_at` timestamp NULL DEFAULT NULL,
                PRIMARY KEY (`id`)
            ) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci",

            "CREATE TABLE IF NOT EXISTS `status` (
                `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
                `name` varchar(50) NOT NULL,
                `label` varchar(50) DEFAULT NULL,
                PRIMARY KEY (`id`)
            ) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci",

            "INSERT IGNORE INTO `status` (`id`, `name`, `label`) VALUES (1, 'Ongoing', 'Ongoing'), (2, 'Completed', 'Completed')",

            "CREATE TABLE IF NOT EXISTS `manga` (
                `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
                `slug` varchar(512) DEFAULT NULL,
                `name` varchar(255) DEFAULT NULL,
                `otherNames` varchar(1000) DEFAULT NULL,
                `summary` text DEFAULT NULL,
                `cover` tinyint(1) DEFAULT NULL,
                `hot` tinyint(1) DEFAULT NULL,
                `caution` tinyint(1) DEFAULT 0,
                `views` int(11) DEFAULT 0,
                `status_id` int(10) unsigned DEFAULT NULL,
                `created_at` timestamp NULL DEFAULT NULL,
                `updated_at` timestamp NULL DEFAULT NULL,
                `is_new` tinyint(2) DEFAULT 0,
                `is_public` tinyint(1) NOT NULL DEFAULT 0,
                `new_slug` varchar(256) DEFAULT NULL,
                `chapter_1` float NOT NULL DEFAULT 0,
                `chap_1_slug` varchar(256) DEFAULT NULL,
                `time_chap_1` int(11) NOT NULL DEFAULT 0,
                `chapter_2` float NOT NULL DEFAULT 0,
                `chap_2_slug` varchar(256) DEFAULT NULL,
                `time_chap_2` int(11) NOT NULL DEFAULT 0,
                `create_at` int(11) NOT NULL DEFAULT 0,
                `update_at` int(11) NOT NULL DEFAULT 0,
                `view_day` int(11) DEFAULT 0,
                `view_week` int(11) DEFAULT 0,
                `view_month` int(11) DEFAULT 0,
                `rating` float(8,2) DEFAULT NULL,
                `from_manga18fx` varchar(500) DEFAULT NULL,
                `flag_chap_1` varchar(10) DEFAULT 'spain',
                `flag_chap_2` varchar(10) DEFAULT 'spain',
                `type_id` int(11) DEFAULT NULL,
                `image` varchar(256) DEFAULT NULL,
                PRIMARY KEY (`id`),
                KEY `manga_slug_index` (`slug`(191)),
                KEY `manga_status_id_foreign` (`status_id`),
                KEY `idx_public_viewday` (`is_public`, `view_day`),
                KEY `idx_public_viewmonth` (`is_public`, `view_month`),
                KEY `idx_public_views` (`is_public`, `views`),
                KEY `idx_public_updateat` (`is_public`, `update_at`),
                KEY `idx_public_id` (`is_public`, `id`)
            ) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci",

            "CREATE TABLE IF NOT EXISTS `chapter` (
                `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
                `slug` varchar(255) DEFAULT NULL,
                `name` varchar(255) DEFAULT NULL,
                `number` decimal(10,2) NOT NULL DEFAULT 0.00,
                `manga_id` int(10) unsigned NOT NULL,
                `created_at` timestamp NULL DEFAULT NULL,
                `updated_at` timestamp NULL DEFAULT NULL,
                `view` int(11) NOT NULL DEFAULT 0,
                `is_show` tinyint(4) NOT NULL DEFAULT 0,
                `is_crawling` tinyint(1) NOT NULL DEFAULT 0,
                `source_url` varchar(500) NOT NULL,
                PRIMARY KEY (`id`),
                KEY `chapter_manga_id_foreign` (`manga_id`),
                CONSTRAINT `chapter_manga_id_foreign` FOREIGN KEY (`manga_id`) REFERENCES `manga` (`id`)
            ) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci",

            "CREATE TABLE IF NOT EXISTS `page` (
                `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
                `slug` int(11) NOT NULL,
                `image` varchar(255) DEFAULT NULL,
                `external` tinyint(1) NOT NULL DEFAULT 0,
                `chapter_id` int(10) unsigned NOT NULL,
                `image_local` varchar(50) DEFAULT NULL,
                PRIMARY KEY (`id`),
                KEY `page_chapter_id_foreign` (`chapter_id`)
            ) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci",

            "CREATE TABLE IF NOT EXISTS `category` (
                `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
                `slug` varchar(255) NOT NULL,
                `name` varchar(255) NOT NULL,
                `created_at` timestamp NULL DEFAULT NULL,
                `updated_at` timestamp NULL DEFAULT NULL,
                `show_home` tinyint(1) NOT NULL DEFAULT 0,
                `jp_name` varchar(256) DEFAULT NULL,
                PRIMARY KEY (`id`),
                UNIQUE KEY `category_slug_unique` (`slug`)
            ) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci",

            "CREATE TABLE IF NOT EXISTS `category_manga` (
                `manga_id` int(10) unsigned NOT NULL,
                `category_id` int(10) unsigned NOT NULL,
                PRIMARY KEY (`manga_id`, `category_id`),
                KEY `category_manga_category_id_foreign` (`category_id`)
            ) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci",

            "CREATE TABLE IF NOT EXISTS `author` (
                `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
                `name` varchar(255) NOT NULL,
                `created_at` timestamp NULL DEFAULT NULL,
                `updated_at` timestamp NULL DEFAULT NULL,
                `slug` varchar(100) NOT NULL,
                PRIMARY KEY (`id`)
            ) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci",

            "CREATE TABLE IF NOT EXISTS `author_manga` (
                `manga_id` int(10) unsigned NOT NULL,
                `author_id` int(10) unsigned NOT NULL,
                `type` tinyint(1) NOT NULL DEFAULT 1,
                PRIMARY KEY (`manga_id`, `author_id`, `type`),
                KEY `author_manga_author_id_foreign` (`author_id`),
                CONSTRAINT `author_manga_author_id_foreign` FOREIGN KEY (`author_id`) REFERENCES `author` (`id`) ON DELETE CASCADE,
                CONSTRAINT `author_manga_manga_id_foreign` FOREIGN KEY (`manga_id`) REFERENCES `manga` (`id`) ON DELETE CASCADE
            ) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci",

            "CREATE TABLE IF NOT EXISTS `tag` (
                `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
                `name` varchar(255) NOT NULL,
                `slug` varchar(255) NOT NULL,
                `created_at` timestamp NULL DEFAULT NULL,
                `updated_at` timestamp NULL DEFAULT NULL,
                PRIMARY KEY (`id`)
            ) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci",

            "CREATE TABLE IF NOT EXISTS `manga_tag` (
                `manga_id` int(10) unsigned NOT NULL,
                `tag_id` int(10) unsigned NOT NULL,
                PRIMARY KEY (`manga_id`, `tag_id`),
                KEY `manga_tag_tag_id_foreign` (`tag_id`)
            ) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci",

            "CREATE TABLE IF NOT EXISTS `comments` (
                `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
                `site_id` int(10) unsigned NOT NULL DEFAULT 1,
                `comment` text DEFAULT NULL,
                `post_id` int(10) unsigned DEFAULT NULL,
                `post_type` varchar(255) DEFAULT NULL,
                `manga_id` int(11) DEFAULT NULL,
                `parent_id` int(10) unsigned DEFAULT NULL,
                `user_id` int(10) unsigned NOT NULL,
                `parent_comment` int(10) unsigned DEFAULT NULL,
                `created_at` timestamp NULL DEFAULT NULL,
                `updated_at` timestamp NULL DEFAULT NULL,
                PRIMARY KEY (`id`),
                KEY `comments_user_id_foreign` (`user_id`),
                KEY `comments_parent_comment_foreign` (`parent_comment`),
                KEY `idx_manga_comments` (`manga_id`, `parent_comment`, `created_at`),
                CONSTRAINT `comments_parent_comment_foreign` FOREIGN KEY (`parent_comment`) REFERENCES `comments` (`id`) ON DELETE SET NULL,
                CONSTRAINT `comments_user_id_foreign` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE
            ) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci",

            "CREATE TABLE IF NOT EXISTS `comment_likes` (
                `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
                `site_id` int(10) unsigned NOT NULL DEFAULT 1,
                `comment_id` int(10) unsigned NOT NULL,
                `user_id` int(10) unsigned NOT NULL,
                `type` enum('like','dislike') NOT NULL,
                `created_at` datetime DEFAULT CURRENT_TIMESTAMP,
                PRIMARY KEY (`id`),
                UNIQUE KEY `uq_comment_user` (`site_id`, `comment_id`, `user_id`)
            ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci",

            "CREATE TABLE IF NOT EXISTS `bookmarks` (
                `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
                `site_id` int(10) unsigned NOT NULL DEFAULT 1,
                `manga_id` int(10) unsigned NOT NULL,
                `user_id` int(10) unsigned NOT NULL,
                `created_at` timestamp NULL DEFAULT NULL,
                `updated_at` timestamp NULL DEFAULT NULL,
                PRIMARY KEY (`id`),
                KEY `bookmarks_manga_id_foreign` (`manga_id`),
                KEY `bookmarks_user_id_foreign` (`user_id`),
                CONSTRAINT `bookmarks_manga_id_foreign` FOREIGN KEY (`manga_id`) REFERENCES `manga` (`id`) ON DELETE CASCADE,
                CONSTRAINT `bookmarks_user_id_foreign` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE
            ) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci",

            "CREATE TABLE IF NOT EXISTS `item_ratings` (
                `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
                `site_id` int(10) unsigned NOT NULL DEFAULT 1,
                `item_id` int(11) NOT NULL,
                `score` tinyint(4) NOT NULL DEFAULT 1,
                `added_on` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
                `ip_address` varchar(255) NOT NULL,
                PRIMARY KEY (`id`),
                KEY `item_ratings_item_id_index` (`item_id`),
                KEY `item_ratings_ip_address_index` (`ip_address`),
                KEY `idx_site_item` (`site_id`, `item_id`)
            ) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci",

            "CREATE TABLE IF NOT EXISTS `notifications` (
                `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
                `site_id` int(10) unsigned NOT NULL DEFAULT 1,
                `user_id` int(10) unsigned NOT NULL,
                `actor_id` int(10) unsigned NOT NULL,
                `type` varchar(50) NOT NULL DEFAULT 'reply',
                `comment_id` int(10) unsigned DEFAULT NULL,
                `manga_id` int(10) unsigned DEFAULT NULL,
                `manga_slug` varchar(255) DEFAULT NULL,
                `manga_name` varchar(255) DEFAULT NULL,
                `chapter_slug` varchar(255) NOT NULL DEFAULT '',
                `preview` varchar(200) DEFAULT NULL,
                `is_read` tinyint(1) NOT NULL DEFAULT 0,
                `created_at` datetime DEFAULT CURRENT_TIMESTAMP,
                PRIMARY KEY (`id`),
                KEY `idx_user_unread` (`user_id`, `is_read`)
            ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci",

            "CREATE TABLE IF NOT EXISTS `chapter_reports` (
                `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
                `site_id` int(10) unsigned NOT NULL DEFAULT 1,
                `chapter_id` int(10) unsigned NOT NULL,
                `user_id` int(10) unsigned DEFAULT NULL,
                `reason` varchar(50) NOT NULL DEFAULT '',
                `note` text DEFAULT NULL,
                `ip_address` varchar(45) DEFAULT '',
                `status` varchar(20) NOT NULL DEFAULT 'pending',
                `created_at` datetime DEFAULT CURRENT_TIMESTAMP,
                PRIMARY KEY (`id`),
                KEY `idx_status` (`status`)
            ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci",

            "CREATE TABLE IF NOT EXISTS `content_likes` (
                `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
                `site_id` int(10) unsigned NOT NULL DEFAULT 1,
                `content_type` varchar(10) NOT NULL COMMENT 'manga or chapter',
                `content_id` int(11) unsigned NOT NULL,
                `user_id` int(11) unsigned NOT NULL,
                `type` varchar(10) NOT NULL DEFAULT 'like',
                `created_at` datetime DEFAULT CURRENT_TIMESTAMP,
                PRIMARY KEY (`id`),
                UNIQUE KEY `uk_content_user` (`site_id`, `content_type`, `content_id`, `user_id`),
                KEY `idx_content` (`content_type`, `content_id`)
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
