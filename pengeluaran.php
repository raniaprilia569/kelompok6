<?php
// File: pengeluaran.php
// Single-file CRUD untuk tabel 'pengeluaran' (MySQL) menggunakan PDO.
// Cara pakai:
// 1. Buat database MySQL (misal: kasdb)
// 2. Jalankan SQL berikut untuk membuat tabel:
/*
CREATE TABLE pengeluaran (
  id INT AUTO_INCREMENT PRIMARY KEY,
  tanggal DATE NOT NULL,
  jenis VARCHAR(191) NOT NULL,
  jumlah BIGINT NOT NULL,
  keterangan TEXT,
  created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;
*/
// 3. Sesuaikan konfigurasi DB di $config di bawah.
// 4. Simpan file ini di server (misal: /var/www/html/pengeluaran.php) dan buka di browser.

session_start();

// ---- KONFIGURASI: sesuaikan dengan server kamu ----
$config = [
    'db_host' => '127.0.0.1',
    'db_name' => 'kasdb',
    'db_user' => 'root',
    'db_pass' => '',
    'dsn' => null,
];
$config['dsn'] = "mysql:host={$config['db_host']};dbname={$config['db_name']};charset=utf8mb4";

try {
    $pdo = new PDO($config['dsn'], $config['db_user'], $config['db_pass'], [
        PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
        PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
    ]);
} catch (PDOException $e) {
    die('Koneksi DB gagal: ' . htmlspecialchars($e->getMessage()));
}

// Simple CSRF token
if (!isset($_SESSION['_csrf'])) {
    $_SESSION['_csrf'] = bin2hex(random_bytes(16));
}
function check_csrf($token) {
    return isset($token) && hash_equals($_SESSION['_csrf'], $token);
}

// Helper sanitasi input
function old($k){ return isset($_POST[$k]) ? htmlspecialchars($_POST[$k]) : ''; }

// ---- CRUD operations ----
$errors = [];
$success = null;

// Create
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action']) && $_POST['action'] === 'create') {
    if (!check_csrf($_POST['_csrf'] ?? '')) { $errors[] = 'Token CSRF tidak valid.'; }

    $tanggal = $_POST['tanggal'] ?? '';
    $jenis = trim($_POST['jenis'] ?? '');
    $jumlah = str_replace([',','.' ], ['', ''], $_POST['jumlah'] ?? '0'); // simple numeric cleanup
    $keterangan = trim($_POST['keterangan'] ?? '');

    if (!$tanggal) $errors[] = 'Tanggal harus diisi.';
    if (!$jenis) $errors[] = 'Jenis pengeluaran harus diisi.';
    if (!is_numeric($jumlah)) $errors[] = 'Jumlah harus angka.';

    if (empty($errors)) {
        $stmt = $pdo->prepare('INSERT INTO pengeluaran (tanggal, jenis, jumlah, keterangan) VALUES (?, ?, ?, ?)');
        $stmt->execute([$tanggal, $jenis, (int)$jumlah, $keterangan]);
        $success = 'Data pengeluaran berhasil ditambahkan.';
    }
}

// Update
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action']) && $_POST['action'] === 'update') {
    if (!check_csrf($_POST['_csrf'] ?? '')) { $errors[] = 'Token CSRF tidak valid.'; }

    $id = (int)($_POST['id'] ?? 0);
    $tanggal = $_POST['tanggal'] ?? '';
    $jenis = trim($_POST['jenis'] ?? '');
    $jumlah = str_replace([',','.' ], ['', ''], $_POST['jumlah'] ?? '0');
    $keterangan = trim($_POST['keterangan'] ?? '');

    if (!$id) $errors[] = 'ID tidak ditemukan.';
    if (!$tanggal) $errors[] = 'Tanggal harus diisi.';
    if (!$jenis) $errors[] = 'Jenis pengeluaran harus diisi.';
    if (!is_numeric($jumlah)) $errors[] = 'Jumlah harus angka.';

    if (empty($errors)) {
        $stmt = $pdo->prepare('UPDATE pengeluaran SET tanggal = ?, jenis = ?, jumlah = ?, keterangan = ? WHERE id = ?');
        $stmt->execute([$tanggal, $jenis, (int)$jumlah, $keterangan, $id]);
        $success = 'Data pengeluaran berhasil diperbarui.';
    }
}

