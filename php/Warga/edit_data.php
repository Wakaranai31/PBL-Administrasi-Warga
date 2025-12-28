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

// 4. LOGIKA PENGAJUAN
if (isset($_POST['update'])) {
    // Tangkap input
    $nama           = mysqli_real_escape_string($koneksi, $_POST['nama']);
    $tempat_lahir   = mysqli_real_escape_string($koneksi, $_POST['tempat_lahir']);
    $tanggal_lahir  = mysqli_real_escape_string($koneksi, $_POST['tanggal_lahir']);
    $jenis_kelamin  = mysqli_real_escape_string($koneksi, $_POST['jenis_kelamin']);
    $pekerjaan      = mysqli_real_escape_string($koneksi, $_POST['pekerjaan']);
    $pendidikan     = mysqli_real_escape_string($koneksi, $_POST['pendidikan']);
    $agama          = mysqli_real_escape_string($koneksi, $_POST['agama']);
    $no_hp          = mysqli_real_escape_string($koneksi, $_POST['no_hp']);
    $status_perkawinan = mysqli_real_escape_string($koneksi, $_POST['status_perkawinan']);
    $status_hub_baru = mysqli_real_escape_string($koneksi, $_POST['status_hub_keluarga']);

    // [VALIDASI] SATPAM KEPALA KELUARGA
    $validasi_aman = true;
    if($status_hub_baru == 'Kepala Keluarga') {
        $no_kk_target = $data['no_kk']; // Ambil KK dari data awal
        // Cek apakah ada Kepala Keluarga selain user ini di KK tersebut
        $cek_head = mysqli_query($koneksi, "SELECT nama FROM warga WHERE no_kk = '$no_kk_target' AND status_hub_keluarga = 'Kepala Keluarga' AND nik != '$nik_target'");
        if(mysqli_num_rows($cek_head) > 0) {
            $existing = mysqli_fetch_assoc($cek_head);
            $validasi_aman = false;
            echo "<script>alert('PERMINTAAN DITOLAK: KK ini sudah memiliki Kepala Keluarga (".$existing['nama']."). Anda tidak bisa mengajukan perubahan menjadi Kepala Keluarga.');</script>";
        }
    }

    if($validasi_aman) {
        // Cek Pending
        $cek_pending = mysqli_query($koneksi, "SELECT * FROM pengajuan_perubahan WHERE nik='$nik_target' AND status='Pending'");
        if(mysqli_num_rows($cek_pending) > 0) {
            $error = "Anggota keluarga ini masih memiliki pengajuan yang belum diproses.";
        } else {
            // Insert Pengajuan
            $insert = mysqli_query($koneksi, "INSERT INTO pengajuan_perubahan 
                (nik, jenis_perubahan, keterangan, status, 
                nama_baru, tempat_lahir_baru, tanggal_lahir_baru, jenis_kelamin_baru, 
                agama_baru, no_hp_baru, pendidikan_baru, pekerjaan_baru, status_perkawinan_baru, status_hub_keluarga_baru) 
                VALUES 
                ('$nik_target', 'Update Anggota Keluarga', 'Perubahan data keluarga', 'Pending',
                '$nama', '$tempat_lahir', '$tanggal_lahir', '$jenis_kelamin',
                '$agama', '$no_hp', '$pendidikan', '$pekerjaan', '$status_perkawinan', '$status_hub_baru')");
                
            if ($insert) {
                echo "<script>alert('Pengajuan perubahan data terkirim! Menunggu persetujuan Admin.'); window.location='keluarga.php';</script>";
            } else {
                $error = "Gagal mengirim: " . mysqli_error($koneksi);
            }
        }
    }
}
?>

<?php include "header.php"; ?>
<?php include "sidebar.php"; ?>

<div class="content">
    <div class="page">
        <div class="d-flex justify-content-between align-items-center mb-3">
            <h2>Edit Anggota Keluarga</h2>
            <a href="keluarga.php" class="btn btn-secondary btn-sm"><i class="bi bi-arrow-left"></i> Kembali</a>
        </div>

        <div class="row justify-content-center">
            <div class="col-md-10">
                <div class="card">
                    
                    <?php if(isset($error)) echo "<div class='alert alert-danger'>$error</div>"; ?>
                    
                    <form method="POST">
                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label class="form-label fw-bold">NIK</label>
                                <input type="text" class="form-control bg-transparent" value="<?= $data['nik']; ?>" readonly>
                            </div>
                            <div class="col-md-6 mb-3">
                                <label class="form-label fw-bold">Nama Lengkap</label>
                                <input type="text" name="nama" class="form-control" value="<?= $data['nama']; ?>" required>
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label class="form-label fw-bold">Tempat Lahir</label>
                                <input type="text" name="tempat_lahir" class="form-control" value="<?= $data['tempat_lahir']; ?>" required>
                            </div>
                            <div class="col-md-6 mb-3">
                                <label class="form-label fw-bold">Tanggal Lahir</label>
                                <input type="date" name="tanggal_lahir" class="form-control" value="<?= $data['tanggal_lahir']; ?>" required>
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label class="form-label fw-bold">Jenis Kelamin</label>
                                <select name="jenis_kelamin" class="form-select" required>
                                    <option value="L" <?= ($data['jenis_kelamin'] == 'L') ? 'selected' : ''; ?>>Laki-laki</option>
                                    <option value="P" <?= ($data['jenis_kelamin'] == 'P') ? 'selected' : ''; ?>>Perempuan</option>
                                </select>
                            </div>
                            
                            <div class="col-md-6 mb-3">
                                <label class="form-label fw-bold">Status Hubungan</label>
                                <select name="status_hub_keluarga" class="form-select" required>
                                    <?php
                                    $opsi_hub = ['Kepala Keluarga', 'Istri', 'Anak'];
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
                                <label class="form-label fw-bold">Pendidikan Terakhir</label>
                                <input type="text" name="pendidikan" class="form-control" value="<?= $data['pendidikan']; ?>">
                            </div>
                            <div class="col-md-6 mb-3">
                                <label class="form-label fw-bold">Pekerjaan</label>
                                <input type="text" name="pekerjaan" class="form-control" value="<?= $data['pekerjaan']; ?>">
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label class="form-label fw-bold">Status Perkawinan</label>
                                <select name="status_perkawinan" class="form-select">
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
                                <label class="form-label fw-bold">Agama</label>
                                <input type="text" name="agama" class="form-control" value="<?= $data['agama']; ?>">
                            </div>
                        </div>

                        <div class="mb-3">
                            <label class="form-label fw-bold">No HP (WhatsApp)</label>
                            <input type="text" name="no_hp" class="form-control" value="<?= $data['no_hp']; ?>">
                        </div>
                                
                        <div class="mt-4 d-flex gap-2">
                            <button type="submit" name="update" class="btn btn-success fw-bold px-4">
                                <i class="bi bi-send-fill me-2"></i> Ajukan Perubahan
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>