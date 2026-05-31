<?php
// Paksa PHP nampilin error biar kita gak buta
ini_set('display_errors', 1);
error_reporting(E_ALL);

// 1. Pindahkan SEMUA file cache system ke /tmp Vercel (WAJIB ditaruh paling atas)
putenv('APP_CONFIG_CACHE=/tmp/cache/config.php');
putenv('APP_EVENTS_CACHE=/tmp/cache/events.php');
putenv('APP_PACKAGES_CACHE=/tmp/cache/packages.php');
putenv('APP_ROUTES_CACHE=/tmp/cache/routes.php');
putenv('APP_SERVICES_CACHE=/tmp/cache/services.php');
$_SERVER['VIEW_COMPILED_PATH'] = '/tmp/storage/framework/views';
$_ENV['VIEW_COMPILED_PATH'] = '/tmp/storage/framework/views';

// 2. Buat kerangka folder di memori /tmp Vercel
$dirs = [
    '/tmp/cache',
    '/tmp/storage/framework/cache/data',
    '/tmp/storage/framework/sessions',
    '/tmp/storage/framework/views',
    '/tmp/storage/logs',
];

foreach ($dirs as $dir) {
    if (!is_dir($dir)) {
        mkdir($dir, 0777, true);
    }
}

// 3. Load Laravel
require __DIR__ . '/../vendor/autoload.php';
$app = require_once __DIR__ . '/../bootstrap/app.php';

// 4. Timpa letak folder Storage secara keseluruhan
$app->useStoragePath('/tmp/storage');

// 5. Eksekusi
$app->handleRequest(Illuminate\Http\Request::capture());