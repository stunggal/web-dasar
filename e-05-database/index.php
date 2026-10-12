<?php
// e-05-database/index.php — controller native: konek DB via PDO, kirim data ke view
require_once __DIR__ . '/../config/database.php';

try {
    $stmt = $pdo->query("SELECT * FROM mahasiswa ORDER BY id ASC");
    $dataMahasiswa = $stmt->fetchAll();

    $stmt2 = $pdo->query("SELECT * FROM produk ORDER BY id ASC");
    $dataProduk = $stmt2->fetchAll();
} catch (PDOException $e) {
    die("Query gagal: " . htmlspecialchars($e->getMessage()));
}
?>
<!DOCTYPE html>
<html lang="id">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Data Database — web-dasar</title>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
  <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.css" rel="stylesheet">
</head>
<body class="bg-light">
<nav class="navbar navbar-expand-lg navbar-dark bg-primary sticky-top shadow-sm">
  <div class="container">
    <a class="navbar-brand fw-bold" href="../index.html"><i class="bi bi-database me-2"></i>web-dasar</a>
    <a href="../index.html" class="btn btn-outline-light btn-sm"><i class="bi bi-arrow-left me-1"></i> Home</a>
  </div>
</nav>

<div class="container py-4">
  <div class="d-flex flex-wrap justify-content-between align-items-center mb-3 gap-2">
    <div>
      <h4 class="fw-bold mb-1"><i class="bi bi-table me-2"></i>Data dari Database Lokal</h4>
      <p class="text-muted small mb-0">DB <code>web_dasar</code> • koneksi PDO <code>config/database.php</code> • data dikirim dari PHP ke view</p>
    </div>
    <span class="badge bg-success"><i class="bi bi-check-circle me-1"></i> PDO Connected</span>
  </div>

  <div class="card border-0 shadow-sm mb-4">
    <div class="card-header bg-white d-flex justify-content-between align-items-center">
      <span class="fw-bold"><i class="bi bi-people me-2"></i>Mahasiswa (<?= count($dataMahasiswa) ?>)</span>
      <small class="text-muted">tabel <code>mahasiswa</code></small>
    </div>
    <div class="table-responsive">
      <table class="table table-hover align-middle mb-0">
        <thead class="table-light">
          <tr>
            <th style="width:60px">ID</th>
            <th>Nama</th>
            <th>Email</th>
            <th>Jurusan</th>
          </tr>
        </thead>
        <tbody>
          <?php if (!empty($dataMahasiswa)): ?>
            <?php foreach ($dataMahasiswa as $row): ?>
            <tr>
              <td><span class="badge bg-light text-dark border"><?= htmlspecialchars($row['id']) ?></span></td>
              <td class="fw-semibold"><?= htmlspecialchars($row['nama']) ?></td>
              <td><a href="mailto:<?= htmlspecialchars($row['email']) ?>"><?= htmlspecialchars($row['email']) ?></a></td>
              <td><span class="badge bg-primary bg-opacity-10 text-primary border"><?= htmlspecialchars($row['jurusan']) ?></span></td>
            </tr>
            <?php endforeach; ?>
          <?php else: ?>
            <tr><td colspan="4" class="text-center text-muted py-4">Tidak ada data mahasiswa.</td></tr>
          <?php endif; ?>
        </tbody>
      </table>
    </div>
  </div>

  <div class="card border-0 shadow-sm">
    <div class="card-header bg-white d-flex justify-content-between align-items-center">
      <span class="fw-bold"><i class="bi bi-box-seam me-2"></i>Produk (<?= count($dataProduk) ?>)</span>
      <small class="text-muted">tabel <code>produk</code></small>
    </div>
    <div class="table-responsive">
      <table class="table table-hover align-middle mb-0">
        <thead class="table-light">
          <tr>
            <th>ID</th>
            <th>Nama</th>
            <th>SKU</th>
            <th>Kategori</th>
            <th class="text-end">Harga</th>
            <th class="text-center">Stok</th>
          </tr>
        </thead>
        <tbody>
          <?php if (!empty($dataProduk)): ?>
            <?php foreach ($dataProduk as $row): ?>
            <tr>
              <td><span class="badge bg-light text-dark border"><?= htmlspecialchars($row['id']) ?></span></td>
              <td class="fw-semibold"><?= htmlspecialchars($row['nama']) ?></td>
              <td><code><?= htmlspecialchars($row['sku']) ?></code></td>
              <td><span class="badge bg-light text-dark border"><?= htmlspecialchars($row['kategori']) ?></span></td>
              <td class="text-end">Rp <?= number_format((int)$row['harga'], 0, ',', '.') ?></td>
              <td class="text-center">
                <?php if ((int)$row['stok'] === 0): ?>
                  <span class="badge bg-danger">Habis</span>
                <?php elseif ((int)$row['stok'] <= 5): ?>
                  <span class="badge bg-warning text-dark"><?= htmlspecialchars($row['stok']) ?> menipis</span>
                <?php else: ?>
                  <span class="badge bg-success"><?= htmlspecialchars($row['stok']) ?></span>
                <?php endif; ?>
              </td>
            </tr>
            <?php endforeach; ?>
          <?php else: ?>
            <tr><td colspan="6" class="text-center text-muted py-4">Tidak ada data produk.</td></tr>
          <?php endif; ?>
        </tbody>
      </table>
    </div>
    <div class="card-footer bg-white text-muted small">Menampilkan <?= count($dataProduk) ?> produk • harga format Rp, stok badge otomatis</div>
  </div>

  <div class="alert alert-light border mt-3 small">
    <i class="bi bi-code-square me-1"></i> Alur native: <code>config/database.php</code> (PDO) → <code>e-05-database/index.php</code> (<code>$pdo-&gt;query()</code> → <code>$dataMahasiswa/$dataProduk</code>) → HTML view <code>foreach</code> + <code>htmlspecialchars()</code>.
  </div>
</div>

<footer class="bg-white border-top py-3 mt-4"><div class="container text-center text-muted small">&copy; 2026 web-dasar • PHP Native PDO • Bootstrap 5.3</div></footer>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
