<?php
session_start();
include '../koneksi.php';

// 1. Cek Login
if (!isset($_SESSION['nik'])) {
    header("Location: ../login.php");
    exit();
}

// 2. Ambil NIK Target dari URL
$nik_target = isset($_GET['nik']) ? $_GET['nik'] : '';
if(empty($nik_target)) {
    header("Location: keluarga.php");
    exit();
}

// 3. Ambil Data Awal
$query = mysqli_query($koneksi, "SELECT * FROM warga WHERE nik='$nik_target'");
$data = mysqli_fetch_array($query);

// 4. LOGIKA PENGAJUAN (Sama seperti Profil)
if (isset($_POST['update'])) {
    // Tangkap input
    $nama = mysqli_real_escape_string($koneksi, $_POST['nama']);
    $tempat_lahir = mysqli_real_escape_string($koneksi, $_POST['tempat_lahir']);
    $tanggal_lahir = mysqli_real_escape_string($koneksi, $_POST['tanggal_lahir']);
    $jenis_kelamin = mysqli_real_escape_string($koneksi, $_POST['jenis_kelamin']);
    $status_hub = mysqli_real_escape_string($koneksi, $_POST['status_hub_keluarga']); // Ini bedanya dgn profil
    $pekerjaan = mysqli_real_escape_string($koneksi, $_POST['pekerjaan']);
    $pendidikan = mysqli_real_escape_string($koneksi, $_POST['pendidikan']);
    $agama = mysqli_real_escape_string($koneksi, $_POST['agama']);
    
    // Default nilai untuk kolom yang tidak ada di form ini tapi ada di tabel pengajuan
    // (Kita isi dengan data lama agar tidak error/kosong)
    $no_hp_lama = $data['no_hp'];
    $status_kawin_lama = $data['status_perkawinan'];

    // Cek Pending
    $cek_pending = mysqli_query($koneksi, "SELECT * FROM pengajuan_perubahan WHERE nik='$nik_target' AND status='Pending'");
    if(mysqli_num_rows($cek_pending) > 0) {
        $error = "Anggota keluarga ini masih memiliki pengajuan yang belum diproses.";
    } else {
        // Masukkan ke Pengajuan
        // Note: Kita pakai NIK Target sebagai referensi
        $insert = mysqli_query($koneksi, "INSERT INTO pengajuan_perubahan 
            (nik, jenis_perubahan, keterangan, status, 
            nama_baru, tempat_lahir_baru, tanggal_lahir_baru, jenis_kelamin_baru, 
            agama_baru, no_hp_baru, pendidikan_baru, pekerjaan_baru, status_perkawinan_baru) 
            VALUES 
            ('$nik_target', 'Update Anggota Keluarga', 'Perubahan data keluarga', 'Pending',
            '$nama', '$tempat_lahir', '$tanggal_lahir', '$jenis_kelamin',
            '$agama', '$no_hp_lama', '$pendidikan', '$pekerjaan', '$status_kawin_lama')");
            
        // Kita juga perlu update status_hub_keluarga secara terpisah/manual nanti oleh admin, 
        // atau untuk simpelnya saat ini kita anggap Admin akan melihat deskripsi.
        
        if ($insert) {
            echo "<script>alert('Pengajuan perubahan data terkirim! Menunggu persetujuan Admin.'); window.location='keluarga.php';</script>";
        } else {
            $error = "Gagal mengirim: " . mysqli_error($koneksi);
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
                    <h2 class="profile-title">Edit Anggota Keluarga</h2>

                    <?php if(isset($error)) echo "<div class='alert alert-danger'>$error</div>"; ?>
                    <form method="POST">
                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label class="form-label">NIK (Tidak bisa diubah)</label>
                                <input type="text" class="form-control bg-light" value="<?= $data['nik']; ?>" readonly>
                            </div>
                            <div class="col-md-6 mb-3">
                                <label class="form-label">Nama Lengkap</label>
                                <input type="text" name="nama" class="form-control" value="<?= $data['nama']; ?>" required>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label class="form-label">Tempat Lahir</label>
                                <input type="text" name="tempat_lahir" class="form-control" value="<?= $data['tempat_lahir']; ?>" required>
                            </div>
                            <div class="col-md-6 mb-3">
                                <label class="form-label">Tanggal Lahir</label>
                                <input type="date" name="tanggal_lahir" class="form-control" value="<?= $data['tanggal_lahir']; ?>" required>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label class="form-label">Jenis Kelamin</label>
                                <select name="jenis_kelamin" class="form-select" required>
                                    <option value="L" <?= ($data['jenis_kelamin'] == 'L') ? 'selected' : ''; ?>>Laki-laki</option>
                                    <option value="P" <?= ($data['jenis_kelamin'] == 'P') ? 'selected' : ''; ?>>Perempuan</option>
                                </select>
                            </div>
                            <div class="col-md-6 mb-3">
                                <label class="form-label">Status Hubungan</label>
                                <select name="status_hub_keluarga" class="form-select" required>
                                    <?php
                                    $opsi_hub = ['Kepala Keluarga', 'Suami', 'Istri', 'Anak', 'Menantu', 'Orang Tua', 'Mertua', 'Famili Lain'];
                                    foreach ($opsi_hub as $h) {
                                        $selected = ($data['status_hub_keluarga'] == $h) ? 'selected' : '';
                                        echo "<option value='$h' $selected>$h</option>";
                                    }
                                    ?>
                                </select>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label class="form-label">Pendidikan Terakhir</label>
                                <input type="text" name="pendidikan" class="form-control" value="<?= $data['pendidikan']; ?>">
                            </div>
                            <div class="col-md-6 mb-3">
                                <label class="form-label">Pekerjaan</label>
                                <input type="text" name="pekerjaan" class="form-control" value="<?= $data['pekerjaan']; ?>">
                            </div>
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Agama</label>
                            <input type="text" name="agama" class="form-control" value="<?= $data['agama']; ?>">
                        </div>
                                
                        <div class="mt-4 d-flex gap-2">
                            <button type="submit" name="update" class="btn btn-save btn-success btn-sm text-white">Ajukan Perubahan</button>
                            <a href="keluarga.php" class="btn btn-cancel btn-danger btn-sm text-white">Batal</a>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>