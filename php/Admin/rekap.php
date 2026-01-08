<?php
session_start();
include '../koneksi.php';

if (!isset($_SESSION['nik']) || $_SESSION['role'] != 'admin') {
    header("Location: ../login.php");
    exit();
}
$kategori = isset($_GET['kategori']) ? $_GET['kategori'] : '';
$data_laporan = [];
$judul_laporan = "";
if ($kategori == 'semua_warga') {
    $judul_laporan = "Laporan Seluruh Data Warga";
    $query = "SELECT * FROM warga ORDER BY nama ASC";
    $result = mysqli_query($koneksi, $query);
} 
elseif ($kategori == 'data_kk') {
    $judul_laporan = "Laporan Data Kepala Keluarga";
    $query = "SELECT * FROM keluarga ORDER BY kepala_keluarga ASC";
    $result = mysqli_query($koneksi, $query);
}
elseif ($kategori == 'laki_laki') {
    $judul_laporan = "Laporan Warga Laki-laki";
    $query = "SELECT * FROM warga WHERE jenis_kelamin='L' ORDER BY nama ASC";
    $result = mysqli_query($koneksi, $query);
}
elseif ($kategori == 'perempuan') {
    $judul_laporan = "Laporan Warga Perempuan";
    $query = "SELECT * FROM warga WHERE jenis_kelamin='P' ORDER BY nama ASC";
    $result = mysqli_query($koneksi, $query);
}
elseif ($kategori == 'lansia') {
    $judul_laporan = "Laporan Warga Lansia (> 60 Tahun)";
    $query = "SELECT * FROM warga WHERE TIMESTAMPDIFF(YEAR, tanggal_lahir, CURDATE()) >= 60 ORDER BY tanggal_lahir ASC";
    $result = mysqli_query($koneksi, $query);
}
elseif ($kategori == 'anak') {
    $judul_laporan = "Laporan Data Anak-anak (< 17 Tahun)";
    $query = "SELECT * FROM warga WHERE TIMESTAMPDIFF(YEAR, tanggal_lahir, CURDATE()) < 17 ORDER BY tanggal_lahir ASC";
    $result = mysqli_query($koneksi, $query);
}
?>

