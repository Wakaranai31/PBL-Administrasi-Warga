<?php 
session_start();
include '../koneksi.php'; // Hubungkan ke database

// --- 1. CEK KEAMANAN (SATPAM) ---
// Kalau belum login, atau role-nya bukan admin, tendang keluar!
if (!isset($_SESSION['nik']) || $_SESSION['role'] != 'admin') {
    header("Location: ../login.php");
    exit();
}

// --- 2. HITUNG DATA DARI DATABASE ---

// Hitung Jumlah Warga
$query_warga = mysqli_query($koneksi, "SELECT COUNT(*) as total FROM warga");
$data_warga = mysqli_fetch_assoc($query_warga);
$jumlah_warga = $data_warga['total'];

// Hitung Jumlah Keluarga (KK)
$query_kk = mysqli_query($koneksi, "SELECT COUNT(*) as total FROM keluarga");
$data_kk = mysqli_fetch_assoc($query_kk);
$jumlah_kk = $data_kk['total'];

// Hitung Pengajuan (Anggap ini Surat Masuk)
$query_surat = mysqli_query($koneksi, "SELECT COUNT(*) as total FROM pengajuan_perubahan");
$data_surat = mysqli_fetch_assoc($query_surat);
$jumlah_surat = $data_surat['total'];

?>

<?php include "header.php"; ?>
<?php include "sidebar.php"; ?>

<div class="content">
    <div id="dashboard" class="page">
        <h2>Dashboard</h2>
        
        <div class="dashboard-grid">
            
            <div class="stat-card">
                <h3><?php echo $jumlah_warga; ?></h3>
                <p>Jumlah Warga</p>
            </div>

            <div class="stat-card">
                <h3><?php echo $jumlah_kk; ?></h3>
                <p>Kepala Keluarga</p>
            </div>

            <div class="stat-card">
                <h3><?php echo $jumlah_surat; ?></h3>
                <p>Pengajuan Masuk</p>
            </div>

            <div class="stat-card">
                <h3>0</h3>
                <p>Surat Keluar</p>
            </div>

        </div>
    </div>
</div>

<script src="../bootstrap/js/bootstrap.bundle.min.js"></script>
</body>
</html>