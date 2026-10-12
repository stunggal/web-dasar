<?php
// config/database.php — koneksi PDO native untuk web-dasar
$host = 'localhost';
$dbname = 'web_dasar';
$username = 'root';
$password = '';
$charset = 'utf8mb4';

$dsn = "mysql:host=$host;dbname=$dbname;charset=$charset";
$options = [
    PDO::ATTR_ERRMODE            => PDO::ERRMODE_EXCEPTION,
    PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
    PDO::ATTR_EMULATE_PREPARES   => false,
];

try {
    $pdo = new PDO($dsn, $username, $password, $options);
} catch (PDOException $e) {
    // jangan expose detail di production
    die('Koneksi database gagal: ' . htmlspecialchars($e->getMessage()));
}
