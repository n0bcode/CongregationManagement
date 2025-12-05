# Hướng dẫn Deploy lên Hostinger - Di chuyển Public Folder

## Vấn đề

Khi deploy lên Hostinger, thư mục `/public` của Laravel thường được di chuyển ra ngoài document root (`public_html`). Điều này gây ra một số lỗi cần được sửa.

## Cấu trúc thư mục trên Hostinger

```
/home/u221940070/domains/admin.sdndel.org/
├── public_html/              # Document root (nội dung của /public)
│   ├── index.php
│   ├── .htaccess
│   ├── build/               # Vite build files
│   ├── storage/             # Symlink to ../storage/app/public
│   └── ...
├── app/
├── bootstrap/
├── config/
├── database/
├── resources/
├── routes/
├── storage/
└── vendor/
```

## Các file cần sửa sau khi di chuyển public folder

### 1. **public_html/index.php** (Quan trọng nhất!)

File này cần trỏ đúng đường dẫn đến thư mục gốc của Laravel:

```php
<?php

use Illuminate\Http\Request;

define('LARAVEL_START', microtime(true));

// Determine if the application is in maintenance mode...
if (file_exists($maintenance = __DIR__.'/../storage/framework/maintenance.php')) {
    require $maintenance;
}

// Register the Composer autoloader...
require __DIR__.'/../vendor/autoload.php';

// Bootstrap Laravel and handle the request...
(require_once __DIR__.'/../bootstrap/app.php')
    ->handleRequest(Request::capture());
```

**Thay đổi:** Tất cả `__DIR__.'/../` thay vì `__DIR__.'/../../'` (vì public đã ở ngoài rồi)

### 2. **bootstrap/app.php**

Không cần sửa file này, nhưng kiểm tra để chắc chắn:

```php
<?php

use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;

return Application::configure(basePath: dirname(__DIR__))
    ->withRouting(
        web: __DIR__.'/../routes/web.php',
        commands: __DIR__.'/../routes/console.php',
        health: '/up',
    )
    ->withMiddleware(function (Middleware $middleware) {
        //
    })
    ->withExceptions(function (Exceptions $exceptions) {
        //
    })->create();
```

### 3. **vite.config.js** (Sửa build path)

```js
import { defineConfig } from "vite";
import laravel from "laravel-vite-plugin";

export default defineConfig({
    plugins: [
        laravel({
            input: ["resources/css/app.css", "resources/js/app.js"],
            refresh: true,
        }),
    ],
    build: {
        manifest: true,
        outDir: "public_html/build", // Thay đổi từ 'public/build'
        rollupOptions: {
            output: {
                manualChunks: undefined,
            },
        },
    },
});
```

**LƯU Ý:** Nếu bạn build trên local rồi upload, không cần sửa file này. Chỉ sửa nếu build trên server.

### 4. **.env** (Cấu hình đường dẫn)

```env
APP_NAME="Managing Congregation"
APP_ENV=production
APP_KEY=base64:your-app-key-here
APP_DEBUG=false
APP_URL=https://admin.sdndel.org

# Asset URL - Quan trọng cho Vite
ASSET_URL=https://admin.sdndel.org

# Database
DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=your_database
DB_USERNAME=your_username
DB_PASSWORD=your_password

# Filesystem
FILESYSTEM_DISK=public

# Session
SESSION_DRIVER=database
SESSION_LIFETIME=120

# Cache
CACHE_DRIVER=file
QUEUE_CONNECTION=database
```

### 5. **config/filesystems.php** (Kiểm tra public disk)

```php
'disks' => [
    'public' => [
        'driver' => 'local',
        'root' => storage_path('app/public'),
        'url' => env('APP_URL').'/storage',
        'visibility' => 'public',
        'throw' => false,
    ],
],
```

### 6. **Tạo Symlink cho Storage**

Sau khi upload code, chạy lệnh này qua SSH hoặc File Manager:

```bash
cd /home/u221940070/domains/admin.sdndel.org
php artisan storage:link
```

Hoặc tạo symlink thủ công:

```bash
ln -s /home/u221940070/domains/admin.sdndel.org/storage/app/public /home/u221940070/domains/admin.sdndel.org/public_html/storage
```

### 7. **Build Vite Assets**

**Trên local (khuyến nghị):**

```bash
npm run build
```

Sau đó upload thư mục `public/build` lên `public_html/build` trên server.

**Hoặc trên server (nếu có Node.js):**

```bash
cd /home/u221940070/domains/admin.sdndel.org
npm install
npm run build
```

### 8. **File .htaccess trong public_html**

Đảm bảo file `.htaccess` có nội dung đúng:

