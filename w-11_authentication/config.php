<?php
// config.php — Koneksi database & session (shared oleh semua file)
$host = '[ganti ini dengan kredential mu]';
$dbname = '[ganti ini dengan kredential mu]';
$username = '[ganti ini dengan kredential mu]';
$password = '[ganti ini dengan kredential mu]';
$charset = 'utf8mb4';

$dsn = "mysql:host=$host;dbname=$dbname;charset=$charset";
$options = [
    PDO::ATTR_ERRMODE            => PDO::ERRMODE_EXCEPTION,
    PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
    PDO::ATTR_EMULATE_PREPARES   => false,
];

// Mulai session (harus di awal, sebelum output apapun)
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

try {
    $pdo = new PDO($dsn, $username, $password, $options);
} catch (PDOException $e) {
    die("Koneksi database gagal: " . htmlspecialchars($e->getMessage()));
}

// Helper flash message
function flash_set($type, $msg) { $_SESSION['flash'] = ['type' => $type, 'msg' => $msg]; }
function flash_get() { $f = $_SESSION['flash'] ?? null; unset($_SESSION['flash']); return $f; }

// Helper cek login (untuk halaman yang butuh auth)
function require_login() {
    if (!isset($_SESSION['user_id'])) {
        header('Location: login.php');
        exit;
    }
}