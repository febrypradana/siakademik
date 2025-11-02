<?php
session_start();
if (!isset($_SESSION['user'])) header("Location: login.php");
require 'db.php';
$msg = $_GET['msg'] ?? '';

// POST add/update
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $nim = $_POST['nim'] ?? null;
    $nama = $_POST['nama'] ?? '';
    $gender = $_POST['gender'] ?? '';
    $no_hp = $_POST['no_hp'] ?? '';
    $nidn = $_POST['nidn'] ?: null; // allow null

    if ($_POST['action'] === 'add') {
        $stmt = $pdo->prepare("INSERT INTO mahasiswa (nim,nama,gender,no_hp,nidn) VALUES (?,?,?,?,?)");
        $stmt->execute([$nim,$nama,$gender,$no_hp,$nidn]);
        header("Location: mahasiswa.php?msg=ditambahkan"); exit;
    } else {
        $stmt = $pdo->prepare("UPDATE mahasiswa SET nama=?, gender=?, no_hp=?, nidn=? WHERE nim=?");
        $stmt->execute([$nama,$gender,$no_hp,$nidn,$nim]);
        header("Location: mahasiswa.php?msg=diupdate"); exit;
    }
}

// delete
if (isset($_GET['delete'])) {
    $nim = $_GET['delete'];
    $stmt = $pdo->prepare("DELETE FROM mahasiswa WHERE nim=?");
    $stmt->execute([$nim]);
    header("Location: mahasiswa.php?msg=dihapus"); exit;
}

$edit = null;
if (isset($_GET['edit'])) {
    $stmt = $pdo->prepare("SELECT * FROM mahasiswa WHERE nim=?");
    $stmt->execute([$_GET['edit']]);
    $edit = $stmt->fetch(PDO::FETCH_ASSOC);
}

// fetch dosen list for dropdown
$dosenList = $pdo->query("SELECT nidn,nama FROM dosen ORDER BY nidn")->fetchAll(PDO::FETCH_ASSOC);
$all = $pdo->query("SELECT m.*, d.nama AS pembimbing FROM mahasiswa m LEFT JOIN dosen d ON m.nidn = d.nidn ORDER BY nim")->fetchAll(PDO::FETCH_ASSOC);
?>
<!doctype html>
<html>
<head><meta charset="utf-8"><title>Mahasiswa</title>
<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
<link href="assets/css/custom.css" rel="stylesheet">
</head>
<body class="d-flex">
<?php include 'sidebar.php'; ?>
<div class="flex-grow-1 p-4">
  <h4>Mahasiswa</h4>
  <?php if($msg): ?><div class="alert alert-success"><?=htmlspecialchars($msg)?></div><?php endif; ?>

  <div class="card mb-4">
    <div class="card-header bg-primary text-white">Form Mahasiswa</div>
    <div class="card-body">
      <form method="post" class="row g-3">
        <input type="hidden" name="action" value="<?= $edit ? 'edit' : 'add' ?>">
        <div class="col-md-3">
          <label>NIM</label>
          <input type="text" name="nim" class="form-control" required value="<?= $edit ? htmlspecialchars($edit['nim']) : '' ?>" <?= $edit ? 'readonly' : '' ?>>
        </div>
        <div class="col-md-5">
          <label>Nama</label>
          <input type="text" name="nama" class="form-control" required value="<?= $edit ? htmlspecialchars($edit['nama']) : '' ?>">
        </div>
        <div class="col-md-2">
          <label>Gender</label>
          <select name="gender" class="form-select" required>
            <option value="">Pilih...</option>
            <option value="L" <?= $edit && $edit['gender']=='L' ? 'selected' : '' ?>>L</option>
            <option value="P" <?= $edit && $edit['gender']=='P' ? 'selected' : '' ?>>P</option>
          </select>
        </div>
        <div class="col-md-2">
          <label>No HP</label>
          <input type="text" name="no_hp" class="form-control" value="<?= $edit ? htmlspecialchars($edit['no_hp']) : '' ?>">
        </div>

        <div class="col-md-4">
          <label>Pembimbing (NIDN)</label>
          <select name="nidn" class="form-select">
            <option value="">-- Tidak ada --</option>
            <?php foreach($dosenList as $d): ?>
              <option value="<?=htmlspecialchars($d['nidn'])?>" <?= $edit && $edit['nidn']==$d['nidn'] ? 'selected' : '' ?>>
                <?=htmlspecialchars($d['nidn'].' - '.$d['nama'])?>
              </option>
            <?php endforeach; ?>
          </select>
        </div>

        <div class="col-12 text-end">
          <?php if($edit): ?><a href="mahasiswa.php" class="btn btn-secondary">Batal</a><?php endif; ?>
          <button class="btn btn-success"><?= $edit ? 'Update' : 'Simpan' ?></button>
        </div>
      </form>
    </div>
  </div>

  <div class="card">
    <div class="card-header">Daftar Mahasiswa</div>
    <div class="card-body p-0">
      <table class="table table-striped mb-0">
        <thead><tr><th>NIM</th><th>Nama</th><th>Gender</th><th>No HP</th><th>Pembimbing</th><th>Aksi</th></tr></thead>
        <tbody>
        <?php foreach($all as $r): ?>
          <tr>
            <td><?=htmlspecialchars($r['nim'])?></td>
            <td><?=htmlspecialchars($r['nama'])?></td>
            <td><?=htmlspecialchars($r['gender'])?></td>
            <td><?=htmlspecialchars($r['no_hp'])?></td>
            <td><?=htmlspecialchars($r['pembimbing']??'-')?></td>
            <td>
              <a href="mahasiswa.php?edit=<?=urlencode($r['nim'])?>" class="btn btn-sm btn-primary">Edit</a>
              <a href="mahasiswa.php?delete=<?=urlencode($r['nim'])?>" class="btn btn-sm btn-danger" onclick="return confirm('Hapus mahasiswa?')">Hapus</a>
            </td>
          </tr>
        <?php endforeach; ?>
        </tbody>
      </table>
    </div>
  </div>

</div>
</body>
</html>
