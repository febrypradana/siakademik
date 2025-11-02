<?php
session_start();
if (!isset($_SESSION['user'])) header("Location: login.php");
require 'db.php';

$msg = $_GET['msg'] ?? '';

// HANDLE POST ADD / UPDATE
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $nidn = $_POST['nidn'] ?? null;
    $nama = $_POST['nama'] ?? '';
    $gender = $_POST['gender'] ?? '';
    $no_hp = $_POST['no_hp'] ?? '';

    if (isset($_POST['action']) && $_POST['action'] === 'add') {
        $stmt = $pdo->prepare("INSERT INTO dosen (nidn,nama,gender,no_hp) VALUES (?,?,?,?)");
        $stmt->execute([$nidn, $nama, $gender, $no_hp]);
        header("Location: dosen.php?msg=ditambahkan");
        exit;
    } elseif (isset($_POST['action']) && $_POST['action'] === 'edit') {
        $stmt = $pdo->prepare("UPDATE dosen SET nama=?, gender=?, no_hp=? WHERE nidn=?");
        $stmt->execute([$nama, $gender, $no_hp, $nidn]);
        header("Location: dosen.php?msg=diupdate");
        exit;
    }
}

// HANDLE DELETE
if (isset($_GET['delete'])) {
    $nidn = $_GET['delete'];
    $stmt = $pdo->prepare("DELETE FROM dosen WHERE nidn = ?");
    $stmt->execute([$nidn]);
    header("Location: dosen.php?msg=dihapus");
    exit;
}

// for edit form prefill
$edit = null;
if (isset($_GET['edit'])) {
    $nidn = $_GET['edit'];
    $stmt = $pdo->prepare("SELECT * FROM dosen WHERE nidn = ?");
    $stmt->execute([$nidn]);
    $edit = $stmt->fetch(PDO::FETCH_ASSOC);
}

// fetch all dosen
$stmt = $pdo->query("SELECT * FROM dosen ORDER BY nidn");
$dosenAll = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>
<!doctype html>
<html>
<head>
  <meta charset="utf-8">
  <title>Dosen - Sistem Akademik</title>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
  <link href="assets/css/custom.css" rel="stylesheet">
</head>
<body class="d-flex">
  <?php include 'sidebar.php'; ?>
  <div class="flex-grow-1 p-4">
    <h4>Dosen</h4>
    <?php if($msg): ?>
      <div class="alert alert-success"><?=htmlspecialchars($msg)?></div>
    <?php endif; ?>

    <div class="card mb-4">
      <div class="card-header bg-primary text-white">Form Input / Edit Dosen</div>
      <div class="card-body">
        <form method="post" class="row g-3">
          <input type="hidden" name="action" value="<?= $edit ? 'edit' : 'add' ?>">
          <div class="col-md-3">
            <label>NIDN</label>
            <input type="text" name="nidn" class="form-control" required value="<?= $edit ? htmlspecialchars($edit['nidn']) : '' ?>" <?= $edit ? 'readonly' : '' ?>>
          </div>
          <div class="col-md-5">
            <label>Nama Dosen</label>
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
          <div class="col-12 text-end">
            <?php if($edit): ?>
              <a href="dosen.php" class="btn btn-secondary">Batal</a>
            <?php endif; ?>
            <button class="btn btn-success"><?= $edit ? 'Update' : 'Simpan' ?></button>
          </div>
        </form>
      </div>
    </div>

    <div class="card">
      <div class="card-header">Daftar Dosen</div>
      <div class="card-body p-0">
        <table class="table table-striped mb-0">
          <thead><tr><th>NIDN</th><th>Nama</th><th>Gender</th><th>No HP</th><th>Aksi</th></tr></thead>
          <tbody>
          <?php foreach($dosenAll as $d): ?>
            <tr>
              <td><?=htmlspecialchars($d['nidn'])?></td>
              <td><?=htmlspecialchars($d['nama'])?></td>
              <td><?=htmlspecialchars($d['gender'])?></td>
              <td><?=htmlspecialchars($d['no_hp'])?></td>
              <td>
                <a href="dosen.php?edit=<?=urlencode($d['nidn'])?>" class="btn btn-sm btn-primary">Edit</a>
                <a href="dosen.php?delete=<?=urlencode($d['nidn'])?>" class="btn btn-sm btn-danger" onclick="return confirm('Hapus dosen ini?')">Hapus</a>
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
