<?php
// w-06-crud/index.php — base dari e-05-database + CRUD mahasiswa/produk (create/update/delete)
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
session_start();
try {
    $pdo = new PDO($dsn, $username, $password, $options);
} catch (PDOException $e) {
    die("Koneksi gagal: " . htmlspecialchars($e->getMessage()));
}

// Flash helper
function flash_set($type, $msg){ $_SESSION['flash'] = ['type'=>$type,'msg'=>$msg]; }
function flash_get(){ $f=$_SESSION['flash']??null; unset($_SESSION['flash']); return $f; }

// Handle POST actions
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $aksi = $_POST['aksi'] ?? '';
    $tabel = $_POST['tabel'] ?? '';
    try {
        if ($tabel === 'mahasiswa') {
            if ($aksi === 'create') {
                $stmt = $pdo->prepare("INSERT INTO mahasiswa (nama,email,jurusan) VALUES (?,?,?)");
                $stmt->execute([trim($_POST['nama']), trim($_POST['email']), trim($_POST['jurusan'])]);
                flash_set('success', 'Mahasiswa berhasil ditambahkan.');
            } elseif ($aksi === 'update') {
                $stmt = $pdo->prepare("UPDATE mahasiswa SET nama=?, email=?, jurusan=? WHERE id=?");
                $stmt->execute([trim($_POST['nama']), trim($_POST['email']), trim($_POST['jurusan']), (int)$_POST['id']]);
                flash_set('success', 'Mahasiswa berhasil diupdate.');
            } elseif ($aksi === 'delete') {
                $stmt = $pdo->prepare("DELETE FROM mahasiswa WHERE id=?");
                $stmt->execute([(int)$_POST['id']]);
                flash_set('success', 'Mahasiswa dihapus.');
            }
        } elseif ($tabel === 'produk') {
            if ($aksi === 'create') {
                $stmt = $pdo->prepare("INSERT INTO produk (nama,sku,kategori,harga,stok,deskripsi) VALUES (?,?,?,?,?,?)");
                $stmt->execute([trim($_POST['nama']), trim($_POST['sku']), trim($_POST['kategori']), (int)$_POST['harga'], (int)$_POST['stok'], trim($_POST['deskripsi'] ?? '')]);
                flash_set('success', 'Produk berhasil ditambahkan.');
            } elseif ($aksi === 'update') {
                $stmt = $pdo->prepare("UPDATE produk SET nama=?, sku=?, kategori=?, harga=?, stok=?, deskripsi=? WHERE id=?");
                $stmt->execute([trim($_POST['nama']), trim($_POST['sku']), trim($_POST['kategori']), (int)$_POST['harga'], (int)$_POST['stok'], trim($_POST['deskripsi'] ?? ''), (int)$_POST['id']]);
                flash_set('success', 'Produk berhasil diupdate.');
            } elseif ($aksi === 'delete') {
                $stmt = $pdo->prepare("DELETE FROM produk WHERE id=?");
                $stmt->execute([(int)$_POST['id']]);
                flash_set('success', 'Produk dihapus.');
            }
        }
    } catch (PDOException $e) {
        // Duplicate sku etc
        flash_set('danger', 'Gagal: ' . $e->getMessage());
    }
    header("Location: index.php");
    exit;
}

try {
    $dataMahasiswa = $pdo->query("SELECT * FROM mahasiswa ORDER BY id ASC")->fetchAll();
    $dataProduk = $pdo->query("SELECT * FROM produk ORDER BY id ASC")->fetchAll();
} catch (PDOException $e) {
    die("Query gagal: " . htmlspecialchars($e->getMessage()));
}
$flash = flash_get();
?>
<!DOCTYPE html>
<html lang="id">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>w-06-crud — Web Dasar</title>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
  <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.css" rel="stylesheet">
</head>
<body class="bg-light">
<nav class="navbar navbar-expand-lg navbar-dark bg-primary sticky-top shadow-sm">
  <div class="container">
    <a class="navbar-brand fw-bold" href="../index.html"><i class="bi bi-pencil-square me-2"></i>w-06-crud</a>
    <a href="../index.html" class="btn btn-outline-light btn-sm"><i class="bi bi-arrow-left me-1"></i> Home</a>
  </div>
</nav>

