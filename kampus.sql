CREATE DATABASE kampus CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci;
USE kampus;

-- 2️⃣ Tabel dosen
CREATE TABLE dosen (
    nidn VARCHAR(23) PRIMARY KEY,
    nama VARCHAR(255) NOT NULL,
    gender CHAR(1) CHECK (gender IN ('L', 'P')),
    no_hp VARCHAR(15)
);

-- 3️⃣ Tabel mahasiswa
CREATE TABLE mahasiswa (
    nim VARCHAR(15) PRIMARY KEY,
    nama VARCHAR(255) NOT NULL,
    gender CHAR(1) CHECK (gender IN ('L', 'P')),
    no_hp VARCHAR(15),
    nidn VARCHAR(23),
    FOREIGN KEY (nidn) REFERENCES dosen(nidn) ON DELETE SET NULL ON UPDATE CASCADE
);

-- 4️⃣ Tabel matkul
CREATE TABLE matkul (
    kode_matkul VARCHAR(10) PRIMARY KEY,
    nama VARCHAR(255) NOT NULL,
    sks INT CHECK (sks > 0)
);

-- 5️⃣ Tabel krs
CREATE TABLE krs (
    kode INT AUTO_INCREMENT PRIMARY KEY,
    nim VARCHAR(15) NOT NULL,
    kode_matkul VARCHAR(10) NOT NULL,
    FOREIGN KEY (nim) REFERENCES mahasiswa(nim) ON DELETE CASCADE ON UPDATE CASCADE,
    FOREIGN KEY (kode_matkul) REFERENCES matkul(kode_matkul) ON DELETE CASCADE ON UPDATE CASCADE,
    UNIQUE KEY uk_krs_nim_kode (nim, kode_matkul)
);
