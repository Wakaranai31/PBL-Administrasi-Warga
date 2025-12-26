<?php
session_start();
include '../koneksi.php';

if (!isset($_SESSION['nik']) || $_SESSION['role'] != 'warga') {
    header("Location: ../login.php");
    exit();
}

$nik_login = $_SESSION['nik'];

// Ambil data
$query = mysqli_query($koneksi, "SELECT * FROM warga WHERE nik='$nik_login'");
$data = mysqli_fetch_array($query);

// LOGIKA PENGAJUAN
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Tangkap input
    $nama           = mysqli_real_escape_string($koneksi, $_POST['nama']);
    $tempat_lahir   = mysqli_real_escape_string($koneksi, $_POST['tempat_lahir']);
    $tanggal_lahir  = mysqli_real_escape_string($koneksi, $_POST['tanggal_lahir']);
    $jenis_kelamin  = mysqli_real_escape_string($koneksi, $_POST['jenis_kelamin']);
    $agama          = mysqli_real_escape_string($koneksi, $_POST['agama']);
    $no_hp          = mysqli_real_escape_string($koneksi, $_POST['no_hp']);
    $pendidikan     = mysqli_real_escape_string($koneksi, $_POST['pendidikan']);
    $pekerjaan      = mysqli_real_escape_string($koneksi, $_POST['pekerjaan']);
    $status_perkawinan = mysqli_real_escape_string($koneksi, $_POST['status_perkawinan']);

    $jenis_perubahan = "Update Biodata";
    $keterangan      = "Warga mengajukan perubahan profil.";
    $status          = "Pending";

    $cek_pending = mysqli_query($koneksi, "SELECT * FROM pengajuan_perubahan WHERE nik='$nik_login' AND status='Pending'");
    
    if(mysqli_num_rows($cek_pending) > 0) {
        $error = "Anda masih memiliki pengajuan yang belum diproses Admin.";
    } else {
        $query_insert = "INSERT INTO pengajuan_perubahan 
            (nik, jenis_perubahan, keterangan, status, 
            nama_baru, tempat_lahir_baru, tanggal_lahir_baru, jenis_kelamin_baru, 
            agama_baru, no_hp_baru, pendidikan_baru, pekerjaan_baru, status_perkawinan_baru) 
            VALUES 
            ('$nik_login', '$jenis_perubahan', '$keterangan', '$status',
            '$nama', '$tempat_lahir', '$tanggal_lahir', '$jenis_kelamin',
            '$agama', '$no_hp', '$pendidikan', '$pekerjaan', '$status_perkawinan')";
            
        $insert = mysqli_query($koneksi, $query_insert);
        
        if ($insert) {
            $success = "Permintaan perubahan berhasil dikirim! Menunggu persetujuan Admin.";
        } else {
            $error = "Gagal mengirim permintaan: " . mysqli_error($koneksi);
        }
    }
}
?>

<?php include "header.php"; ?>
<?php include "sidebar.php"; ?>