// Delete
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action']) && $_POST['action'] === 'delete') {
    if (!check_csrf($_POST['_csrf'] ?? '')) { $errors[] = 'Token CSRF tidak valid.'; }
    $id = (int)($_POST['id'] ?? 0);
    if ($id) {
        $stmt = $pdo->prepare('DELETE FROM pengeluaran WHERE id = ?');
        $stmt->execute([$id]);
        $success = 'Data pengeluaran berhasil dihapus.';
    } else {
        $errors[] = 'ID tidak valid.';
    }
}

// Fetch all (with simple search & pagination optional)
$q = trim($_GET['q'] ?? '');
$page = max(1, (int)($_GET['page'] ?? 1));
$perPage = 20;
$offset = ($page - 1) * $perPage;
$params = [];
$where = '';
if ($q !== '') {
    $where = 'WHERE jenis LIKE ? OR keterangan LIKE ?';
    $params[] = "%$q%"; $params[] = "%$q%";
}
$totalStmt = $pdo->prepare("SELECT COUNT(*) FROM pengeluaran $where");
$totalStmt->execute($params);
$total = (int)$totalStmt->fetchColumn();
$pages = (int)ceil($total / $perPage);

$listStmt = $pdo->prepare("SELECT * FROM pengeluaran $where ORDER BY tanggal DESC, id DESC LIMIT $perPage OFFSET $offset");
$listStmt->execute($params);
$rows = $listStmt->fetchAll();

// If edit requested, fetch single
$edit = null;
if (isset($_GET['edit'])) {
    $id = (int)$_GET['edit'];
    if ($id) {
        $s = $pdo->prepare('SELECT * FROM pengeluaran WHERE id = ?');
        $s->execute([$id]);
        $edit = $s->fetch();
    }
}

