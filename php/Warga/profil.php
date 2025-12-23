<?php
session_start();
include '../koneksi.php';

// 1. Cek Login
if (!isset($_SESSION['nik'])) {
    echo "<script>window.location='../login.php';</script>";
    exit();
}

// 2. Ambil data user
$nik_login = $_SESSION['nik'];
$query = mysqli_query($koneksi, "SELECT * FROM warga WHERE nik='$nik_login'");
$data = mysqli_fetch_array($query);

// --- [FIX: SABUK PENGAMAN ANTI-ERROR] ---
// Jika data tidak ditemukan di database (karena terhapus/diedit admin), 
// hancurkan sesi dan paksa login ulang.
if (!$data) {
    session_unset();
    session_destroy();
    echo "<script>
            alert('Sesi tidak valid atau data akun Anda telah dihapus. Silakan Login ulang.'); 
            window.location='../login.php';
          </script>";
    exit();
}
// ----------------------------------------

// 3. Cek Status Pengajuan
$cek_request = mysqli_query($koneksi, "SELECT * FROM pengajuan_perubahan WHERE nik='$nik_login' ORDER BY id_pengajuan DESC LIMIT 1");
$info_request = mysqli_fetch_array($cek_request);
?>

<?php include "header.php"; ?>
<?php include "sidebar.php"; ?>

<div class="content">
    <div class="page">
        
        <?php if($info_request): ?>
            <?php if($info_request['status'] == 'Pending'): ?>
                <div class="alert alert-warning border-start border-warning border-4">
                    <h5 class="alert-heading"><i class="bi bi-hourglass-split"></i> Menunggu Persetujuan</h5>
                    <p class="mb-0">Data profil akan diperbarui setelah Admin menyetujui permintaan Anda.</p>
                </div>
            <?php elseif($info_request['status'] == 'Disetujui'): ?>
                <div class="alert alert-success alert-dismissible fade show border-start border-success border-4 alert-notif" role="alert" data-id="<?= $info_request['id_pengajuan']; ?>">
                    <h5 class="alert-heading"><i class="bi bi-check-circle-fill"></i> Perubahan Berhasil!</h5>
                    <p class="mb-0">Selamat! Pengajuan perubahan data profil Anda telah disetujui.</p>
                    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                </div>
            <?php elseif($info_request['status'] == 'Ditolak'): ?>
                <div class="alert alert-danger alert-dismissible fade show border-start border-danger border-4" role="alert">
                    <h5 class="alert-heading"><i class="bi bi-x-circle-fill"></i> Pengajuan Ditolak</h5>
                    <p class="mb-2">Maaf, pengajuan perubahan data Anda ditolak.</p>
                    <div class="bg-light text-danger p-2 rounded border border-danger">
                        <strong>Alasan:</strong> <?php echo $info_request['alasan_ditolak'] ? $info_request['alasan_ditolak'] : '-'; ?>
                    </div>
                    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                </div>
            <?php endif; ?>
        <?php endif; ?>

        <div class="d-flex justify-content-between align-items-center mb-3">
            <h2>Profil Saya</h2>
            <?php if(!$info_request || $info_request['status'] != 'Pending'): ?>
            <a href="edit_profile.php" class="btn btn-primary btn-sm"><i class="bi bi-pencil-square"></i> Edit Profil</a>
            <?php endif; ?>
        </div>

        <div class="card">
            <div class="card-body">
                <div class="row mb-2">
                    <div class="col-md-3 fw-bold">NIK</div>
                    <div class="col-md-9">: <?php echo $data['nik']; ?></div>
                </div>
                <div class="row mb-2">
                    <div class="col-md-3 fw-bold">Nama Lengkap</div>
                    <div class="col-md-9">: <?php echo $data['nama']; ?></div>
                </div>
                <div class="row mb-2">
                    <div class="col-md-3 fw-bold">Tempat, Tgl Lahir</div>
                    <div class="col-md-9">: <?php echo $data['tempat_lahir'] . ", " . date('d F Y', strtotime($data['tanggal_lahir'])); ?></div>
                </div>
                <div class="row mb-2">
                    <div class="col-md-3 fw-bold">Jenis Kelamin</div>
                    <div class="col-md-9">: <?php echo ($data['jenis_kelamin'] == 'L') ? 'Laki-laki' : 'Perempuan'; ?></div>
                </div>
                <div class="row mb-2">
                    <div class="col-md-3 fw-bold">Agama</div>
                    <div class="col-md-9">: <?php echo $data['agama']; ?></div>
                </div>
                <div class="row mb-2">
                    <div class="col-md-3 fw-bold">Status Perkawinan</div>
                    <div class="col-md-9">: <?php echo $data['status_perkawinan']; ?></div>
                </div>
                <div class="row mb-2">
                    <div class="col-md-3 fw-bold">Pekerjaan</div>
                    <div class="col-md-9">: <?php echo $data['pekerjaan']; ?></div>
                </div>
                <div class="row mb-2">
                    <div class="col-md-3 fw-bold">No HP</div>
                    <div class="col-md-9">: <?php echo $data['no_hp']; ?></div>
                </div>
                <div class="row mb-2">
                    <div class="col-md-3 fw-bold">Status Keluarga</div>
                    <div class="col-md-9">: <span class="badge bg-info text-dark"><?php echo $data['status_hub_keluarga']; ?></span></div>
                </div>
            </div>
        </div>
    </div>
</div>

<script src="../assets/bootstrap/js/bootstrap.bundle.min.js"></script>
<script>
    // Script Alert LocalStorage (Sama seperti sebelumnya)
    document.addEventListener("DOMContentLoaded", function() {
        var alerts = document.querySelectorAll('.alert-notif');
        alerts.forEach(function(alert) {
            var id = alert.getAttribute('data-id');
            if (localStorage.getItem('alert_seen_' + id) === 'yes') { alert.remove(); }
        });
        var closeBtns = document.querySelectorAll('.btn-close');
        closeBtns.forEach(function(btn) {
            btn.addEventListener('click', function() {
                var parent = this.closest('.alert-notif');
                if(parent) {
                    var id = parent.getAttribute('data-id');
                    localStorage.setItem('alert_seen_' + id, 'yes');
                }
            });
        });
    });
</script>

<?php include "footer.php" ?>