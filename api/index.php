<?php
// Paksa PHP nampilin error biar kita gak buta
ini_set('display_errors', 1);
error_reporting(E_ALL);

define('LARAVEL_START', microtime(true));

require __DIR__ . '/../vendor/autoload.php';

$app = require_once __DIR__ . '/../bootstrap/app.php';

// 1. Definisikan folder /tmp khusus untuk Vercel Serverless
$storagePath = '/tmp/storage';

// 2. Bikin semua folder yang dibutuhin Laravel secara "on-the-fly"
$directories = [
    'framework/cache/data',
    'framework/sessions',
    'framework/views',
    'logs'
];

foreach ($directories as $dir) {
    $path = $storagePath . '/' . $dir;
    if (!is_dir($path)) {
        mkdir($path, 0777, true);
    }
}

// 3. Paksa Laravel pakai folder /tmp/storage yang udah kita buat
$app->useStoragePath($storagePath);

// 4. Eksekusi Request-nya!
$app->handleRequest(Illuminate\Http\Request::capture());