<div class="container py-4">
  <div class="d-flex flex-wrap justify-content-between align-items-center mb-3 gap-2">
    <div>
      <h4 class="fw-bold mb-1"><i class="bi bi-database-gear me-2"></i>CRUD — Mahasiswa & Produk</h4>
      <p class="text-muted small mb-0">Base dari <code>e-05-database</code> • PDO • POST + PRG • <code>web_dasar</code></p>
    </div>
    <span class="badge bg-success"><i class="bi bi-check-circle me-1"></i> PDO Connected</span>
  </div>

  <?php if ($flash): ?>
    <div class="alert alert-<?= htmlspecialchars($flash['type']) ?> alert-dismissible fade show"><i class="bi bi-<?= $flash['type']==='success'?'check-circle':'exclamation-triangle' ?> me-1"></i> <?= htmlspecialchars($flash['msg']) ?> <button type="button" class="btn-close" data-bs-dismiss="alert"></button></div>
  <?php endif; ?>

  <!-- Mahasiswa -->
  <div class="card border-0 shadow-sm mb-4">
    <div class="card-header bg-white d-flex flex-wrap justify-content-between align-items-center gap-2">
      <span class="fw-bold"><i class="bi bi-people me-2"></i>Mahasiswa (<?= count($dataMahasiswa) ?>)</span>
      <div class="d-flex gap-2">
        <small class="text-muted align-self-center">tabel <code>mahasiswa</code></small>
        <button class="btn btn-sm btn-primary" data-bs-toggle="modal" data-bs-target="#mhsCreate"><i class="bi bi-plus-lg me-1"></i> Tambah</button>
      </div>
    </div>
    <div class="table-responsive">
      <table class="table table-hover align-middle mb-0">
        <thead class="table-light"><tr><th style="width:60px">ID</th><th>Nama</th><th>Email</th><th>Jurusan</th><th style="width:160px" class="text-end">Aksi</th></tr></thead>
        <tbody>
          <?php if (!empty($dataMahasiswa)): foreach ($dataMahasiswa as $row): ?>
            <tr>
              <td><span class="badge bg-light text-dark border"><?= htmlspecialchars($row['id']) ?></span></td>
              <td class="fw-semibold"><?= htmlspecialchars($row['nama']) ?></td>
              <td><a href="mailto:<?= htmlspecialchars($row['email']) ?>"><?= htmlspecialchars($row['email']) ?></a></td>
              <td><span class="badge bg-primary bg-opacity-10 text-primary border"><?= htmlspecialchars($row['jurusan']) ?></span></td>
              <td class="text-end">
                <button class="btn btn-sm btn-outline-warning" data-bs-toggle="modal" data-bs-target="#mhsEdit<?= $row['id'] ?>"><i class="bi bi-pencil"></i></button>
                <button class="btn btn-sm btn-outline-danger" data-bs-toggle="modal" data-bs-target="#mhsDel<?= $row['id'] ?>"><i class="bi bi-trash"></i></button>
              </td>
            </tr>
          <?php endforeach; else: ?>
            <tr><td colspan="5" class="text-center text-muted py-4">Tidak ada data mahasiswa. Klik Tambah.</td></tr>
          <?php endif; ?>
        </tbody>
      </table>
    </div>
  </div>

  <!-- Produk -->
  <div class="card border-0 shadow-sm">
    <div class="card-header bg-white d-flex flex-wrap justify-content-between align-items-center gap-2">
      <span class="fw-bold"><i class="bi bi-box-seam me-2"></i>Produk (<?= count($dataProduk) ?>)</span>
      <div class="d-flex gap-2">
        <small class="text-muted align-self-center">tabel <code>produk</code></small>
        <button class="btn btn-sm btn-primary" data-bs-toggle="modal" data-bs-target="#prdCreate"><i class="bi bi-plus-lg me-1"></i> Tambah</button>
      </div>
    </div>
    <div class="table-responsive">
      <table class="table table-hover align-middle mb-0">
        <thead class="table-light"><tr><th>ID</th><th>Nama</th><th>SKU</th><th>Kategori</th><th class="text-end">Harga</th><th class="text-center">Stok</th><th class="text-end">Aksi</th></tr></thead>
        <tbody>
          <?php if (!empty($dataProduk)): foreach ($dataProduk as $row): ?>
            <tr>
              <td><span class="badge bg-light text-dark border"><?= htmlspecialchars($row['id']) ?></span></td>
              <td class="fw-semibold"><?= htmlspecialchars($row['nama']) ?></td>
              <td><code><?= htmlspecialchars($row['sku']) ?></code></td>
              <td><span class="badge bg-light text-dark border"><?= htmlspecialchars($row['kategori']) ?></span></td>
              <td class="text-end">Rp <?= number_format((int)$row['harga'], 0, ',', '.') ?></td>
              <td class="text-center">
                <?php if ((int)$row['stok'] === 0): ?><span class="badge bg-danger">Habis</span>
                <?php elseif ((int)$row['stok'] <= 5): ?><span class="badge bg-warning text-dark"><?= htmlspecialchars($row['stok']) ?> menipis</span>
                <?php else: ?><span class="badge bg-success"><?= htmlspecialchars($row['stok']) ?></span><?php endif; ?>
              </td>
              <td class="text-end">
                <button class="btn btn-sm btn-outline-warning" data-bs-toggle="modal" data-bs-target="#prdEdit<?= $row['id'] ?>"><i class="bi bi-pencil"></i></button>
                <button class="btn btn-sm btn-outline-danger" data-bs-toggle="modal" data-bs-target="#prdDel<?= $row['id'] ?>"><i class="bi bi-trash"></i></button>
              </td>
            </tr>
          <?php endforeach; else: ?>
            <tr><td colspan="7" class="text-center text-muted py-4">Tidak ada data produk.</td></tr>
          <?php endif; ?>
        </tbody>
      </table>
    </div>
    <div class="card-footer bg-white text-muted small">Menampilkan <?= count($dataProduk) ?> produk • harga format Rp, stok badge otomatis</div>
  </div>
