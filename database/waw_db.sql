-- ==========================================
-- REKONSTRUKSI DATABASE DARI LAPORAN BAB 5
-- ==========================================

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+07:00";

-- --------------------------------------------------------

-- 1. TABEL ADMIN
CREATE TABLE `admin` (
  `id_admin` int(1) NOT NULL,
  `nik` varchar(16) NOT NULL,
  `password` varchar(255) NOT NULL,
  `nama` varchar(100) NOT NULL,
  PRIMARY KEY (`id_admin`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- --------------------------------------------------------

-- 2. TABEL KELUARGA
CREATE TABLE `keluarga` (
  `no_kk` varchar(16) NOT NULL,
  `kepala_keluarga` varchar(100) DEFAULT NULL,
  `alamat` text DEFAULT NULL,
  `rt` varchar(5) DEFAULT NULL,
  `rw` varchar(5) DEFAULT NULL,
  `kelurahan` varchar(50) DEFAULT NULL,
  `kecamatan` varchar(50) DEFAULT NULL,
  `kota` varchar(50) DEFAULT NULL,
  `provinsi` varchar(50) DEFAULT NULL,
  `kode_pos` varchar(10) DEFAULT NULL,
  PRIMARY KEY (`no_kk`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- --------------------------------------------------------

-- 3. TABEL WARGA
CREATE TABLE `warga` (
  `nik` varchar(16) NOT NULL,
  `no_kk` varchar(16) NOT NULL,
  `password` varchar(255) NOT NULL,
  `nama` varchar(100) NOT NULL,
  `tempat_lahir` varchar(50) DEFAULT NULL,
  `tanggal_lahir` date NOT NULL,
  `jenis_kelamin` enum('L','P') NOT NULL,
  `agama` varchar(20) DEFAULT NULL,
  `no_hp` varchar(15) DEFAULT NULL,
  `pendidikan` varchar(50) DEFAULT NULL,
  `pekerjaan` varchar(50) DEFAULT NULL,
  `status_perkawinan` enum('Belum Kawin','Kawin','Cerai Hidup','Cerai Mati') DEFAULT NULL,
  `status_hub_keluarga` enum('Kepala Keluarga','Suami','Istri','Anak') NOT NULL,
  PRIMARY KEY (`nik`),
  KEY `fk_warga_kk` (`no_kk`),
  CONSTRAINT `fk_warga_kk` FOREIGN KEY (`no_kk`) REFERENCES `keluarga` (`no_kk`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- --------------------------------------------------------

-- 4. TABEL LOG WARGA
CREATE TABLE `log_warga` (
  `id_log` int(11) NOT NULL AUTO_INCREMENT,
  `nik` varchar(16) NOT NULL,
  `aksi` varchar(50) NOT NULL,
  `detail` text NOT NULL,
  `waktu` datetime DEFAULT CURRENT_TIMESTAMP,
  `id_admin` int(1) DEFAULT NULL,
  PRIMARY KEY (`id_log`),
  KEY `fk_log_warga` (`nik`),
  CONSTRAINT `fk_log_warga` FOREIGN KEY (`nik`) REFERENCES `warga` (`nik`) ON DELETE CASCADE ON UPDATE CASCADE,
  KEY `fk_log_admin` (`id_admin`),
  CONSTRAINT `fk_log_admin` FOREIGN KEY (`id_admin`) REFERENCES `admin` (`id_admin`) ON DELETE SET NULL ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- --------------------------------------------------------

-- 5. TABEL PENGAJUAN PERUBAHAN
CREATE TABLE `pengajuan_perubahan` (
  `id_pengajuan` int(11) NOT NULL AUTO_INCREMENT,
  `nik` varchar(16) NOT NULL,
  `jenis_perubahan` varchar(100) NOT NULL,
  `keterangan` text NOT NULL,
  `status` enum('Pending','Disetujui','Ditolak') DEFAULT 'Pending',
  `tanggal_request` datetime DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id_pengajuan`),
  KEY `fk_pengajuan_nik` (`nik`),
  CONSTRAINT `fk_pengajuan_nik` FOREIGN KEY (`nik`) REFERENCES `warga` (`nik`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

COMMIT;