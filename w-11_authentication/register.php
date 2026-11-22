<?php
// register.php — Form pendaftaran pengguna baru
require_once __DIR__ . '/config.php';

// Jika sudah login, redirect ke dashboard
if (isset($_SESSION['user_id'])) {
    header('Location: dashboard.php');
    exit;
}

$nama = '';
$username = '';
$error = '';
$success = '';

// Proses registrasi
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $username = trim($_POST['username'] ?? '');
    $nama = trim($_POST['nama'] ?? '');
    $password = $_POST['password'] ?? '';
    $konfirmasi = $_POST['konfirmasi'] ?? '';

    // Validasi input
    if ($username === '' || $nama === '' || $password === '' || $konfirmasi === '') {
        $error = 'Semua field wajib diisi.';
    } elseif (strlen($password) < 6) {
        $error = 'Password minimal 6 karakter.';
    } elseif ($password !== $konfirmasi) {
        $error = 'Password dan konfirmasi password tidak cocok.';
    } else {
        // Cek apakah username sudah dipakai
        try {
            $stmt = $pdo->prepare("SELECT id FROM users WHERE username = ?");
            $stmt->execute([$username]);
            if ($stmt->fetch()) {
                $error = 'Username sudah digunakan. Pilih username lain.';
            } else {
                // Hash password & insert
                $hash = password_hash($password, PASSWORD_DEFAULT);
                $stmt = $pdo->prepare("INSERT INTO users (username, password, nama, role) VALUES (?, ?, ?, 'user')");
                $stmt->execute([$username, $hash, $nama]);
                $success = 'Pendaftaran berhasil! Silakan login.';
                $username = '';
                $nama = '';
            }
        } catch (PDOException $e) {
            $error = 'Terjadi kesalahan sistem: ' . $e->getMessage();
        }
    }
}
?>
<!DOCTYPE html>
<html lang="id">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Daftar — Web Dasar</title>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
  <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.css" rel="stylesheet">
  <style>
    body { background: linear-gradient(135deg, #667eea 0%, #764ba2 100%); min-height: 100vh; display: flex; align-items: center; justify-content: center; }
    .register-card { max-width: 440px; width: 100%; box-shadow: 0 10px 30px rgba(0,0,0,0.2); border: none; border-radius: 16px; }
    .register-header { background: linear-gradient(135deg, #667eea 0%, #764ba2 100%); color: white; border-radius: 16px 16px 0 0; padding: 2rem; text-align: center; }
    .form-control { border-radius: 8px; padding: 0.75rem 1rem; }
    .btn-register { border-radius: 8px; padding: 0.75rem; font-weight: 600; }
  </style>
</head>
<body>
<div class="container">
  <div class="card register-card">
    <div class="register-header">
      <i class="bi bi-person-plus fs-1 mb-2"></i>
      <h3 class="fw-bold mb-0">Daftar Akun</h3>
      <p class="mb-0 opacity-75">Buat akun baru untuk memulai</p>
    </div>
    <div class="card-body p-4">
      <?php if ($error): ?>
        <div class="alert alert-danger alert-dismissible fade show" role="alert">
          <i class="bi bi-exclamation-triangle me-2"></i><?= htmlspecialchars($error) ?>
          <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
      <?php endif; ?>
      <?php if ($success): ?>
        <div class="alert alert-success alert-dismissible fade show" role="alert">
          <i class="bi bi-check-circle me-2"></i><?= htmlspecialchars($success) ?>
          <a href="login.php" class="alert-link">Login sekarang</a>
          <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
      <?php endif; ?>

      <form method="post" novalidate>
        <div class="mb-3">
          <label for="username" class="form-label fw-semibold"><i class="bi bi-at me-2"></i>Username</label>
          <input type="text" name="username" id="username" class="form-control" required autofocus
                 value="<?= htmlspecialchars($username) ?>" placeholder="Pilih username">
          <div class="form-text">Digit, huruf, atau kombinasi. Unik di seluruh sistem.</div>
        </div>

        <div class="mb-3">
          <label for="nama" class="form-label fw-semibold"><i class="bi bi-person me-2"></i>Nama Lengkap</label>
          <input type="text" name="nama" id="nama" class="form-control" required
                 value="<?= htmlspecialchars($nama) ?>" placeholder="Nama lengkap Anda">
        </div>

        <div class="mb-3">
          <label for="password" class="form-label fw-semibold"><i class="bi bi-lock me-2"></i>Password</label>
          <input type="password" name="password" id="password" class="form-control" required
                 placeholder="Minimal 6 karakter">
          <div class="form-text">Gunakan minimal 6 karakter.</div>
        </div>

        <div class="mb-4">
          <label for="konfirmasi" class="form-label fw-semibold"><i class="bi bi-lock-fill me-2"></i>Konfirmasi Password</label>
          <input type="password" name="konfirmasi" id="konfirmasi" class="form-control" required
                 placeholder="Ulangi password">
        </div>

        <button type="submit" class="btn btn-primary btn-register w-100 mb-3">
          <i class="bi bi-person-plus me-2"></i>Daftar
        </button>
      </form>

      <div class="text-center text-muted small">
        <p class="mb-0">Sudah punya akun? <a href="login.php" class="fw-semibold">Login di sini</a></p>
      </div>
    </div>
  </div>
</div>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>