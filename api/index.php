<?php

use Illuminate\Http\Request;

define('LARAVEL_START', microtime(true));

// Atur error reporting agar error muncul di log Vercel
ini_set('display_errors', '0');
ini_set('log_errors', '1');
error_reporting(E_ALL);

require __DIR__.'/../vendor/autoload.php';

$app = require_once __DIR__.'/../bootstrap/app.php';

// Atur folder storage ke /tmp karena Vercel bersifat Read-Only
$storagePath = '/tmp/storage';
$app->useStoragePath($storagePath);

// Buat struktur direktori yang dibutuhkan Laravel secara dinamis
foreach (['app', 'framework/cache/data', 'framework/sessions', 'framework/views', 'logs'] as $dir) {
    if (!is_dir("$storagePath/$dir")) {
        mkdir("$storagePath/$dir", 0777, true);
    }
}

// Paksa konfigurasi yang kompatibel dengan serverless (Vercel)
// Session tidak bisa pakai database/file di serverless, gunakan cookie
$_ENV['SESSION_DRIVER'] = 'cookie';
$_SERVER['SESSION_DRIVER'] = 'cookie';
putenv('SESSION_DRIVER=cookie');

// Cache tidak bisa pakai database/file di serverless, gunakan array
$_ENV['CACHE_STORE'] = 'array';
$_SERVER['CACHE_STORE'] = 'array';
putenv('CACHE_STORE=array');

// Log ke stderr agar muncul di Vercel Function Logs
$_ENV['LOG_CHANNEL'] = 'errorlog';
$_SERVER['LOG_CHANNEL'] = 'errorlog';
putenv('LOG_CHANNEL=errorlog');

// Paksa APP_ENV ke production
$_ENV['APP_ENV'] = 'production';
$_SERVER['APP_ENV'] = 'production';
putenv('APP_ENV=production');

try {
    $app->handleRequest(Request::capture());
} catch (\Throwable $e) {
    // Jika terjadi error, tampilkan pesan yang berguna untuk debugging
    error_log('Laravel Error: ' . $e->getMessage() . ' in ' . $e->getFile() . ':' . $e->getLine());
    
    if (env('APP_DEBUG', false)) {
        echo '<h1>Error</h1>';
        echo '<pre>' . $e->getMessage() . "\n" . $e->getTraceAsString() . '</pre>';
    } else {
        http_response_code(500);
        echo '<h1>500 - Server Error</h1>';
        echo '<p>Terjadi kesalahan pada server. Silakan coba lagi nanti.</p>';
    }
}


