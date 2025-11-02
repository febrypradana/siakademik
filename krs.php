<?php
session_start();
if (!isset($_SESSION['user'])) header("Location: login.php");
require 'db.php';
$msg = $_GET['msg'] ?? '';

// ADD entry KRS
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $nim = $_POST['nim'] ?? '';
    $kode = $_POST['kode_matkul'] ?? '';
    // guard duplicate via UNIQUE constraint (catch exception)
    try {
        $stmt = $pdo->prepare("INSERT INTO krs (nim, kode_matkul) VALUES (?,?)");
        $stmt->execute([$nim,$kode]);
        header("Location: krs.php?msg=ditambahkan"); exit;
    } catch (PDOException $e) {
        header("Location: krs.php?msg=gagal"); exit;
    }
}

// delete
if (isset($_GET['delete'])) {
    $id = (int)$_GET['delete'];
    $stmt = $pdo->prepare("DELETE FROM krs WHERE kode = ?");
    $stmt->execute([$id]);
    header("Location: krs.php?msg=dihapus"); exit;
}

// lists
$students = $pdo->query("SELECT nim, nama FROM mahasiswa ORDER BY nim")->fetchAll(PDO::FETCH_ASSOC);
$matkuls = $pdo->query("SELECT kode_matkul, nama, sks FROM matkul ORDER BY kode_matkul")->fetchAll(PDO::FETCH_ASSOC);
$krsList = $pdo->query("SELECT k.*, m.nama AS mahasiswa, mk.nama AS matkul, mk.sks FROM krs k JOIN mahasiswa m ON k.nim=m.nim JOIN matkul mk ON k.kode_matkul=mk.kode_matkul ORDER BY m.nim")->fetchAll(PDO::FETCH_ASSOC);
?>
<!doctype html>
<html>
<head><meta charset="utf-8"><title>KRS</title>
<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
<link href="assets/css/custom.css" rel="stylesheet">
</head>
<body class="d-flex">
<?php include 'sidebar.php'; ?>
<div class="flex-grow-1 p-4">
  <h4>KRS</h4>
  <?php if($msg): ?><div class="alert alert-success"><?=htmlspecialchars($msg)?></div><?php endif; ?>

  <div class="card mb-4">
    <div class="card-header bg-primary text-white">Tambah KRS</div>
    <div class="card-body">
      <form method="post" class="row g-3">
        <div class="col-md-4">
          <label>Mahasiswa</label>
          <select name="nim" class="form-select" required>
            <option value="">Pilih mahasiswa</option>
            <?php foreach($students as $s): ?>
              <option value="<?=htmlspecialchars($s['nim'])?>"><?=htmlspecialchars($s['nim'].' - '.$s['nama'])?></option>
            <?php endforeach; ?>
          </select>
        </div>
        <div class="col-md-4">
          <label>Matkul</label>
          <select name="kode_matkul" class="form-select" required>
            <option value="">Pilih matkul</option>
            <?php foreach($matkuls as $m): ?>
              <option value="<?=htmlspecialchars($m['kode_matkul'])?>"><?=htmlspecialchars($m['kode_matkul'].' - '.$m['nama'].' ('.$m['sks'].' sks)')?></option>
            <?php endforeach; ?>
          </select>
        </div>
        <div class="col-md-4 d-flex align-items-end">
          <button class="btn btn-success">Tambah</button>
        </div>
      </form>
    </div>
  </div>

  <div class="card">
    <div class="card-header">Daftar KRS</div>
    <div class="card-body p-0">
      <table class="table table-striped mb-0">
        <thead><tr><th>#</th><th>NIM</th><th>Mahasiswa</th><th>Kode</th><th>Matkul</th><th>SKS</th><th>Aksi</th></tr></thead>
        <tbody>
        <?php foreach($krsList as $k): ?>
          <tr>
            <td><?=htmlspecialchars($k['kode'])?></td>
            <td><?=htmlspecialchars($k['nim'])?></td>
            <td><?=htmlspecialchars($k['mahasiswa'])?></td>
            <td><?=htmlspecialchars($k['kode_matkul'])?></td>
            <td><?=htmlspecialchars($k['matkul'])?></td>
            <td><?=htmlspecialchars($k['sks'])?></td>
            <td>
              <a href="krs.php?delete=<?=urlencode($k['kode'])?>" class="btn btn-sm btn-danger" onclick="return confirm('Hapus entry ini?')">Hapus</a>
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
