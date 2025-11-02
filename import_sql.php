<?php
// import_sql.php
$host = 'localhost';
$user = 'root';
$pass = ''; // ubah jika MySQL kamu pakai password
$dbname = 'kampus';

// Lokasi file SQL
$file_structure = __DIR__ . '/kampus.sql';
$file_data = __DIR__ . '/seed.sql';

try {
    // Koneksi ke MySQL tanpa pilih database dulu
    $pdo = new PDO("mysql:host=$host", $user, $pass, [
        PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION
    ]);
    echo "✅ Terhubung ke server MySQL\n";

    // Jalankan file kampus.sql (membuat DB dan tabel)
    $sql1 = file_get_contents($file_structure);
    $pdo->exec($sql1);
    echo "✅ Struktur database 'kampus' berhasil dibuat\n";

    // Koneksi ulang ke database kampus
    $pdo = new PDO("mysql:host=$host;dbname=$dbname;charset=utf8mb4", $user, $pass, [
        PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION
    ]);

    // Jalankan file seed.sql (isi data)
    $sql2 = file_get_contents($file_data);
    $pdo->exec($sql2);
    echo "✅ Data contoh berhasil dimasukkan ke tabel\n";

} catch (PDOException $e) {
    echo "❌ Error: " . $e->getMessage();
}
