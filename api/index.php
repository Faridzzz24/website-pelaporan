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

// Paksa APP_ENV dan APP_DEBUG untuk debugging sementara
$_ENV['APP_ENV'] = 'production';
$_SERVER['APP_ENV'] = 'production';
putenv('APP_ENV=production');

// SEMENTARA: Aktifkan debug agar error terlihat
$_ENV['APP_DEBUG'] = 'true';
$_SERVER['APP_DEBUG'] = 'true';
putenv('APP_DEBUG=true');

// Set APP_KEY langsung (karena .env mungkin tidak terbaca di Vercel)
$_ENV['APP_KEY'] = 'base64:CYyVf2RfIYtdDB+cv9llBTcrnGOa5IwOdvUhV3rWnUA=';
$_SERVER['APP_KEY'] = 'base64:CYyVf2RfIYtdDB+cv9llBTcrnGOa5IwOdvUhV3rWnUA=';
putenv('APP_KEY=base64:CYyVf2RfIYtdDB+cv9llBTcrnGOa5IwOdvUhV3rWnUA=');

// Paksa DB connection ke sqlite dengan path /tmp agar tidak crash
$_ENV['DB_CONNECTION'] = 'sqlite';
$_SERVER['DB_CONNECTION'] = 'sqlite';
putenv('DB_CONNECTION=sqlite');
$_ENV['DB_DATABASE'] = '/tmp/database.sqlite';
$_SERVER['DB_DATABASE'] = '/tmp/database.sqlite';
putenv('DB_DATABASE=/tmp/database.sqlite');

// Buat file SQLite kosong di /tmp jika belum ada
if (!file_exists('/tmp/database.sqlite')) {
    touch('/tmp/database.sqlite');
}

try {
    $app->handleRequest(Request::capture());
} catch (\Throwable $e) {
    // Tampilkan error detail untuk debugging
    error_log('Laravel Error: ' . $e->getMessage() . ' in ' . $e->getFile() . ':' . $e->getLine());
    
    http_response_code(500);
    echo '<h1>Laravel Error</h1>';
    echo '<pre>' . htmlspecialchars($e->getMessage()) . "\n\n" . htmlspecialchars($e->getTraceAsString()) . '</pre>';
}
