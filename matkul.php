<?php
session_start();
if (!isset($_SESSION['user'])) header("Location: login.php");
require 'db.php';

$msg = $_GET['msg'] ?? '';

// POST add/update
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $kode = $_POST['kode_matkul'] ?? null;
    $nama = $_POST['nama'] ?? '';
    $sks = (int)($_POST['sks'] ?? 0);
    if ($_POST['action'] === 'add') {
        $stmt = $pdo->prepare("INSERT INTO matkul (kode_matkul,nama,sks) VALUES (?,?,?)");
        $stmt->execute([$kode,$nama,$sks]);
        header("Location: matkul.php?msg=ditambahkan"); exit;
    } else {
        $stmt = $pdo->prepare("UPDATE matkul SET nama=?, sks=? WHERE kode_matkul=?");
        $stmt->execute([$nama,$sks,$kode]);
        header("Location: matkul.php?msg=diupdate"); exit;
    }
}

// delete
if (isset($_GET['delete'])) {
    $kode = $_GET['delete'];
    $stmt = $pdo->prepare("DELETE FROM matkul WHERE kode_matkul=?");
    $stmt->execute([$kode]);
    header("Location: matkul.php?msg=dihapus"); exit;
}

$edit = null;
if (isset($_GET['edit'])) {
    $stmt = $pdo->prepare("SELECT * FROM matkul WHERE kode_matkul=?");
    $stmt->execute([$_GET['edit']]);
    $edit = $stmt->fetch(PDO::FETCH_ASSOC);
}

$all = $pdo->query("SELECT * FROM matkul ORDER BY kode_matkul")->fetchAll(PDO::FETCH_ASSOC);
?>
<!doctype html>
<html>
<head>
  <meta charset="utf-8"><title>Matkul</title>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
  <link href="assets/css/custom.css" rel="stylesheet">
</head>
<body class="d-flex">
<?php include 'sidebar.php'; ?>
<div class="flex-grow-1 p-4">
  <h4>Matkul</h4>
  <?php if($msg): ?><div class="alert alert-success"><?=htmlspecialchars($msg)?></div><?php endif; ?>

  <div class="card mb-4">
    <div class="card-header bg-primary text-white">Form Matkul</div>
    <div class="card-body">
      <form method="post" class="row g-3">
        <input type="hidden" name="action" value="<?= $edit ? 'edit' : 'add' ?>">
        <div class="col-md-3">
          <label>Kode</label>
          <input type="text" name="kode_matkul" class="form-control" required value="<?= $edit ? htmlspecialchars($edit['kode_matkul']) : '' ?>" <?= $edit ? 'readonly' : '' ?>>
        </div>
        <div class="col-md-7">
          <label>Nama Matkul</label>
          <input type="text" name="nama" class="form-control" required value="<?= $edit ? htmlspecialchars($edit['nama']) : '' ?>">
        </div>
        <div class="col-md-2">
          <label>SKS</label>
          <input type="number" name="sks" min="1" class="form-control" required value="<?= $edit ? (int)$edit['sks'] : 3 ?>">
        </div>
        <div class="col-12 text-end">
          <?php if($edit): ?><a href="matkul.php" class="btn btn-secondary">Batal</a><?php endif; ?>
          <button class="btn btn-success"><?= $edit ? 'Update' : 'Simpan' ?></button>
        </div>
      </form>
    </div>
  </div>

  <div class="card">
    <div class="card-header">Daftar Matkul</div>
    <div class="card-body p-0">
      <table class="table table-striped mb-0">
        <thead><tr><th>Kode</th><th>Nama</th><th>SKS</th><th>Aksi</th></tr></thead>
        <tbody>
        <?php foreach($all as $r): ?>
          <tr>
            <td><?=htmlspecialchars($r['kode_matkul'])?></td>
            <td><?=htmlspecialchars($r['nama'])?></td>
            <td><?=htmlspecialchars($r['sks'])?></td>
            <td>
              <a href="matkul.php?edit=<?=urlencode($r['kode_matkul'])?>" class="btn btn-sm btn-primary">Edit</a>
              <a href="matkul.php?delete=<?=urlencode($r['kode_matkul'])?>" class="btn btn-sm btn-danger" onclick="return confirm('Hapus matkul?')">Hapus</a>
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
