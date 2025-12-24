<?php
// config/app.php

/**
 * Menentukan Base URL aplikasi secara dinamis.
 * Ini memungkinkan aplikasi berfungsi dengan benar baik di root server web
 * maupun di dalam subdirektori (misalnya, /pkl/).
 */

// Izinkan BASE_URL di-override oleh environment variable untuk konfigurasi khusus
if (getenv('BASE_URL')) {
    define('BASE_URL', getenv('BASE_URL'));
} else {
    // Tentukan path dasar secara dinamis
    $base_path = dirname($_SERVER['SCRIPT_NAME']);

    // Normalisasi untuk kasus di mana aplikasi berada di direktori root
    if ($base_path === '.' || $base_path === '\\' || $base_path === '/') {
        $base_path = '/';
    } else {
        // Pastikan ada garis miring di akhir untuk subdirektori
        $base_path = rtrim($base_path, '/') . '/';
    }

    define('BASE_URL', $base_path);
}
