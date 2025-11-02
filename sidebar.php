<?php
// sidebar.php (pastikan session_start() sudah dipanggil di halaman yang include)
?>
<div class="bg-dark text-light p-3" style="width:250px; min-height:100vh;">
    <h5 class="text-center mb-4">📘 Sistem Akademik</h5>
    <ul class="nav flex-column">
        <li class="nav-item mb-1"><a href="dashboard.php" class="nav-link text-light">🏠 Dashboard</a></li>
        <li class="nav-item mb-1"><a href="dosen.php" class="nav-link text-light">👨‍🏫 Dosen</a></li>
        <li class="nav-item mb-1"><a href="mahasiswa.php" class="nav-link text-light">🎓 Mahasiswa</a></li>
        <li class="nav-item mb-1"><a href="matkul.php" class="nav-link text-light">📚 Matkul</a></li>
        <li class="nav-item mb-1"><a href="krs.php" class="nav-link text-light">🗂️ KRS</a></li>
        <li class="nav-item mt-3"><a href="logout.php" class="btn btn-danger w-100">Logout</a></li>
    </ul>
</div>
