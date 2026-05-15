<?php
error_reporting(E_ALL);
ini_set('display_errors', '1');

// Cek keberadaan file autoload
if (!file_exists(__DIR__ . '/../vendor/autoload.php')) {
    die("Error: Folder 'vendor' atau file 'autoload.php' tidak ditemukan. Pastikan Anda sudah menjalankan composer install.");
}

try {
    require __DIR__ . '/../public/index.php';
} catch (\Throwable $e) {
    echo "<h1>Laravel Crash!</h1>";
    echo "<p><b>Pesan:</b> " . $e->getMessage() . "</p>";
    echo "<p><b>File:</b> " . $e->getFile() . " (Baris: " . $e->getLine() . ")</p>";
    echo "<pre>" . $e->getTraceAsString() . "</pre>";
}
