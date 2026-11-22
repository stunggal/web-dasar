<?php
// dashboard.php — Halaman yang hanya bisa diakses setelah login (Langkah 5)
require_once __DIR__ . '/config.php';

// Cek session (Konsep Session: dashboard harus memeriksa session)
if (!isset($_SESSION['user_id'])) {
    // Belum login → redirect ke login
    header('Location: login.php');
    exit;
}

// Data user dari session
$userId = $_SESSION['user_id'];
$username = $_SESSION['username'];
$nama = $_SESSION['nama'];
$role = $_SESSION['role'];

// Ambil statistik dari database (contoh: mahasiswa & produk dari w-10-softdelete)
try {
    $jmlMahasiswa = $pdo->query("SELECT COUNT(*) FROM mahasiswa WHERE deleted_at IS NULL")->fetchColumn();
    $jmlProduk = $pdo->query("SELECT COUNT(*) FROM produk WHERE deleted_at IS NULL")->fetchColumn();
    $jmlTrashMhs = $pdo->query("SELECT COUNT(*) FROM mahasiswa WHERE deleted_at IS NOT NULL")->fetchColumn();
    $jmlTrashPrd = $pdo->query("SELECT COUNT(*) FROM produk WHERE deleted_at IS NOT NULL")->fetchColumn();
} catch (PDOException $e) {
    $jmlMahasiswa = $jmlProduk = $jmlTrashMhs = $jmlTrashPrd = 0;
}
?>
<!DOCTYPE html>
<html lang="id">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Dashboard — Web Dasar</title>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
  <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.css" rel="stylesheet">
</head>
<body class="bg-light">
<nav class="navbar navbar-expand-lg navbar-dark bg-primary shadow-sm">
  <div class="container">
    <a class="navbar-brand fw-bold" href="dashboard.php"><i class="bi bi-speedometer2 me-2"></i>Dashboard</a>
    <div class="navbar-nav ms-auto">
      <span class="nav-item nav-link text-white-50 small">
        <i class="bi bi-person-circle me-1"></i><?= htmlspecialchars($nama) ?> (<strong><?= htmlspecialchars($role) ?></strong>)
      </span>
      <a class="nav-item nav-link btn btn-outline-light btn-sm ms-2" href="logout.php">
        <i class="bi bi-box-arrow-right me-1"></i>Logout
      </a>
    </div>
  </div>
</nav>

