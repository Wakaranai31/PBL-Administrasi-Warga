<?php 
session_start();
// Mundur 2 langkah cari koneksi.php
include '../koneksi.php'; 

// 1. CEK KEAMANAN (SATPAM)
// Kalau belum login atau bukan warga, tendang ke login
if (!isset($_SESSION['nik']) || $_SESSION['role'] != 'warga') {
    header("Location: ../login.php");
    exit();
}

// 2. AMBIL DATA WARGA
$nik = $_SESSION['nik'];
$query_warga = mysqli_query($koneksi, "SELECT * FROM warga WHERE nik = '$nik'");
$data_warga = mysqli_fetch_assoc($query_warga);

// 3. AMBIL DATA KELUARGA (Jika warga sudah punya No KK)
$data_keluarga = null;
if (!empty($data_warga['no_kk'])) {
    $no_kk = $data_warga['no_kk'];
    $query_kk = mysqli_query($koneksi, "SELECT * FROM keluarga WHERE no_kk = '$no_kk'");
    $data_keluarga = mysqli_fetch_assoc($query_kk);
}
?>

<?php include "header.php"; ?>
<?php include "sidebar.php"; ?>
    
<div class="content">
    <div class="page">
        <h2>Selamat Datang di Portal Warga</h2>
        <p>Anda dapat melihat data keluarga, surat, dan pengumuman dari pengurus RT/RW.</p>
        
        <div class="card">
            <h3>Status Keanggotaan</h3>
            
            <p>Nama Lengkap: 
                <span style="color: #007bff; font-weight: bold;">
                    <?php echo $data_warga['nama'] ? $data_warga['nama'] : '<span class="text-danger">Belum Terdata</span>'; ?>
                </span>
            </p>

            <p>Kepala Keluarga: 
                <span style="color: #007bff; font-weight: bold;">
                    <?php echo ($data_keluarga) ? $data_keluarga['kepala_keluarga'] : '<span class="text-danger">Belum Terdata</span>'; ?>
                </span>
            </p>

            <p>Alamat: 
                <span style="color: #007bff; font-weight: bold;">
                    <?php echo ($data_keluarga) ? $data_keluarga['alamat'] : '<span class="text-danger">Belum Terdata</span>'; ?>
                </span>
            </p>

            <p>Nomor KK: 
                <span style="color: #007bff; font-weight: bold;">
                    <?php echo $data_warga['no_kk'] ? $data_warga['no_kk'] : '<span class="text-danger">Belum Terdata</span>'; ?>
                </span>
            </p>
        </div>
    </div>
</div>

<?php include "footer.php" ?>