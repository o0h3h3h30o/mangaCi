# MangaCI — Hướng dẫn Deploy Server

## Yêu cầu hệ thống

| Thành phần | Phiên bản |
|-----------|-----------|
| PHP | 8.1+ |
| MySQL / MariaDB | 8.0+ / 10.6+ |
| Nginx (khuyến nghị) | 1.18+ |
| Composer | 2.x |

PHP extensions cần có: `mysqli`, `gd`, `curl`, `mbstring`, `xml`, `zip`, `intl`

---

## 1. Cài đặt môi trường (Ubuntu 22.04)

```bash
sudo apt update && sudo apt upgrade -y

# PHP 8.2
sudo apt install -y software-properties-common
sudo add-apt-repository ppa:ondrej/php -y
sudo apt update
sudo apt install -y php8.2 php8.2-fpm php8.2-mysql php8.2-gd \
  php8.2-curl php8.2-mbstring php8.2-xml php8.2-zip php8.2-intl

# Nginx + MySQL
sudo apt install -y nginx mysql-server

# Composer
curl -sS https://getcomposer.org/installer | php
sudo mv composer.phar /usr/local/bin/composer
```

---

## 2. Clone code từ GitHub

```bash
cd /var/www
sudo git clone https://github.com/o0h3h3h30o/mangaCi.git manga
sudo chown -R www-data:www-data manga
cd manga
```

---

## 3. Cài dependencies

```bash
composer install --no-dev --optimize-autoloader
```

---

## 4. Cấu hình `.env`

```bash
cp env .env
nano .env
```

Sửa các giá trị sau trong `.env`:

```env
CI_ENVIRONMENT = production

app.baseURL = 'https://yourdomain.com/'
app.forceGlobalSecureRequests = true

database.default.hostname = 127.0.0.1
database.default.database  = manga_db
database.default.username  = manga_user
database.default.password  = your_password
database.default.DBDriver  = MySQLi
database.default.port      = 3306

# S3 / CDN (nếu dùng)
S3_KEY      = your_access_key
S3_SECRET   = your_secret_key
S3_BUCKET   = your_bucket
S3_REGION   = your_region
S3_ENDPOINT = https://your-s3-endpoint
CDN_CHAPTER_URL = https://your-cdn-url

# Encryption key (tạo ngẫu nhiên)
encryption.key = hex2bin:xxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxx
```

> Tạo encryption key: `php spark key:generate`

---

## 5. Cài đặt MySQL

```bash
sudo mysql -u root

-- Trong MySQL shell:
CREATE DATABASE manga_db CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
CREATE USER 'manga_user'@'localhost' IDENTIFIED BY 'your_password';
GRANT ALL PRIVILEGES ON manga_db.* TO 'manga_user'@'localhost';
FLUSH PRIVILEGES;
EXIT;
```

Import database (nếu có file SQL sẵn):
```bash
mysql -u manga_user -p manga_db < database.sql
```

Thêm setting theme vào DB:
```sql
INSERT IGNORE INTO site_settings (`key`, `value`) VALUES ('active_theme', 'default');
```

---

## 6. Cấu hình Nginx

```bash
sudo nano /etc/nginx/sites-available/manga
```

```nginx
server {
    listen 80;
    server_name yourdomain.com www.yourdomain.com;
    root /var/www/manga/public;
    index index.php;

    charset utf-8;
    client_max_body_size 50M;

    location / {
        try_files $uri $uri/ /index.php$is_args$args;
    }

    location ~ \.php$ {
        fastcgi_pass unix:/run/php/php8.2-fpm.sock;
        fastcgi_index index.php;
        fastcgi_param SCRIPT_FILENAME $document_root$fastcgi_script_name;
        include fastcgi_params;
        fastcgi_read_timeout 120;
    }

    # Chặn truy cập thư mục nhạy cảm
    location ~ ^/(app|system|tests|writable) {
        deny all;
    }

    location ~* \.(js|css|png|jpg|jpeg|gif|webp|ico|svg|woff|woff2|ttf)$ {
        expires 30d;
        add_header Cache-Control "public, immutable";
    }

    location ~ /\.(?!well-known) {
        deny all;
    }
}
```

```bash
sudo ln -s /etc/nginx/sites-available/manga /etc/nginx/sites-enabled/
sudo nginx -t
sudo systemctl restart nginx
```

---

## 7. Phân quyền thư mục

```bash
cd /var/www/manga
sudo chown -R www-data:www-data .
sudo chmod -R 755 .
sudo chmod -R 777 writable/
sudo chmod -R 777 public/uploads/   # nếu dùng local upload
```

---

## 8. SSL với Let's Encrypt

```bash
sudo apt install -y certbot python3-certbot-nginx
sudo certbot --nginx -d yourdomain.com -d www.yourdomain.com

# Auto-renew (đã tự động, kiểm tra bằng)
sudo certbot renew --dry-run
```

---

## 9. Cấu hình PHP-FPM (tối ưu)

```bash
sudo nano /etc/php/8.2/fpm/pool.d/www.conf
```

Chỉnh các dòng sau:
```ini
pm = dynamic
pm.max_children = 20
pm.start_servers = 5
pm.min_spare_servers = 3
pm.max_spare_servers = 8
```

```bash
sudo systemctl restart php8.2-fpm
```

---

## 10. Cập nhật code (khi có thay đổi)

```bash
cd /var/www/manga
git pull origin main
composer install --no-dev --optimize-autoloader
sudo chown -R www-data:www-data .
sudo chmod -R 777 writable/
```

---

## Cấu trúc thư mục quan trọng

```
manga/
├── app/
│   ├── Controllers/    # Logic xử lý
│   ├── Models/         # Database models
│   ├── Views/
│   │   ├── themes/
│   │   │   ├── default/   # Theme mặc định
│   │   │   └── madara/    # Theme madara (customize tại đây)
│   │   └── admin/         # Admin panel views
│   └── Config/
│       └── Routes.php     # Cấu hình routes
├── public/             # Document root (trỏ Nginx vào đây)
│   ├── css/
│   ├── js/
│   └── uploads/        # File upload local
├── writable/           # Cache, logs, sessions (cần chmod 777)
├── .env                # Cấu hình môi trường (KHÔNG commit)
└── env                 # File mẫu .env
```

---

## Thêm theme mới

1. Copy thư mục `app/Views/themes/default/` → `app/Views/themes/tentheme/`
2. Chỉnh sửa các file trong `tentheme/` theo design mới
3. Vào Admin → Settings → Active Theme → chọn `tentheme` → Save

---

## Troubleshooting

**Site trắng / 500 error:**
```bash
tail -50 /var/www/manga/writable/logs/log-$(date +%Y-%m-%d).log
```

**Permission denied:**
```bash
sudo chmod -R 777 /var/www/manga/writable/
```

**Nginx 502 Bad Gateway:**
```bash
sudo systemctl status php8.2-fpm
sudo systemctl restart php8.2-fpm
```

**Session không lưu:**
```bash
sudo chmod 777 /var/www/manga/writable/session/
```
