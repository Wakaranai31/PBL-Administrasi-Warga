<?php
session_start();
include '../koneksi.php';

if (!isset($_SESSION['nik']) || $_SESSION['role'] != 'warga') {
    header("Location: ../login.php");
    exit();
}

$nik_login = $_SESSION['nik'];

// Ambil data berdasarkan NIK
$query = mysqli_query($koneksi, "SELECT * FROM warga WHERE nik='$nik_login'");
$data = mysqli_fetch_array($query);

// LOGIKA PENGAJUAN PERUBAHAN DATA (VERSI TANPA JSON)
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // 1. Tangkap semua input
    $nama = mysqli_real_escape_string($koneksi, $_POST['nama']);
    $tempat_lahir = mysqli_real_escape_string($koneksi, $_POST['tempat_lahir']);
    $tanggal_lahir = mysqli_real_escape_string($koneksi, $_POST['tanggal_lahir']);
    $jenis_kelamin = mysqli_real_escape_string($koneksi, $_POST['jenis_kelamin']);
    $agama = mysqli_real_escape_string($koneksi, $_POST['agama']);
    $no_hp = mysqli_real_escape_string($koneksi, $_POST['no_hp']);
    $pendidikan = mysqli_real_escape_string($koneksi, $_POST['pendidikan']);
    $pekerjaan = mysqli_real_escape_string($koneksi, $_POST['pekerjaan']);
    $status_perkawinan = mysqli_real_escape_string($koneksi, $_POST['status_perkawinan']);

    $jenis_perubahan = "Update Biodata";
    $keterangan = "Warga mengajukan perubahan profil.";
    $status = "Pending";

    // 2. Cek apakah sudah ada pengajuan yang PENDING?
    $cek_pending = mysqli_query($koneksi, "SELECT * FROM pengajuan_perubahan WHERE nik='$nik_login' AND status='Pending'");
    
    if(mysqli_num_rows($cek_pending) > 0) {
        $error = "Anda masih memiliki pengajuan yang belum diproses Admin.";
    } else {
        // 3. Masukkan ke tabel PENGAJUAN (Kolom Manual)
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
            // Kita tidak merefresh data $data[...] disini, 
            // karena data asli di database belum berubah.
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
        <div class="row justify-content-center">
            <div class="col-md-10">
                <div class="profile-card">
                    <h2 class="profile-title">Edit Biodata</h2>

                    <?php if (isset($success)) echo "<div class='alert alert-success'>$success</div>"; ?>
                    <?php if (isset($error)) echo "<div class='alert alert-danger'>$error</div>"; ?>

                    <form method="POST" action="">
                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label>NIK (Tidak bisa diubah)</label>
                                <input type="text" class="form-control" value="<?= $data['nik']; ?>" disabled>
                            </div>
                            <div class="col-md-6 mb-3">
                                <label>Nama Lengkap</label>
                                <input type="text" class="form-control" name="nama" value="<?= $data['nama']; ?>" required>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label>Tempat Lahir</label>
                                <input type="text" class="form-control" name="tempat_lahir" value="<?= $data['tempat_lahir']; ?>" required>
                            </div>
                            <div class="col-md-6 mb-3">
                                <label>Tanggal Lahir</label>
                                <input type="date" class="form-control" name="tanggal_lahir" value="<?= $data['tanggal_lahir']; ?>" required>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label>Jenis Kelamin</label>
                                <select class="form-select" name="jenis_kelamin">
                                    <option value="L" <?= ($data['jenis_kelamin'] == 'L') ? 'selected' : ''; ?>>Laki-laki</option>
                                    <option value="P" <?= ($data['jenis_kelamin'] == 'P') ? 'selected' : ''; ?>>Perempuan</option>
                                </select>
                            </div>
                            <div class="col-md-6 mb-3">
                                <label>Agama</label>
                                <input type="text" class="form-control" name="agama" value="<?= $data['agama']; ?>">
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label>Pendidikan</label>
                                <input type="text" class="form-control" name="pendidikan" value="<?= $data['pendidikan']; ?>">
                            </div>
                            <div class="col-md-6 mb-3">
                                <label>Pekerjaan</label>
                                <input type="text" class="form-control" name="pekerjaan" value="<?= $data['pekerjaan']; ?>">
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label>Status Perkawinan</label>
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
                                <label>No HP</label>
                                <input type="text" class="form-control" name="no_hp" value="<?= $data['no_hp']; ?>">
                            </div>
                        </div>
                                
                        <div class="mt-4">
                            <button type="submit" class="btn btn-save btn-success btn-sm text-white">Simpan Perubahan</button>
                            <a href="profil.php" class="btn btn-cancel btn-danger btn-sm text-white">Kembali</a>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>