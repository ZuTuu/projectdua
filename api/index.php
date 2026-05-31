<?php
// Paksa PHP nampilin error
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

// INI YANG LO SKIP BANG, PADAHAL INI KUNCINYA BIAR GAK CRASH!
$_SERVER['VIEW_COMPILED_PATH'] = '/tmp';
$_ENV['VIEW_COMPILED_PATH'] = '/tmp';

require __DIR__ . '/../public/index.php';