</div>

<!-- Modals Mahasiswa Create -->
<div class="modal fade" id="mhsCreate" tabindex="-1"><div class="modal-dialog"><form method="post" class="modal-content"><div class="modal-header"><h6 class="modal-title fw-bold">Tambah Mahasiswa</h6><button type="button" class="btn-close" data-bs-dismiss="modal"></button></div><div class="modal-body">
  <input type="hidden" name="tabel" value="mahasiswa"><input type="hidden" name="aksi" value="create">
  <div class="mb-3"><label class="form-label">Nama *</label><input name="nama" class="form-control" required></div>
  <div class="mb-3"><label class="form-label">Email *</label><input name="email" type="email" class="form-control" required></div>
  <div class="mb-3"><label class="form-label">Jurusan *</label><input name="jurusan" class="form-control" required placeholder="Teknik Informatika"></div>
</div><div class="modal-footer"><button type="button" class="btn btn-light" data-bs-dismiss="modal">Batal</button><button type="submit" class="btn btn-primary">Simpan</button></div></form></div></div>

<?php foreach ($dataMahasiswa as $row): ?>
<div class="modal fade" id="mhsEdit<?= $row['id'] ?>" tabindex="-1"><div class="modal-dialog"><form method="post" class="modal-content"><div class="modal-header"><h6 class="modal-title fw-bold">Edit Mahasiswa #<?= htmlspecialchars($row['id']) ?></h6><button type="button" class="btn-close" data-bs-dismiss="modal"></button></div><div class="modal-body">
  <input type="hidden" name="tabel" value="mahasiswa"><input type="hidden" name="aksi" value="update"><input type="hidden" name="id" value="<?= htmlspecialchars($row['id']) ?>">
  <div class="mb-3"><label class="form-label">Nama *</label><input name="nama" class="form-control" required value="<?= htmlspecialchars($row['nama']) ?>"></div>
  <div class="mb-3"><label class="form-label">Email *</label><input name="email" type="email" class="form-control" required value="<?= htmlspecialchars($row['email']) ?>"></div>
  <div class="mb-3"><label class="form-label">Jurusan *</label><input name="jurusan" class="form-control" required value="<?= htmlspecialchars($row['jurusan']) ?>"></div>
</div><div class="modal-footer"><button type="button" class="btn btn-light" data-bs-dismiss="modal">Batal</button><button type="submit" class="btn btn-warning">Update</button></div></form></div></div>

<div class="modal fade" id="mhsDel<?= $row['id'] ?>" tabindex="-1"><div class="modal-dialog modal-sm"><form method="post" class="modal-content"><div class="modal-header"><h6 class="modal-title">Hapus?</h6><button type="button" class="btn-close" data-bs-dismiss="modal"></button></div><div class="modal-body"><p class="mb-0">Yakin hapus <b><?= htmlspecialchars($row['nama']) ?></b>?</p><input type="hidden" name="tabel" value="mahasiswa"><input type="hidden" name="aksi" value="delete"><input type="hidden" name="id" value="<?= htmlspecialchars($row['id']) ?>"></div><div class="modal-footer"><button type="button" class="btn btn-light" data-bs-dismiss="modal">Batal</button><button type="submit" class="btn btn-danger">Hapus</button></div></form></div></div>
<?php endforeach; ?>

