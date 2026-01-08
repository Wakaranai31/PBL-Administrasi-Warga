<?php
include '../koneksi.php'; 
$q_notif = mysqli_query($koneksi, "SELECT COUNT(*) as total FROM pengajuan_perubahan WHERE status='Pending'");
$d_notif = mysqli_fetch_assoc($q_notif);
$jml_pending = $d_notif['total'];
?>
<div class="sidebar">
    <a href="index.php"><i class="bi bi-speedometer2"></i> Dashboard</a>
    <a href="verifikasi.php" class="d-flex justify-content-between align-items-center">
        <span> <i class="bi bi-ui-checks"></i> Verifikasi </span>
        <?php if($jml_pending > 0): ?>
            <span class="badge bg-danger rounded-pill"><?= $jml_pending; ?></span>
        <?php endif; ?>
    </a>
    <a href="data_cri.php"><i class="bi bi-search"></i> Pencarian Data</a>
    <a href="data_wrg.php"><i class="bi bi-people-fill"></i> Data Warga</a>
    <a href="data_klg.php"><i class="bi bi-card-checklist"></i> Data Keluarga</a>   
    <a href="rekap.php"><i class="bi bi-graph-up"></i> Rekap</a>
</div>