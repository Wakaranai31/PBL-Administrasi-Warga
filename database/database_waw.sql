CREATE DATABASE waw;
USE waw;

DROP TABLE IF EXISTS layanan_administrasi;
DROP TABLE IF EXISTS admin;
DROP TABLE IF EXISTS warga;

CREATE TABLE admin (
  id_admin int(11) NOT NULL AUTO_INCREMENT,
  NIK varchar(20) NOT NULL,
  nama varchar(100) NOT NULL,
  Password varchar(255) NOT NULL,
  PRIMARY KEY (id_admin),
  UNIQUE (NIK)
);

CREATE TABLE warga (
  id_warga int(11) NOT NULL AUTO_INCREMENT,
  NIK int(20) NOT NULL,
  nama varchar(100) NOT NULL,
  password varchar(255) NOT NULL,
  no_hp varchar(20) NOT NULL,
  pendidikan varchar(50) NOT NULL,
  pekerjaan varchar(50) NOT NULL,
  status varchar(20) NOT NULL,
  tempat_lahir varchar(50) NOT NULL,
  tanggal_lahir date NOT NULL,
  umur int(3) NOT NULL,
  jenis_kelamin varchar(10) NOT NULL,
  alamat text NOT NULL,
  PRIMARY KEY (id_warga),
  UNIQUE (NIK)
);

CREATE TABLE layanan_administrasi (
  id_pengajuan int(11) NOT NULL AUTO_INCREMENT,
  id_warga int(11) NOT NULL,
  disetujui_oleh int(11) NOT NULL,
  status_pengajuan varchar(50) NOT NULL,
  tanggal_pengajuan date NOT NULL,
  jenis_layanan varchar(100) NOT NULL,
  PRIMARY KEY (id_pengajuan),
  FOREIGN KEY (id_warga) REFERENCES warga(id_warga),
  FOREIGN KEY (disetujui_oleh) REFERENCES admin(id_admin)
);

CREATE TABLE berkas (
    id_berkas INT(3) NOT NULL AUTO_INCREMENT,
    kartu_keluarga BLOB NOT NULL,
    ktp BLOB NOT NULL,
    PRIMARY KEY (id_berkas)
);

CREATE TABLE keluarga (
    id_keluarga INT(3) NOT NULL,
    kepala_keluarga INT(3) NOT NULL,
    no_kk INT(18) NOT NULL,
    PRIMARY KEY (id_keluarga)
);