// ---- HTML output ----
?>
<!doctype html>
<html lang="id">
<head>
<meta charset="utf-8">
<meta name="viewport" content="width=device-width,initial-scale=1">
<title>Pengeluaran — CRUD</title>
<style>
  body{font-family:Arial, sans-serif;max-width:980px;margin:20px auto;color:#111}
  h1{font-size:20px}
  form{display:grid;grid-template-columns:repeat(4,1fr);gap:8px;margin-bottom:12px}
  label{font-size:13px}
  input[type=text], input[type=date], input[type=number], textarea{padding:8px;border:1px solid #ccc;border-radius:6px;width:100%}
  textarea{grid-column:span 4}
  table{width:100%;border-collapse:collapse;margin-top:8px}
  th,td{padding:8px;border:1px solid #ddd;text-align:left}
  th{background:#f7f7f7}
  .actions{display:flex;gap:6px}
  .btn{padding:6px 10px;border-radius:6px;border:0;cursor:pointer}
  .btn-primary{background:#2563eb;color:#fff}
  .btn-danger{background:#ef4444;color:#fff}
  .muted{color:#666;font-size:13px}
  .pager{margin-top:8px}
  .error{color:#ef4444}
  .success{color:#16a34a}
  @media(max-width:700px){form{grid-template-columns:1fr}}
</style>
</head>
<body>
  <h1>Halaman Pengeluaran — CRUD</h1>
  <p class="muted">Tambah, ubah, atau hapus data pengeluaran. Data tersimpan di tabel <code>pengeluaran</code>.</p>

  <?php if ($errors): ?>
    <div class="error"><?= implode('<br>', array_map('htmlspecialchars', $errors)) ?></div>
  <?php endif; ?>
  <?php if ($success): ?>
    <div class="success"><?= htmlspecialchars($success) ?></div>
  <?php endif; ?>

  <!-- Form tambah / edit -->
  <form method="post" action="?">
    <input type="hidden" name="_csrf" value="<?= htmlspecialchars($_SESSION['_csrf']) ?>">
    <?php if ($edit): ?>
      <input type="hidden" name="action" value="update">
      <input type="hidden" name="id" value="<?= (int)$edit['id'] ?>">
    <?php else: ?>
      <input type="hidden" name="action" value="create">
    <?php endif; ?>

    <div>
      <label>Tanggal</label>
      <input type="date" name="tanggal" value="<?= htmlspecialchars($edit['tanggal'] ?? old('tanggal') ?: date('Y-m-d')) ?>" required>
    </div>
    <div>
      <label>Jenis Pengeluaran</label>
      <input type="text" name="jenis" value="<?= htmlspecialchars($edit['jenis'] ?? old('jenis')) ?>" required>
    </div>
    <div>
      <label>Jumlah (Rp)</label>
      <input type="number" name="jumlah" min="0" value="<?= htmlspecialchars($edit['jumlah'] ?? old('jumlah')) ?>" required>
    </div>
    <div>
      <label>&nbsp;</label>
      <div style="display:flex;gap:8px">
        <button class="btn btn-primary" type="submit"><?= $edit ? 'Update' : 'Tambah' ?></button>
        <?php if ($edit): ?>
          <a href="pengeluaran.php" style="align-self:center;text-decoration:none" class="muted">Batal</a>
        <?php endif; ?>
      </div>
    </div>

    <div style="grid-column:span 4">
      <label>Keterangan</label>
      <textarea name="keterangan" rows="2"><?= htmlspecialchars($edit['keterangan'] ?? old('keterangan')) ?></textarea>
    </div>
  </form>

  <!-- Search & list -->
  <form method="get" style="display:flex;gap:8px;margin-bottom:8px">
    <input type="text" name="q" placeholder="Cari jenis atau keterangan" value="<?= htmlspecialchars($q) ?>">
    <button class="btn" type="submit">Cari</button>
    <div style="margin-left:auto;align-self:center">Total: <strong><?= $total ?></strong></div>
  </form>

  <table>
    <thead>
      <tr><th>Tanggal</th><th>Jenis</th><th>Jumlah</th><th>Keterangan</th><th>Aksi</th></tr>
    </thead>
    <tbody>
      <?php if (empty($rows)): ?>
        <tr><td colspan="5" class="muted">Belum ada data.</td></tr>
      <?php else: foreach ($rows as $r): ?>
        <tr>
          <td><?= htmlspecialchars($r['tanggal']) ?></td>
          <td><?= htmlspecialchars($r['jenis']) ?></td>
          <td><?= number_format($r['jumlah'], 0, ',', '.') ?></td>
          <td><?= htmlspecialchars($r['keterangan']) ?></td>
          <td>
            <div class="actions">
              <a class="btn" href="?edit=<?= (int)$r['id'] ?>">Edit</a>
              <form method="post" action="?" onsubmit="return confirm('Hapus data ini?');" style="display:inline">
                <input type="hidden" name="_csrf" value="<?= htmlspecialchars($_SESSION['_csrf']) ?>">
                <input type="hidden" name="action" value="delete">
                <input type="hidden" name="id" value="<?= (int)$r['id'] ?>">
                <button class="btn btn-danger" type="submit">Hapus</button>
              </form>
            </div>
          </td>
        </tr>
      <?php endforeach; endif; ?>
    </tbody>
  </table>

  <div class="pager muted">
    <?php for ($p=1;$p<=$pages;$p++): ?>
      <?php if ($p == $page): ?> <strong><?= $p ?></strong>
      <?php else: ?> <a href="?page=<?= $p ?>&q=<?= urlencode($q) ?>"><?= $p ?></a>
      <?php endif; ?>
    <?php endfor; ?>
  </div>

</body>
</html>
