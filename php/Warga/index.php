<?php 
session_start();
include '../koneksi.php'; 

if (!isset($_SESSION['nik']) || $_SESSION['role'] != 'warga') {
    header("Location: ../login.php");
    exit();
}

$nik = $_SESSION['nik'];
$query_warga = mysqli_query($koneksi, "SELECT * FROM warga WHERE nik = '$nik'");
$data_warga = mysqli_fetch_assoc($query_warga);
$data_keluarga = null;
$nama_kepala_keluarga = "Belum Terdata"; // Default

if (!empty($data_warga['no_kk'])) {
    $no_kk = $data_warga['no_kk'];
    $query_kk = mysqli_query($koneksi, "SELECT * FROM keluarga WHERE no_kk = '$no_kk'");
    $data_keluarga = mysqli_fetch_assoc($query_kk);
    $query_head = mysqli_query($koneksi, "SELECT nama FROM warga WHERE no_kk = '$no_kk' AND status_hub_keluarga = 'Kepala Keluarga'");
    if(mysqli_num_rows($query_head) > 0){
        $data_head = mysqli_fetch_assoc($query_head);
        $nama_kepala_keluarga = $data_head['nama'];
    }
}
?>

<?php include "header.php"; ?>
<?php include "sidebar.php"; ?>
    
<div class="content">
    <div class="page">
        <div class="mb-4">
            <h2 class="fw-bold text-dark">Selamat Datang, <?php echo explode(' ', $data_warga['nama'])[0]; ?>! </h2>
            <p class="text-muted">dashboard layanan warga</p>
        </div>
        
        <div class="row">
            <div class="col-md-8">
                <div class="card border-0 shadow-sm mb-4">
                    <div class="card-body p-4">
                        <h5 class="fw-bold text-primary mb-3 border-bottom pb-2">
                            <i class="bi bi-person-badge-fill me-2"></i>Status Warga
                        </h5>
                        
                        <div class="row g-3">
                            <div class="col-md-6">
                                <label class="small text-muted fw-bold">Nama Lengkap</label>
                                <p class="text-dark fs-5 mb-0">
                                    <?php echo $data_warga['nama']; ?>
                                </p>
                            </div>

                            <div class="col-md-6">
                                <label class="small text-muted fw-bold">Nomor Induk Kependudukan (NIK)</label>
                                <p class="text-dark mb-0">
                                    <?php echo $data_warga['nik']; ?>
                                </p>
                            </div>

                            <div class="col-md-6">
                                <label class="small text-muted fw-bold">Nomor Kartu Keluarga (KK)</label>
                                <p class="text-dark mb-0">
                                    <?php echo $data_warga['no_kk'] ? $data_warga['no_kk'] : '<span class="text-danger fw-normal fst-italic">Belum memiliki KK</span>'; ?>
                                </p>
                            </div>

                            <div class="col-md-6">
                                <label class="small text-muted fw-bold">Status Hubungan</label>
                                <p class="mb-0">
                                    <span class="text-dark">
                                        <?php echo $data_warga['status_hub_keluarga']; ?>
                                    </span>
                                </p>
                            </div>

                            <div class="col-12">
                                <label class="small text-muted fw-bold">Alamat Terdaftar</label>
                                <p class="mb-0 bg-light p-2 rounded border text-secondary">
                                    <i class="bi bi-geo-alt-fill me-1 text-danger"></i>
                                    <?php echo ($data_keluarga) ? $data_keluarga['alamat'] . " RT " . $data_keluarga['rt'] . " / RW " . $data_keluarga['rw'] : '-'; ?>
                                </p>
                            </div>
                        </div>

                    </div>
                </div>
            </div>

            <div class="col-md-4">
                <div class="card border-0 shadow-sm bg-primary text-white mb-4">
                    <div class="card-body p-0 text-center">
                        <i class="bi bi-people-fill display-6 mb-2"></i>
                        <h5 class="fw-bold">Anggota Keluarga</h5>
                        <a href="keluarga.php" class="btn btn-light text-primary fw-bold w-100 btn-sm">Lihat Detail</a>
                    </div>
                </div>

                <div class="card border-0 shadow-sm bg-warning text-dark">
                    <div class="card-body p-0 text-center">
                        <i class="bi bi-pencil-square display-6 mb-2"></i>
                        <h5 class="fw-bold">Edit Profil</h5>
                        <a href="edit_profile.php" class="btn btn-light text-dark fw-bold w-100 btn-sm">Perbarui Data</a>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<?php include "footer.php" ?>