```apache
<IfModule mod_rewrite.c>
    <IfModule mod_negotiation.c>
        Options -MultiViews -Indexes
    </IfModule>

    RewriteEngine On

    # Handle Authorization Header
    RewriteCond %{HTTP:Authorization} .
    RewriteRule .* - [E=HTTP_AUTHORIZATION:%{HTTP:Authorization}]

    # Redirect Trailing Slashes If Not A Folder...
    RewriteCond %{REQUEST_FILENAME} !-d
    RewriteCond %{REQUEST_URI} (.+)/$
    RewriteRule ^ %1 [L,R=301]

    # Send Requests To Front Controller...
    RewriteCond %{REQUEST_FILENAME} !-d
    RewriteCond %{REQUEST_FILENAME} !-f
    RewriteRule ^ index.php [L]
</IfModule>
```

### 9. **Permissions (Quyền truy cập)**

Đảm bảo các thư mục sau có quyền ghi:

```bash
chmod -R 755 /home/u221940070/domains/admin.sdndel.org/storage
chmod -R 755 /home/u221940070/domains/admin.sdndel.org/bootstrap/cache
```

### 10. **Cache Configuration**

Sau khi deploy, chạy các lệnh sau:

```bash
cd /home/u221940070/domains/admin.sdndel.org

# Clear all caches
php artisan cache:clear
php artisan config:clear
php artisan route:clear
php artisan view:clear

# Optimize for production
php artisan config:cache
php artisan route:cache
php artisan view:cache
```

## Checklist Deploy

-   [ ] Upload tất cả files (trừ `/public`) vào thư mục gốc
-   [ ] Upload nội dung `/public` vào `public_html`
-   [ ] Sửa `public_html/index.php` - đường dẫn `__DIR__.'/../`
-   [ ] Upload file `.env` với cấu hình production
-   [ ] Tạo symlink storage: `php artisan storage:link`
-   [ ] Build Vite assets: `npm run build` và upload `build` folder
-   [ ] Set permissions cho `storage` và `bootstrap/cache`
-   [ ] Chạy migrations: `php artisan migrate --force`
-   [ ] Clear và cache lại: `php artisan optimize`
-   [ ] Test website

## Lỗi thường gặp và cách khắc phục

### Lỗi: "Vite manifest not found"

**Nguyên nhân:** Chưa build Vite assets hoặc đường dẫn sai.

**Giải pháp:**

1. Build assets trên local: `npm run build`
2. Upload thư mục `public/build` lên `public_html/build`
3. Hoặc tắt Vite trong development bằng cách comment `@vite` trong blade files

### Lỗi: "500 Internal Server Error"

**Nguyên nhân:** Permissions sai hoặc `.env` chưa đúng.

**Giải pháp:**

1. Kiểm tra file `.env` có tồn tại không
2. Set permissions: `chmod -R 755 storage bootstrap/cache`
3. Xem error log: `tail -f storage/logs/laravel.log`

### Lỗi: "Class not found"

**Nguyên nhân:** Composer autoload chưa được tạo.

**Giải pháp:**

```bash
composer install --optimize-autoloader --no-dev
php artisan optimize
```

### Lỗi: "Storage link not working"

**Nguyên nhân:** Symlink chưa được tạo hoặc bị lỗi.

**Giải pháp:**

```bash
# Xóa symlink cũ nếu có
rm -rf public_html/storage

# Tạo lại
php artisan storage:link
```

### Lỗi: "CSRF token mismatch"

**Nguyên nhân:** Session không hoạt động đúng.

**Giải pháp:**

1. Kiểm tra `SESSION_DRIVER` trong `.env`
2. Nếu dùng `database`, chạy: `php artisan session:table` và `php artisan migrate`
3. Clear cache: `php artisan cache:clear`

## Lưu ý quan trọng

1. **Không commit file `.env`** - Tạo riêng trên server
2. **Không upload `node_modules`** - Chỉ upload `build` folder
3. **Không upload `.git`** - Chỉ upload code
4. **Backup database** trước khi migrate
5. **Test trên subdomain** trước khi deploy production
6. **Enable maintenance mode** khi deploy: `php artisan down`
7. **Disable maintenance mode** sau khi xong: `php artisan up`

## Script tự động deploy (Optional)

Tạo file `deploy.sh` để tự động hóa:

```bash
#!/bin/bash

echo "🚀 Starting deployment..."

# Pull latest code
git pull origin main

# Install dependencies
composer install --optimize-autoloader --no-dev

# Build assets
npm install
npm run build

# Run migrations
php artisan migrate --force

# Clear and cache
php artisan optimize

echo "✅ Deployment completed!"
```

## Liên hệ hỗ trợ

Nếu gặp vấn đề, kiểm tra:

-   Laravel logs: `storage/logs/laravel.log`
-   Server error logs: Hostinger Control Panel > Error Logs
-   PHP version: Đảm bảo >= 8.2
-   Extensions: Kiểm tra PHP extensions cần thiết (PDO, OpenSSL, Mbstring, Tokenizer, XML, Ctype, JSON, BCMath)
