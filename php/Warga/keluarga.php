<?php 
session_start();
include '../koneksi.php';


// 1. Cek Login
if (!isset($_SESSION['nik'])) {
    echo "<script>window.location='../login.php';</script>";
    exit();
}

$nik_login = $_SESSION['nik'];

// 2. Ambil Data User & JOIN Keluarga
// Gunakan LEFT JOIN agar jika KK hilang, data user tetap bisa diambil (untuk dicek)
$query_user = mysqli_query($koneksi, "SELECT warga.*, keluarga.kepala_keluarga, keluarga.alamat, keluarga.rt, keluarga.rw, keluarga.kelurahan 
                                    FROM warga 
                                    LEFT JOIN keluarga ON warga.no_kk = keluarga.no_kk 
                                    WHERE warga.nik='$nik_login'");
$user_data = mysqli_fetch_array($query_user);

// --- [FIX: SABUK PENGAMAN] ---
if (!$user_data) {
    // Jika user benar-benar hilang dari database
    echo "<script>alert('Data akun Anda tidak ditemukan. Silakan Login ulang.'); window.location='../login.php';</script>";
    exit();
}
// Jika user ada, TAPI data keluarganya (KK) kosong/null (Broken Link)
if ($user_data['no_kk'] == NULL || $user_data['kepala_keluarga'] == NULL) {
    echo "<div class='content'>
            <div class='page'>
                <div class='alert alert-danger'>
                    <h4><i class='bi bi-exclamation-triangle-fill'></i> Data Keluarga Tidak Ditemukan</h4>
                    <p>Anda terdaftar sebagai warga, tetapi Nomor KK Anda tidak ditemukan di database keluarga.</p>
                    <hr>
                    <p class='mb-0'>Kemungkinan Admin baru saja menghapus/mengubah Kartu Keluarga Anda. Silakan hubungi Admin.</p>
                </div>
            </div>
        </div>";
    include "footer.php"; // Opsional
    exit();
}
// -----------------------------

$no_kk_user = $user_data['no_kk'];

// 3. Ambil Anggota Keluarga
$query_keluarga = mysqli_query($koneksi, "SELECT * FROM warga WHERE no_kk='$no_kk_user' ORDER BY CASE WHEN status_hub_keluarga = 'Kepala Keluarga' THEN 1 ELSE 2 END");
?>

<?php include "header.php"; ?>
<?php include "sidebar.php"; ?>

<div class="content">
    <div class="page">
        <div class="d-flex justify-content-between align-items-center mb-3">
            <h2>Data Keluarga</h2>
            <span class="badge bg-secondary fs-6">No. KK: <?php echo $no_kk_user; ?></span>
        </div>
        
        <div class="notifikasi-area mb-3">
            <?php
            mysqli_data_seek($query_keluarga, 0); 
            while($row = mysqli_fetch_array($query_keluarga)) {
                $nik_anggota = $row['nik'];
                $nama_anggota = $row['nama'];
                
                $q_status = mysqli_query($koneksi, "SELECT * FROM pengajuan_perubahan WHERE nik='$nik_anggota' ORDER BY id_pengajuan DESC LIMIT 1");
                $stat = mysqli_fetch_array($q_status);

                if($stat) {
                    if($stat['status'] == 'Pending') {
                        echo '<div class="alert alert-warning border-start border-warning border-4 d-flex align-items-center" role="alert">
                                <div><i class="bi bi-hourglass-split me-2"></i> Perubahan data <b>'.$nama_anggota.'</b> sedang <strong>DIPROSES</strong>.</div>
                            </div>';
                    } elseif($stat['status'] == 'Ditolak') {
                        echo '<div class="alert alert-danger alert-dismissible fade show border-start border-danger border-4" role="alert">
                                <div><i class="bi bi-x-circle-fill me-2"></i> Pengajuan <b>'.$nama_anggota.'</b> <strong>DITOLAK</strong>.<br><small>Alasan: '.$stat['alasan_ditolak'].'</small></div>
                                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                            </div>';
                    } elseif($stat['status'] == 'Disetujui') {
                        echo '<div class="alert alert-success alert-dismissible fade show border-start border-success border-4 auto-fade alert-notif" role="alert" data-id="'.$stat['id_pengajuan'].'">
                                <div><i class="bi bi-check-circle-fill me-2"></i> Data <b>'.$nama_anggota.'</b> berhasil <strong>DIPERBARUI</strong>.</div>
                                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                            </div>';
                    }
                }
            }
            mysqli_data_seek($query_keluarga, 0); 
            ?>
        </div>

        <div class="card mb-4">
            <div class="card-body">
                <div class="row">
                    <div class="col-md-6">
                        <p class="mb-1"><strong>Kepala Keluarga:</strong> <?php echo $user_data['kepala_keluarga']; ?></p>
                        <p class="mb-1"><strong>Alamat:</strong> <?php echo $user_data['alamat']; ?></p>
                    </div>
                    <div class="col-md-6 text-md-end">
                        <p class="mb-1">RT/RW: <?php echo $user_data['rt'] . " / " . $user_data['rw']; ?></p>
                        <p class="mb-1">Kelurahan: <?php echo $user_data['kelurahan']; ?></p>
                    </div>
                </div>
            </div>
        </div>

        <div class="card">
            <div class="table-responsive">
                <table class="table table-hover mb-3 align-middle">
                    <thead class="table-light">
                        <tr>
                            <th>No</th>
                            <th>NIK</th>
                            <th>Nama Anggota</th>
                            <th>L/P</th>
                            <th>Hubungan</th>
                            <th>Usia</th>
                            <th>Pekerjaan</th>
                            <th>Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php 
                        $no = 1;
                        while($row = mysqli_fetch_array($query_keluarga)) {
                            $tgl_lahir = new DateTime($row['tanggal_lahir']);
                            $hari_ini = new DateTime();
                            $usia = $hari_ini->diff($tgl_lahir)->y;
                            
                            $nik_anggota = $row['nik'];
                            $q_stat_btn = mysqli_query($koneksi, "SELECT status FROM pengajuan_perubahan WHERE nik='$nik_anggota' ORDER BY id_pengajuan DESC LIMIT 1");
                            $stat_btn = mysqli_fetch_array($q_stat_btn);
                            $status_now = $stat_btn ? $stat_btn['status'] : '';
                        ?>
                        <tr>
                            <td><?php echo $no++; ?></td>
                            <td><?php echo $row['nik']; ?></td>
                            <td><?php echo $row['nama']; ?></td>
                            <td><?php echo ($row['jenis_kelamin'] == 'L') ? 'L' : 'P'; ?></td>
                            <td>
                                <?php 
                                    if($row['status_hub_keluarga'] == 'Kepala Keluarga'){
                                        echo '<span class="badge bg-success">Kepala Keluarga</span>';
                                    } else {
                                        echo $row['status_hub_keluarga'];
                                    }
                                ?>
                            </td>
                            <td><?php echo $usia; ?> Thn</td>
                            <td><?php echo $row['pekerjaan']; ?></td>
                            <td>
                                <?php if($status_now == 'Pending'): ?>
                                    <button class="btn btn-secondary btn-sm" disabled><i class="bi bi-clock"></i> Diproses</button>
                                <?php elseif($status_now == 'Ditolak'): ?>
                                    <a href="edit_data.php?nik=<?php echo $row['nik']; ?>" class="btn btn-warning btn-sm text-white">Ajukan Ulang</a>
                                <?php else: ?>
                                    <a href="edit_data.php?nik=<?php echo $row['nik']; ?>" class="btn btn-warning btn-sm text-white"><i class="bi bi-pencil-square"></i> Edit</a>
                                <?php endif; ?>
                            </td>
                        </tr>
                        <?php } ?>
                    </tbody>
                </table>
            </div>
            <?php if(mysqli_num_rows($query_keluarga) == 0): ?>
                <div class="text-center p-3 text-muted">Data keluarga tidak ditemukan.</div>
            <?php endif; ?>
        </div>
    </div>
</div>

<script src="../../bootstrap/js/bootstrap.bundle.min.js"></script>
<script>
    // (Script Javascript sama seperti sebelumnya - LocalStorage & AutoFade)
    document.addEventListener("DOMContentLoaded", function() {
        var allAlerts = document.querySelectorAll('.alert-notif');
        allAlerts.forEach(function(alertBox) {
            var id = alertBox.getAttribute('data-id');
            if (localStorage.getItem('notif_seen_' + id) === 'yes') { alertBox.remove(); }
        });
        function markAsSeen(element) {
            var id = element.getAttribute('data-id');
            if(id) localStorage.setItem('notif_seen_' + id, 'yes');
        }
        var autoFadeAlerts = document.querySelectorAll('.auto-fade');
        if (autoFadeAlerts.length > 0) {
            setTimeout(function() {
                autoFadeAlerts.forEach(function(badge) {
                    if(document.body.contains(badge)){ 
                        badge.style.transition = "opacity 1s ease-out"; badge.style.opacity = "0"; 
                        markAsSeen(badge); 
                        setTimeout(function() { badge.remove(); }, 1000);
                    }
                });
            }, 5000); 
        }
        var closeButtons = document.querySelectorAll('.btn-close');
        closeButtons.forEach(function(btn) {
            btn.addEventListener('click', function() {
                var parentAlert = this.closest('.alert-notif');
                if(parentAlert) markAsSeen(parentAlert);
            });
        });
    });
</script>

<?php include "footer.php" ?>