<?php include "header.php" ?>
<?php include "sidebar.php" ?>

    <div class="content">
        <div class="page">
            
            <div class="d-flex justify-content-between align-items-center mb-4 btn-cetak-area">
                <h2><i class="bi bi-file-earmark-text-fill"></i>Rekap Data</h2>
            </div>
            <div class="card filter-card shadow-sm">
                <h5 class="mb-3 text-primary"><i class="bi bi-filter-circle"></i> Pilih Kategori</h5>
                <form method="GET" action="">
                    <div class="input-group input-group-sm">
                        <span class="input-group-text bg-white border-end-0"><i class="bi bi-collection-fill text-primary"></i></span>
                        <select name="kategori" class="form-select border-start-0" required>
                            <option value="" disabled selected>-- Pilih Jenis Data --</option>
                            <option value="semua_warga" <?= ($kategori == 'semua_warga') ? 'selected' : '' ?>>Semua Data Warga</option>
                            <option value="data_kk" <?= ($kategori == 'data_kk') ? 'selected' : '' ?>>Data Kepala Keluarga</option>
                            <option value="laki_laki" <?= ($kategori == 'laki_laki') ? 'selected' : '' ?>>Warga Laki-laki</option>
                            <option value="perempuan" <?= ($kategori == 'perempuan') ? 'selected' : '' ?>>Warga Perempuan</option>
                            <option value="lansia" <?= ($kategori == 'lansia') ? 'selected' : '' ?>>Warga Lansia (> 60 Thn)</option>
                            <option value="anak" <?= ($kategori == 'anak') ? 'selected' : '' ?>>Anak-anak (< 17 Thn)</option>
                        </select>
                        <button type="submit" class="btn btn-primary px-4 fw-bold"><i class="bi bi-search"></i> Tampilkan</button>
                    </div>
                </form>
            </div>
            <?php if (!empty($kategori) && isset($result)): ?>
                <link rel="stylesheet" href="../../css/style.css?v=1.1">
                <div class="card shadow">
                    <div class="card-header bg-white py-3 d-flex justify-content-between align-items-center btn-cetak-area">
                        <h5 class="mb-0 fw-bold text-uppercase text-primary"><?= $judul_laporan; ?></h5>
                        
                        <div class="d-flex gap-2">
                            <a href="export.php?kategori=<?= $kategori; ?>" target="_blank" class="btn btn-success text-white">
                                <i class="bi bi-file-earmark-spreadsheet-fill"></i> Export Data (Excel/PDF)
                            </a>
                            <button onclick="window.print()" class="btn btn-primary">
                                <i class="bi bi-printer-fill"></i> Cetak PDF
                            </button>
                        </div>
                    </div>
                    <div class="text-center mt-3 d-none d-print-block">
                        <h3 class="fw-bold text-uppercase">Laporan Data Kependudukan</h3>
                        <h5 class="fw-bold">RT 03 / RW 05 - KOTA BATAM</h5>
                        <h6 class="text-decoration-underline"><?= $judul_laporan; ?></h6>
                        <br>
                    </div>
                    <div class="card-body">
                        <div class="table-responsive">
                            <table class="table table-bordered table-striped align-middle">
                                <thead class="table-dark text-center">
                                    <?php if ($kategori == 'data_kk'): ?>
                                        <tr>
                                            <th>No</th>
                                            <th>No. KK</th>
                                            <th>Kepala Keluarga</th>
                                            <th>Alamat</th>
                                            <th>RT/RW</th>
                                            <th>Kode Pos</th>
                                        </tr>
                                    <?php else: ?>
                                        <tr>
                                            <th>No</th>
                                            <th>NIK</th>
                                            <th>Nama Lengkap</th>
                                            <th>L/P</th>
                                            <th>Usia</th>
                                            <th>Pekerjaan</th>
                                            <th>Alamat (KK)</th>
                                        </tr>
                                    <?php endif; ?>
                                </thead>
                                <tbody>
                                    <?php 
                                    $no = 1;
                                    if(mysqli_num_rows($result) > 0) {
                                        while($row = mysqli_fetch_assoc($result)) {
                                    ?>
                                        <?php if ($kategori == 'data_kk'): ?>
                                            <tr>
                                                <td class="text-center"><?= $no++; ?></td>
                                                <td><?= $row['no_kk']; ?></td>
                                                <td class="fw-bold"><?= $row['kepala_keluarga']; ?></td>
                                                <td><?= $row['alamat']; ?></td>
                                                <td class="text-center"><?= $row['rt']; ?>/<?= $row['rw']; ?></td>
                                                <td class="text-center"><?= $row['kode_pos']; ?></td>
                                            </tr>
                                        <?php else: ?>
                                            <?php
                                                $lahir = new DateTime($row['tanggal_lahir']);
                                                $today = new DateTime();
                                                $usia = $today->diff($lahir)->y;
                                                $no_kk = $row['no_kk'];
                                                $q_alamat = mysqli_query($koneksi, "SELECT alamat FROM keluarga WHERE no_kk='$no_kk'");
                                                $d_alamat = mysqli_fetch_array($q_alamat);
                                                $alamat = $d_alamat ? $d_alamat['alamat'] : '-';
                                            ?>
                                            <tr>
                                                <td class="text-center"><?= $no++; ?></td>
                                                <td><?= $row['nik']; ?></td>
                                                <td><?= $row['nama']; ?></td>
                                                <td class="text-center"><?= $row['jenis_kelamin']; ?></td>
                                                <td class="text-center"><?= $usia; ?> Thn</td>
                                                <td><?= $row['pekerjaan']; ?></td>
                                                <td><?= $alamat; ?></td>
                                            </tr>
                                        <?php endif; ?>
                                    <?php 
                                        }
                                    } else {
                                        echo '<tr><td colspan="7" class="text-center p-3 text-muted">Data tidak ditemukan.</td></tr>';
                                    }
                                    ?>
                                </tbody>
                            </table>
                        </div>
                        
                        <div class="mt-3">
                            <strong>Total Data: </strong> <?= mysqli_num_rows($result); ?> Baris
                        </div>
                        <div class="tanda-tangan">
                            <p>Batam, <?= date('d F Y'); ?></p>
                            <br><br><br>
                            <p class="fw-bold text-decoration-underline">Ketua RT 03</p>
                        </div>
                    </div>
                </div>
            <?php else: ?>
                <div class="alert alert-info border-start border-info border-4 text-center p-5">
                    <h1><i class="bi bi-bar-chart-line"></i></h1>
                    <h6>Silakan Pilih Kategori Laporan</h6>
                    <p>Pilih jenis data pada dropdown di atas, lalu klik tombol <b>Tampilkan</b>.</p>
                </div>
            <?php endif; ?>
        </div>
    </div>

<?php include "footer.php" ?>