<div class="container py-4">
  <!-- Welcome Card -->
  <div class="card border-0 shadow-sm mb-4">
    <div class="card-body">
      <div class="d-flex flex-wrap justify-content-between align-items-center gap-3">
        <div>
          <h4 class="fw-bold mb-1"><i class="bi bi-person-check me-2"></i>Selamat Datang, <?= htmlspecialchars($nama) ?>!</h4>
          <p class="text-muted mb-0">Anda login sebagai <span class="badge bg-<?= $role === 'admin' ? 'danger' : 'primary' ?>"><?= htmlspecialchars($role) ?></span> • Session aktif</p>
        </div>
        <a href="index.php" class="btn btn-outline-primary"><i class="bi bi-database me-1"></i>Kelola Data (CRUD)</a>
      </div>
    </div>
  </div>

  <!-- Stats Cards -->
  <div class="row g-3 mb-4">
    <div class="col-12 col-md-6 col-lg-3">
      <div class="card border-0 shadow-sm h-100">
        <div class="card-body">
          <div class="d-flex align-items-center">
            <div class="bg-primary bg-opacity-10 text-primary p-3 rounded-circle"><i class="bi bi-people fs-4"></i></div>
            <div class="ms-3">
              <h3 class="fw-bold mb-0"><?= number_format($jmlMahasiswa) ?></h3>
              <small class="text-muted">Mahasiswa Aktif</small>
            </div>
          </div>
        </div>
      </div>
    </div>
    <div class="col-12 col-md-6 col-lg-3">
      <div class="card border-0 shadow-sm h-100">
        <div class="card-body">
          <div class="d-flex align-items-center">
            <div class="bg-success bg-opacity-10 text-success p-3 rounded-circle"><i class="bi bi-box-seam fs-4"></i></div>
            <div class="ms-3">
              <h3 class="fw-bold mb-0"><?= number_format($jmlProduk) ?></h3>
              <small class="text-muted">Produk Aktif</small>
            </div>
          </div>
        </div>
      </div>
    </div>
    <div class="col-12 col-md-6 col-lg-3">
      <div class="card border-0 shadow-sm h-100">
        <div class="card-body">
          <div class="d-flex align-items-center">
            <div class="bg-warning bg-opacity-10 text-warning p-3 rounded-circle"><i class="bi bi-trash fs-4"></i></div>
            <div class="ms-3">
              <h3 class="fw-bold mb-0"><?= number_format($jmlTrashMhs) ?></h3>
              <small class="text-muted">Mahasiswa di Sampah</small>
            </div>
          </div>
        </div>
      </div>
    </div>
    <div class="col-12 col-md-6 col-lg-3">
      <div class="card border-0 shadow-sm h-100">
        <div class="card-body">
          <div class="d-flex align-items-center">
            <div class="bg-danger bg-opacity-10 text-danger p-3 rounded-circle"><i class="bi bi-archive fs-4"></i></div>
            <div class="ms-3">
              <h3 class="fw-bold mb-0"><?= number_format($jmlTrashPrd) ?></h3>
              <small class="text-muted">Produk di Sampah</small>
            </div>
          </div>
        </div>
      </div>
    </div>
  </div>

  <!-- Quick Actions -->
  <div class="card border-0 shadow-sm">
    <div class="card-header bg-white">
      <h5 class="fw-bold mb-0"><i class="bi bi-lightning me-2"></i>Aksi Cepat</h5>
    </div>
    <div class="card-body">
      <div class="row g-3">
        <div class="col-12 col-md-4">
          <a href="index.php" class="btn btn-primary w-100 py-3 d-flex flex-column align-items-center gap-2 text-decoration-none">
            <i class="bi bi-plus-circle fs-2"></i>
            <span class="fw-semibold">Tambah Data</span>
            <small class="text-white-50">Mahasiswa / Produk</small>
          </a>
        </div>
        <div class="col-12 col-md-4">
          <a href="index.php?view=trash" class="btn btn-outline-warning w-100 py-3 d-flex flex-column align-items-center gap-2 text-decoration-none">
            <i class="bi bi-trash fs-2"></i>
            <span class="fw-semibold">Keranjang Sampah</span>
            <small class="text-muted">Lihat & Pulihkan</small>
          </a>
        </div>
        <div class="col-12 col-md-4">
          <a href="logout.php" class="btn btn-outline-danger w-100 py-3 d-flex flex-column align-items-center gap-2 text-decoration-none">
            <i class="bi bi-box-arrow-right fs-2"></i>
            <span class="fw-semibold">Logout</span>
            <small class="text-muted">Akhiri Sesi</small>
          </a>
        </div>
      </div>
    </div>
  </div>

  <!-- Session Info -->
  <div class="card border-0 shadow-sm mt-4">
    <div class="card-header bg-white">
      <h6 class="fw-bold mb-0"><i class="bi bi-info-circle me-2"></i>Informasi Session</h6>
    </div>
    <div class="card-body">
      <div class="row g-3 text-center">
        <div class="col-6 col-md-3">
          <div class="p-3 bg-light rounded">
            <div class="fw-bold text-primary"><?= htmlspecialchars($userId) ?></div>
            <small class="text-muted">User ID</small>
          </div>
        </div>
        <div class="col-6 col-md-3">
          <div class="p-3 bg-light rounded">
            <div class="fw-bold text-primary"><?= htmlspecialchars($username) ?></div>
            <small class="text-muted">Username</small>
          </div>
        </div>
        <div class="col-6 col-md-3">
          <div class="p-3 bg-light rounded">
            <div class="fw-bold text-primary"><?= htmlspecialchars($role) ?></div>
            <small class="text-muted">Role</small>
          </div>
        </div>
        <div class="col-6 col-md-3">
          <div class="p-3 bg-light rounded">
            <div class="fw-bold text-primary"><?= date('d/m/Y H:i') ?></div>
            <small class="text-muted">Waktu Server</small>
          </div>
        </div>
      </div>
    </div>
  </div>
</div>

<footer class="bg-white border-top py-3 mt-4">
  <div class="container text-center text-muted small">
    &copy; 2026 Web Dasar • Dashboard • PHP Session + MySQL
  </div>
</footer>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>