<?php
// Konfigurasi Database Lokal
$host = 'localhost';
$dbname = 'nama_database'; // Ganti dengan nama database Anda
$username = 'root';          // Default XAMPP/Laragon
$password = '';              // Default kosong

try {
    // Membuat koneksi PDO
    $pdo = new PDO("mysql:host=$host;dbname=$dbname;charset=utf8", $username, $password);

    // Mengatur mode error PDO ke Exception
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

    // Query untuk mengambil seluruh data dari tabel (contoh: tabel 'mahasiswa')
    $sql = "SELECT * FROM mahasiswa";
    $stmt = $pdo->query($sql);

    // Mengambil semua baris data sebagai array asosiatif
    $dataMahasiswa = $stmt->fetchAll(PDO::FETCH_ASSOC);
} catch (PDOException $e) {
    // Tangani error koneksi atau query
    die("Koneksi gagal: " . $e->getMessage());
}