<div class="content">
    <div class="page">
        <div class="d-flex justify-content-between align-items-center mb-3">
            <h2>Edit Biodata Saya</h2>
            <a href="profil.php" class="btn btn-secondary btn-sm"><i class="bi bi-arrow-left"></i> Kembali</a>
        </div>

        <div class="row justify-content-center">
            <div class="col-md-10">
                <div class="card">

                    <?php if (isset($success)) echo "<div class='alert alert-success'><i class='bi bi-check-circle'></i> $success</div>"; ?>
                    <?php if (isset($error)) echo "<div class='alert alert-danger'><i class='bi bi-exclamation-triangle'></i> $error</div>"; ?>

                    <form method="POST" action="">
                        
                        <div class="row bg-light p-2 mb-3 rounded border">
                            <div class="col-12 mb-2 border-bottom pb-1">
                                <small class="fw-bold text-muted text-uppercase">Identitas Utama (Tidak dapat diubah)</small>
                            </div>
                            <div class="col-md-4 mb-2">
                                <label class="fw-bold small">Nomor KK</label>
                                <input type="text" class="form-control form-control-sm bg-white" value="<?= $data['no_kk']; ?>" readonly>
                            </div>
                            <div class="col-md-4 mb-2">
                                <label class="fw-bold small">NIK</label>
                                <input type="text" class="form-control form-control-sm bg-white" value="<?= $data['nik']; ?>" readonly>
                            </div>
                            <div class="col-md-4 mb-2">
                                <label class="fw-bold small">Status Hubungan</label>
                                <input type="text" class="form-control form-control-sm bg-white" value="<?= $data['status_hub_keluarga']; ?>" readonly>
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-md-12 mb-3">
                                <label class="fw-bold">Nama Lengkap</label>
                                <input type="text" class="form-control" name="nama" value="<?= $data['nama']; ?>" required>
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label class="fw-bold">Tempat Lahir</label>
                                <input type="text" class="form-control" name="tempat_lahir" value="<?= $data['tempat_lahir']; ?>" required>
                            </div>
                            <div class="col-md-6 mb-3">
                                <label class="fw-bold">Tanggal Lahir</label>
                                <input type="date" class="form-control" name="tanggal_lahir" value="<?= $data['tanggal_lahir']; ?>" required>
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label class="fw-bold">Jenis Kelamin</label>
                                <select class="form-select" name="jenis_kelamin">
                                    <option value="L" <?= ($data['jenis_kelamin'] == 'L') ? 'selected' : ''; ?>>Laki-laki</option>
                                    <option value="P" <?= ($data['jenis_kelamin'] == 'P') ? 'selected' : ''; ?>>Perempuan</option>
                                </select>
                            </div>
                            <div class="col-md-6 mb-3">
                                <label class="fw-bold">Agama</label>
                                <select name="agama" class="form-select">
                                    <?php 
                                    $agama_list = ['Islam', 'Kristen', 'Katolik', 'Hindu', 'Buddha', 'Konghucu'];
                                    foreach($agama_list as $ag) {
                                        $selected = ($data['agama'] == $ag) ? 'selected' : '';
                                        echo "<option value='$ag' $selected>$ag</option>";
                                    }
                                    ?>
                                </select>
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label class="fw-bold">Pendidikan</label>
                                <select name="pendidikan" class="form-select">
                                    <?php 
                                    $pendidikan_list = ['SD', 'SMP', 'SMA/SMK', 'D3', 'S1', 'S2', 'S3', 'Tidak/Belum Sekolah'];
                                    foreach($pendidikan_list as $pd) {
                                        $selected = ($data['pendidikan'] == $pd) ? 'selected' : '';
                                        echo "<option value='$pd' $selected>$pd</option>";
                                    }
                                    ?>
                                </select>
                            </div>
                            <div class="col-md-6 mb-3">
                                <label class="fw-bold">Pekerjaan</label>
                                <input type="text" class="form-control" name="pekerjaan" value="<?= $data['pekerjaan']; ?>">
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label class="fw-bold">Status Perkawinan</label>
                                <select class="form-select" name="status_perkawinan">
                                    <?php 
                                    $opsi_nikah = ['Belum Kawin', 'Kawin', 'Cerai Hidup', 'Cerai Mati'];
                                    foreach($opsi_nikah as $st) {
                                        $selected = ($data['status_perkawinan'] == $st) ? 'selected' : '';
                                        echo "<option value='$st' $selected>$st</option>";
                                    }
                                    ?>
                                </select>
                            </div>
                            <div class="col-md-6 mb-3">
                                <label class="fw-bold">No HP (WhatsApp)</label>
                                <input type="text" class="form-control" name="no_hp" value="<?= $data['no_hp']; ?>">
                            </div>
                        </div>
                                
                        <div class="mt-4">
                            <button type="submit" class="btn btn-success fw-bold px-4">
                                <i class="bi bi-save-fill me-2"></i> Simpan Perubahan
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>

<?php include "footer.php" ?>