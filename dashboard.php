<?php
session_start();
if (!isset($_SESSION['user'])) {
    header("Location: login.php");
    exit;
}

require 'db.php';

// Hitung total data
$jumlahDosen = $pdo->query("SELECT COUNT(*) FROM dosen")->fetchColumn();
$jumlahMhs   = $pdo->query("SELECT COUNT(*) FROM mahasiswa")->fetchColumn();
$jumlahMatkul= $pdo->query("SELECT COUNT(*) FROM matkul")->fetchColumn();
$jumlahKrs   = $pdo->query("SELECT COUNT(*) FROM krs")->fetchColumn();

// Statistik tambahan
$mhsL = $pdo->query("SELECT COUNT(*) FROM mahasiswa WHERE gender='L'")->fetchColumn();
$mhsP = $pdo->query("SELECT COUNT(*) FROM mahasiswa WHERE gender='P'")->fetchColumn();
?>
<!DOCTYPE html>
<html lang="id">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1">
<title>Dashboard - Sistem Akademik</title>
<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
<link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.css" rel="stylesheet">
<link href="assets/css/custom.css" rel="stylesheet">
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
</head>
<body>
<div class="d-flex">
    <?php include 'sidebar.php'; ?>
    <div class="flex-grow-1 p-4">

        <h4 class="mb-4"><i class="bi bi-speedometer2"></i> Dashboard</h4>

        <!-- Ringkasan Data -->
        <div class="row g-3">
            <div class="col-md-3">
                <div class="card text-center shadow-sm border-0">
                    <div class="card-body">
                        <i class="bi bi-person-badge fs-1 text-primary"></i>
                        <h5 class="mt-2"><?= $jumlahDosen ?></h5>
                        <p class="text-muted">Dosen</p>
                        <a href="dosen.php" class="btn btn-sm btn-outline-primary">Lihat Data</a>
                    </div>
                </div>
            </div>
            <div class="col-md-3">
                <div class="card text-center shadow-sm border-0">
                    <div class="card-body">
                        <i class="bi bi-mortarboard fs-1 text-success"></i>
                        <h5 class="mt-2"><?= $jumlahMhs ?></h5>
                        <p class="text-muted">Mahasiswa</p>
                        <a href="mahasiswa.php" class="btn btn-sm btn-outline-success">Lihat Data</a>
                    </div>
                </div>
            </div>
            <div class="col-md-3">
                <div class="card text-center shadow-sm border-0">
                    <div class="card-body">
                        <i class="bi bi-book fs-1 text-warning"></i>
                        <h5 class="mt-2"><?= $jumlahMatkul ?></h5>
                        <p class="text-muted">Mata Kuliah</p>
                        <a href="matkul.php" class="btn btn-sm btn-outline-warning">Lihat Data</a>
                    </div>
                </div>
            </div>
            <div class="col-md-3">
                <div class="card text-center shadow-sm border-0">
                    <div class="card-body">
                        <i class="bi bi-journal-text fs-1 text-danger"></i>
                        <h5 class="mt-2"><?= $jumlahKrs ?></h5>
                        <p class="text-muted">KRS</p>
                        <a href="krs.php" class="btn btn-sm btn-outline-danger">Lihat Data</a>
                    </div>
                </div>
            </div>
        </div>

        <!-- Garis Pemisah -->
        <hr class="my-4">

        <!-- Grafik Statistik -->
        <div class="row g-4">
            <div class="col-md-6">
                <div class="card shadow-sm">
                    <div class="card-header bg-primary text-white">
                        <i class="bi bi-bar-chart"></i> Jumlah Mahasiswa Berdasarkan Gender
                    </div>
                    <div class="card-body">
                        <canvas id="chartGender" height="180"></canvas>
                    </div>
                </div>
            </div>
            <div class="col-md-6">
                <div class="card shadow-sm">
                    <div class="card-header bg-success text-white">
                        <i class="bi bi-pie-chart"></i> Komposisi Data Sistem
                    </div>
                    <div class="card-body">
                        <canvas id="chartData" height="180"></canvas>
                    </div>
                </div>
            </div>
        </div>

    </div>
</div>

<!-- Chart.js Script -->
<script>
const ctxGender = document.getElementById('chartGender');
new Chart(ctxGender, {
    type: 'bar',
    data: {
        labels: ['Laki-laki', 'Perempuan'],
        datasets: [{
            label: 'Jumlah Mahasiswa',
            data: [<?= $mhsL ?>, <?= $mhsP ?>],
            backgroundColor: ['#007bff', '#e83e8c']
        }]
    },
    options: {
        responsive: true,
        scales: { y: { beginAtZero: true } }
    }
});

const ctxData = document.getElementById('chartData');
new Chart(ctxData, {
    type: 'doughnut',
    data: {
        labels: ['Dosen', 'Mahasiswa', 'Mata Kuliah', 'KRS'],
        datasets: [{
            data: [<?= $jumlahDosen ?>, <?= $jumlahMhs ?>, <?= $jumlahMatkul ?>, <?= $jumlahKrs ?>],
            backgroundColor: ['#0d6efd', '#198754', '#ffc107', '#dc3545']
        }]
    },
    options: {
        responsive: true,
        plugins: { legend: { position: 'bottom' } }
    }
});
</script>
</body>
</html>
