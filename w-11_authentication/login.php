<?php
// login.php — Form login + proses autentikasi
require_once __DIR__ . '/config.php';

// Jika sudah login, redirect ke dashboard
if (isset($_SESSION['user_id'])) {
    header('Location: dashboard.php');
    exit;
}

$error = '';
$username = '';

// Tampilkan pesan logout sukses
if (isset($_GET['logout'])) {
    $error = 'Anda telah logout. Silakan login kembali.';
    // Ganti jadi success alert via flash
    flash_set('success', 'Anda telah logout. Silakan login kembali.');
    $error = '';
}

// Proses login
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $username = trim($_POST['username'] ?? '');
    $password = $_POST['password'] ?? '';

    // Validasi input (Langkah 2: Validasi Input)
    if ($username === '' || $password === '') {
        $error = 'Username dan password tidak boleh kosong.';
    } else {
        // Langkah 4: Pemeriksaan ke Database
        try {
            $stmt = $pdo->prepare("SELECT id, username, password, nama, role FROM users WHERE username = ?");
            $stmt->execute([$username]);
            $user = $stmt->fetch();

            if ($user && password_verify($password, $user['password'])) {
                // Langkah 5: Login Berhasil - Buat Session
                $_SESSION['user_id'] = $user['id'];
                $_SESSION['username'] = $user['username'];
                $_SESSION['nama'] = $user['nama'];
                $_SESSION['role'] = $user['role'];

                header('Location: dashboard.php');
                exit;
            } else {
                // Langkah 6: Login Gagal
                $error = 'Username atau password salah.';
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
  <title>Login — Web Dasar</title>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
  <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.css" rel="stylesheet">
  <style>
    body { background: linear-gradient(135deg, #667eea 0%, #764ba2 100%); min-height: 100vh; display: flex; align-items: center; justify-content: center; }
    .login-card { max-width: 420px; width: 100%; box-shadow: 0 10px 30px rgba(0,0,0,0.2); border: none; border-radius: 16px; }
    .login-header { background: linear-gradient(135deg, #667eea 0%, #764ba2 100%); color: white; border-radius: 16px 16px 0 0; padding: 2rem; text-align: center; }
    .form-control { border-radius: 8px; padding: 0.75rem 1rem; }
    .btn-login { border-radius: 8px; padding: 0.75rem; font-weight: 600; }
  </style>
</head>
<body>
<div class="container">
  <div class="card login-card">
    <div class="login-header">
      <i class="bi bi-box-seam fs-1 mb-2"></i>
      <h3 class="fw-bold mb-0">Web Dasar</h3>
      <p class="mb-0 opacity-75">Sistem Login PHP + MySQL</p>
    </div>
    <div class="card-body p-4">
      <h5 class="fw-bold text-center mb-3">Masuk ke Akun</h5>

      <?php if ($error): ?>
        <div class="alert alert-danger alert-dismissible fade show" role="alert">
          <i class="bi bi-exclamation-triangle me-2"></i><?= htmlspecialchars($error) ?>
          <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
      <?php endif; ?>
      <?php $flash = flash_get(); if ($flash): ?>
        <div class="alert alert-<?= htmlspecialchars($flash['type']) ?> alert-dismissible fade show" role="alert">
          <i class="bi bi-<?= $flash['type']==='success'?'check-circle':'exclamation-triangle' ?> me-2"></i><?= htmlspecialchars($flash['msg']) ?>
          <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
      <?php endif; ?>

      <form method="post" novalidate>
        <div class="mb-3">
          <label for="username" class="form-label fw-semibold"><i class="bi bi-person me-2"></i>Username</label>
          <input type="text" name="username" id="username" class="form-control" required autofocus
                 value="<?= htmlspecialchars($username) ?>" placeholder="Masukkan username">
        </div>

        <div class="mb-3">
          <label for="password" class="form-label fw-semibold"><i class="bi bi-lock me-2"></i>Password</label>
          <input type="password" name="password" id="password" class="form-control" required
                 placeholder="Masukkan password">
        </div>

        <button type="submit" class="btn btn-primary btn-login w-100 mb-3">
          <i class="bi bi-box-arrow-in-right me-2"></i>Login
        </button>
      </form>

      <div class="text-center text-muted small">
        <p class="mb-0">Belum punya akun? <a href="register.php" class="fw-semibold">Daftar sekarang</a></p>
        <p class="mt-2 mb-0"><strong>Akun Demo:</strong> <code>admin</code> / <code>admin123</code> · <code>user</code> / <code>user123</code></p>
      </div>
    </div>
  </div>
</div>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>