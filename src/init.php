<?php
require_once __DIR__ . '/config.php';
session_start();

try {
    $dsn = "mysql:host=" . DB_HOST . ";dbname=" . DB_NAME . ";charset=utf8mb4";
    $pdo = new PDO($dsn, DB_USER, DB_PASS, [
        PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
    ]);
} catch (PDOException $e) {
    die("DB Connection failed: " . $e->getMessage());
}

require_once __DIR__ . '/auth.php';
require_once __DIR__ . '/mailer.php';
