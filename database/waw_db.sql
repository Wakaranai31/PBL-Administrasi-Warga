-- Tabel Admin
CREATE TABLE admin (
  id_admin int(2) NOT NULL,
  nik varchar(16) NOT NULL,
  nama varchar(100) NOT NULL,
  password varchar(255) NOT NULL,
  PRIMARY KEY (id_admin),
  UNIQUE (nik)
);

-- Tabel Keluarga
CREATE TABLE keluarga (
  no_kk varchar(16) NOT NULL,
  kepala_keluarga varchar(100) DEFAULT NULL,
  alamat text DEFAULT NULL,
  rt varchar(5) DEFAULT NULL,
  rw varchar(5) DEFAULT NULL,
  kelurahan varchar(50) DEFAULT NULL,
  kecamatan varchar(50) DEFAULT NULL,
  kota varchar(50) DEFAULT NULL,
  provinsi varchar(50) DEFAULT NULL,
  kode_pos varchar(10) DEFAULT NULL,
  PRIMARY KEY (no_kk)
);

-- Tabel Warga
CREATE TABLE warga (
  nik varchar(16) NOT NULL,
  no_kk varchar(16) DEFAULT NULL,
  password varchar(255) NOT NULL,
  nama varchar(100) DEFAULT NULL,
  tempat_lahir varchar(50) DEFAULT NULL,
  tanggal_lahir date DEFAULT NULL,
  jenis_kelamin enum('L','P') DEFAULT NULL,
  agama varchar(20) DEFAULT NULL,
  no_hp varchar(15) DEFAULT NULL,
  pendidikan varchar(50) DEFAULT NULL,
  pekerjaan varchar(50) DEFAULT NULL,
  status_perkawinan enum('Belum Kawin','Kawin','Cerai Hidup','Cerai Mati') DEFAULT NULL,
  status_hub_keluarga enum('Kepala Keluarga','Istri','Anak') DEFAULT NULL,
  PRIMARY KEY (nik),
  FOREIGN KEY (no_kk) REFERENCES keluarga(no_kk) ON DELETE CASCADE ON UPDATE CASCADE
);

-- Tabel Pengajuan Perubahan
CREATE TABLE pengajuan_perubahan (
  id_pengajuan int(11) NOT NULL AUTO_INCREMENT,
  nik varchar(16) NOT NULL,
  jenis_perubahan varchar(100) NOT NULL,
  keterangan text NOT NULL,
  status enum('Pending','Disetujui','Ditolak') DEFAULT 'Pending',
  alasan_ditolak text DEFAULT NULL,
  tanggal_request datetime DEFAULT CURRENT_TIMESTAMP,
  nama_baru varchar(100) DEFAULT NULL,
  tempat_lahir_baru varchar(50) DEFAULT NULL,
  tanggal_lahir_baru date DEFAULT NULL,
  jenis_kelamin_baru enum('L','P') DEFAULT NULL,
  agama_baru varchar(20) DEFAULT NULL,
  no_hp_baru varchar(15) DEFAULT NULL,
  pendidikan_baru varchar(50) DEFAULT NULL,
  pekerjaan_baru varchar(50) DEFAULT NULL,
  status_perkawinan_baru varchar(50) DEFAULT NULL,
  status_hub_keluarga_baru enum('Kepala Keluarga','Istri','Anak') DEFAULT NULL,
  PRIMARY KEY (id_pengajuan)
);

-- Tabel Log Warga
CREATE TABLE log_warga (
  id_log int(11) NOT NULL AUTO_INCREMENT,
  nik varchar(16) DEFAULT NULL,
  id_admin int(11) DEFAULT NULL,
  aksi varchar(50) NOT NULL,
  detail text NOT NULL,
  waktu datetime DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (id_log),
  FOREIGN KEY (nik) REFERENCES warga(nik) ON DELETE SET NULL ON UPDATE CASCADE,
  FOREIGN KEY (id_admin) REFERENCES admin(id_admin) ON DELETE SET NULL ON UPDATE CASCADE
);

-- ==========================================
-- INSERT DATA DUMMY ADMIN
-- password : admin
-- ==========================================

INSERT INTO admin (id_admin, nik, password, nama) VALUES
(1, '1234567890123456', '$2y$10$FjptIWWT6.bPF1iSBpMv0OFRNrXc0WTAkuN7umUDmngsKXNLkdBWW', 'Johanes Alpino'); 