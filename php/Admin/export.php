<?php
session_start();
include '../koneksi.php';

// 1. Cek Keamanan
if (!isset($_SESSION['nik']) || $_SESSION['role'] != 'admin') {
    header("Location: ../login.php");
    exit();
}

// 2. Ambil Kategori
$kategori = isset($_GET['kategori']) ? $_GET['kategori'] : '';
if(empty($kategori)){
    echo "<script>window.close();</script>";
    exit();
}

// 3. Logika Query (Sama seperti data_lpr.php)
$judul = "";
$query = "";

if ($kategori == 'semua_warga') {
    $judul = "Laporan Seluruh Data Warga";
    $query = "SELECT * FROM warga ORDER BY nama ASC";
} elseif ($kategori == 'data_kk') {
    $judul = "Laporan Data Kepala Keluarga";
    $query = "SELECT * FROM keluarga ORDER BY kepala_keluarga ASC";
} elseif ($kategori == 'laki_laki') {
    $judul = "Laporan Warga Laki-laki";
    $query = "SELECT * FROM warga WHERE jenis_kelamin='L' ORDER BY nama ASC";
} elseif ($kategori == 'perempuan') {
    $judul = "Laporan Warga Perempuan";
    $query = "SELECT * FROM warga WHERE jenis_kelamin='P' ORDER BY nama ASC";
} elseif ($kategori == 'lansia') {
    $judul = "Laporan Warga Lansia (> 60 Tahun)";
    $query = "SELECT * FROM warga WHERE TIMESTAMPDIFF(YEAR, tanggal_lahir, CURDATE()) >= 60 ORDER BY tanggal_lahir ASC";
} elseif ($kategori == 'anak') {
    $judul = "Laporan Data Anak-anak (< 17 Tahun)";
    $query = "SELECT * FROM warga WHERE TIMESTAMPDIFF(YEAR, tanggal_lahir, CURDATE()) < 17 ORDER BY tanggal_lahir ASC";
}

$result = mysqli_query($koneksi, $query);
?>

<html>
<head>
  <title>Export Data - <?= $judul; ?></title>
  
  <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
  <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.5.1/jquery.min.js"></script>
  <script src="https://cdnjs.cloudflare.com/ajax/libs/popper.js/1.16.0/umd/popper.min.js"></script>
  <script src="https://maxcdn.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>
  
  <link rel="stylesheet" type="text/css" href="https://cdn.datatables.net/1.10.19/css/jquery.dataTables.css">
  <link rel="stylesheet" type="text/css" href="https://cdn.datatables.net/buttons/1.6.5/css/buttons.dataTables.min.css">
  <link rel="stylesheet" type="text/css" href="https://cdn.datatables.net/1.10.19/css/jquery.dataTables.min.css">
  
  <script type="text/javascript" charset="utf8" src="https://cdn.datatables.net/1.10.19/js/jquery.dataTables.js"></script>
</head>

<body>
<div class="container-fluid py-4">
    <h2><?= $judul; ?></h2>
    <p>RT 03 / RW 05 - Kota Batam</p>
    
    <div class="data-tables datatable-dark mt-4">
        <table class="table table-bordered table-striped" id="mauexport" width="100%" cellspacing="0">
            <thead>
                <?php if ($kategori == 'data_kk'): ?>
                    <tr>
                        <th>No</th>
                        <th>No. KK</th>
                        <th>Kepala Keluarga</th>
                        <th>Alamat</th>
                        <th>RT/RW</th>
                        <th>Kode Pos</th>
                        <th>Kelurahan</th>
                        <th>Kecamatan</th>
                        <th>Kota</th>
                        <th>Provinsi</th>
                    </tr>
                <?php else: ?>
                    <tr>
                        <th>No</th>
                        <th>NIK</th>
                        <th>Nama Lengkap</th>
                        <th>L/P</th>
                        <th>Tempat Lahir</th>
                        <th>Tgl Lahir</th>
                        <th>Usia</th>
                        <th>Agama</th>
                        <th>Pendidikan</th>
                        <th>Pekerjaan</th>
                        <th>Alamat</th>
                    </tr>
                <?php endif; ?>
            </thead>
            <tbody>
                <?php 
                $no = 1;
                while($row = mysqli_fetch_assoc($result)) {
                ?>
                    <?php if ($kategori == 'data_kk'): ?>
                        <tr>
                            <td><?= $no++; ?></td>
                            <td><?= $row['no_kk']; ?></td>
                            <td><?= $row['kepala_keluarga']; ?></td>
                            <td><?= $row['alamat']; ?></td>
                            <td><?= $row['rt']; ?>/<?= $row['rw']; ?></td>
                            <td><?= $row['kode_pos']; ?></td>
                            <td><?= $row['kelurahan']; ?></td>
                            <td><?= $row['kecamatan']; ?></td>
                            <td><?= $row['kota']; ?></td>
                            <td><?= $row['provinsi']; ?></td>
                        </tr>
                    <?php else: ?>
                        <?php
                            // Hitung Usia & Alamat
                            $lahir = new DateTime($row['tanggal_lahir']);
                            $today = new DateTime();
                            $usia = $today->diff($lahir)->y;
                            
                            $no_kk = $row['no_kk'];
                            $q_alamat = mysqli_query($koneksi, "SELECT alamat FROM keluarga WHERE no_kk='$no_kk'");
                            $d_alamat = mysqli_fetch_array($q_alamat);
                            $alamat = $d_alamat ? $d_alamat['alamat'] : '-';
                        ?>
                        <tr>
                            <td><?= $no++; ?></td>
                            <td><?= $row['nik']; ?></td>
                            <td><?= $row['nama']; ?></td>
                            <td><?= $row['jenis_kelamin']; ?></td>
                            <td><?= $row['tempat_lahir']; ?></td>
                            <td><?= $row['tanggal_lahir']; ?></td>
                            <td><?= $usia; ?></td>
                            <td><?= $row['agama']; ?></td>
                            <td><?= $row['pendidikan']; ?></td>
                            <td><?= $row['pekerjaan']; ?></td>
                            <td><?= $alamat; ?></td>
                        </tr>
                    <?php endif; ?>
                <?php } ?>
            </tbody>
        </table>
    </div>
</div>
	
<script>
$(document).ready(function() {
    $('#mauexport').DataTable( {
        dom: 'Bfrtip',
        buttons: [
            'copy', 'csv', 'excel', 'pdf', 'print'
        ]
    } );
} );
</script>

<script src="https://code.jquery.com/jquery-3.5.1.js"></script>
<script src="https://cdn.datatables.net/1.10.22/js/jquery.dataTables.min.js"></script>
<script src="https://cdn.datatables.net/buttons/1.6.5/js/dataTables.buttons.min.js"></script>
<script src="https://cdn.datatables.net/buttons/1.6.5/js/buttons.flash.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/jszip/3.1.3/jszip.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/pdfmake/0.1.53/pdfmake.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/pdfmake/0.1.53/vfs_fonts.js"></script>
<script src="https://cdn.datatables.net/buttons/1.6.5/js/buttons.html5.min.js"></script>
<script src="https://cdn.datatables.net/buttons/1.6.5/js/buttons.print.min.js"></script>

<?php include "footer.php" ?>