<!-- Modals Produk Create -->
<div class="modal fade" id="prdCreate" tabindex="-1"><div class="modal-dialog"><form method="post" class="modal-content"><div class="modal-header"><h6 class="modal-title fw-bold">Tambah Produk</h6><button type="button" class="btn-close" data-bs-dismiss="modal"></button></div><div class="modal-body">
  <input type="hidden" name="tabel" value="produk"><input type="hidden" name="aksi" value="create">
  <div class="mb-3"><label class="form-label">Nama *</label><input name="nama" class="form-control" required></div>
  <div class="row g-2 mb-3"><div class="col-6"><label class="form-label">SKU *</label><input name="sku" class="form-control" required placeholder="SKU-006"></div><div class="col-6"><label class="form-label">Kategori *</label><input name="kategori" class="form-control" required placeholder="Pakaian"></div></div>
  <div class="row g-2 mb-3"><div class="col-6"><label class="form-label">Harga *</label><input name="harga" type="number" min="0" class="form-control" required></div><div class="col-6"><label class="form-label">Stok *</label><input name="stok" type="number" min="0" class="form-control" required></div></div>
  <div class="mb-3"><label class="form-label">Deskripsi</label><textarea name="deskripsi" class="form-control" rows="2"></textarea></div>
</div><div class="modal-footer"><button type="button" class="btn btn-light" data-bs-dismiss="modal">Batal</button><button type="submit" class="btn btn-primary">Simpan</button></div></form></div></div>

<?php foreach ($dataProduk as $row): ?>
<div class="modal fade" id="prdEdit<?= $row['id'] ?>" tabindex="-1"><div class="modal-dialog"><form method="post" class="modal-content"><div class="modal-header"><h6 class="modal-title fw-bold">Edit Produk #<?= htmlspecialchars($row['id']) ?></h6><button type="button" class="btn-close" data-bs-dismiss="modal"></button></div><div class="modal-body">
  <input type="hidden" name="tabel" value="produk"><input type="hidden" name="aksi" value="update"><input type="hidden" name="id" value="<?= htmlspecialchars($row['id']) ?>">
  <div class="mb-3"><label class="form-label">Nama *</label><input name="nama" class="form-control" required value="<?= htmlspecialchars($row['nama']) ?>"></div>
  <div class="row g-2 mb-3"><div class="col-6"><label class="form-label">SKU *</label><input name="sku" class="form-control" required value="<?= htmlspecialchars($row['sku']) ?>"></div><div class="col-6"><label class="form-label">Kategori *</label><input name="kategori" class="form-control" required value="<?= htmlspecialchars($row['kategori']) ?>"></div></div>
  <div class="row g-2 mb-3"><div class="col-6"><label class="form-label">Harga *</label><input name="harga" type="number" min="0" class="form-control" required value="<?= htmlspecialchars($row['harga']) ?>"></div><div class="col-6"><label class="form-label">Stok *</label><input name="stok" type="number" min="0" class="form-control" required value="<?= htmlspecialchars($row['stok']) ?>"></div></div>
  <div class="mb-3"><label class="form-label">Deskripsi</label><textarea name="deskripsi" class="form-control" rows="2"><?= htmlspecialchars($row['deskripsi']) ?></textarea></div>
</div><div class="modal-footer"><button type="button" class="btn btn-light" data-bs-dismiss="modal">Batal</button><button type="submit" class="btn btn-warning">Update</button></div></form></div></div>

<div class="modal fade" id="prdDel<?= $row['id'] ?>" tabindex="-1"><div class="modal-dialog modal-sm"><form method="post" class="modal-content"><div class="modal-header"><h6 class="modal-title">Hapus?</h6><button type="button" class="btn-close" data-bs-dismiss="modal"></button></div><div class="modal-body"><p class="mb-0">Yakin hapus <b><?= htmlspecialchars($row['nama']) ?></b>?</p><input type="hidden" name="tabel" value="produk"><input type="hidden" name="aksi" value="delete"><input type="hidden" name="id" value="<?= htmlspecialchars($row['id']) ?>"></div><div class="modal-footer"><button type="button" class="btn btn-light" data-bs-dismiss="modal">Batal</button><button type="submit" class="btn btn-danger">Hapus</button></div></form></div></div>
<?php endforeach; ?>

<footer class="bg-white border-top py-3 mt-4"><div class="container text-center text-muted small">&copy; 2026 web-dasar • w-06-crud • PDO native</div></footer>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
