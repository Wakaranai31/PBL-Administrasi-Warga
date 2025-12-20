<?php
session_start();
include 'koneksi.php';

if (!isset($_SESSION['username'])) {
    header("Location: login.php");
    exit();
}

$username = $_SESSION['username'];
$query = mysqli_query($koneksi, "SELECT * FROM user WHERE username='$username'");
$data = mysqli_fetch_array($query);

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $nama_lengkap = mysqli_real_escape_string($koneksi, $_POST['nama_lengkap']);
    $nik = mysqli_real_escape_string($koneksi, $_POST['nik']);
    $tempat_lahir = mysqli_real_escape_string($koneksi, $_POST['tempat_lahir']);
    $tanggal_lahir = mysqli_real_escape_string($koneksi, $_POST['tanggal_lahir']);
    $umur = mysqli_real_escape_string($koneksi, $_POST['umur']);
    $jenis_kelamin = mysqli_real_escape_string($koneksi, $_POST['jenis_kelamin']);
    $status = mysqli_real_escape_string($koneksi, $_POST['status']);
    $pendidikan = mysqli_real_escape_string($koneksi, $_POST['pendidikan']);
    $pekerjaan = mysqli_real_escape_string($koneksi, $_POST['pekerjaan']);
    $no_hp = mysqli_real_escape_string($koneksi, $_POST['no_hp']);
    
    $update = mysqli_query($koneksi, "UPDATE user SET 
        nama_lengkap='$nama_lengkap',
        nik='$nik',
        tempat_lahir='$tempat_lahir',
        tanggal_lahir='$tanggal_lahir',
        umur='$umur',
        jenis_kelamin='$jenis_kelamin',
        status='$status',
        pendidikan='$pendidikan',
        pekerjaan='$pekerjaan',
        no_hp='$no_hp'
        WHERE username='$username'");
    
    if ($update) {
        $success = "Profil berhasil diperbarui!";
        $query = mysqli_query($koneksi, "SELECT * FROM user WHERE username='$username'");
        $data = mysqli_fetch_array($query);
    } else {
        $error = "Gagal memperbarui profil!";
    }
}
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Edit Profile</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="style_edit_profile.css" rel="stylesheet">
</head>
<body>
    <div class="container">
        <div class="row justify-content-center">
            <div class="col-md-8">
                <div class="profile-card">
                    <h2 class="profile-title">Edit Profil Saya</h2>
                    
                    <?php if (isset($success)) : ?>
                        <div class="alert alert-success alert-dismissible fade show" role="alert">
                            <?php echo $success; ?>
                            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                        </div>
                    <?php endif; ?>
                    
                    <?php if (isset($error)) : ?>
                        <div class="alert alert-danger alert-dismissible fade show" role="alert">
                            <?php echo $error; ?>
                            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                        </div>
                    <?php endif; ?>
                    
                    <form method="POST" action="">
                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label for="nama_lengkap" class="form-label">Nama Lengkap</label>
                                <input type="text" class="form-control" id="nama_lengkap" name="nama_lengkap" 
                                       value="<?php echo isset($data['nama_lengkap']) ? $data['nama_lengkap'] : ''; ?>" required>
                            </div>
                            
                            <div class="col-md-6 mb-3">
                                <label for="nik" class="form-label">NIK</label>
                                <input type="text" class="form-control" id="nik" name="nik" 
                                       value="<?php echo isset($data['nik']) ? $data['nik'] : ''; ?>" required>
                            </div>
                        </div>
                        
                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label for="tempat_lahir" class="form-label">Tempat Lahir</label>
                                <input type="text" class="form-control" id="tempat_lahir" name="tempat_lahir" 
                                       value="<?php echo isset($data['tempat_lahir']) ? $data['tempat_lahir'] : ''; ?>" required>
                            </div>
                            
                            <div class="col-md-6 mb-3">
                                <label for="tanggal_lahir" class="form-label">Tanggal Lahir</label>
                                <input type="date" class="form-control" id="tanggal_lahir" name="tanggal_lahir" 
                                       value="<?php echo isset($data['tanggal_lahir']) ? $data['tanggal_lahir'] : ''; ?>" required>
                            </div>
                        </div>
                        
                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label for="umur" class="form-label">Umur</label>
                                <input type="number" class="form-control" id="umur" name="umur" 
                                       value="<?php echo isset($data['umur']) ? $data['umur'] : ''; ?>">
                            </div>
                            
                            <div class="col-md-6 mb-3">
                                <label for="jenis_kelamin" class="form-label">Jenis Kelamin</label>
                                <select class="form-select" id="jenis_kelamin" name="jenis_kelamin" required>
                                    <option value="">Pilih Jenis Kelamin</option>
                                    <option value="Laki-laki" <?php echo (isset($data['jenis_kelamin']) && $data['jenis_kelamin'] == 'Laki-laki') ? 'selected' : ''; ?>>Laki-laki</option>
                                    <option value="Perempuan" <?php echo (isset($data['jenis_kelamin']) && $data['jenis_kelamin'] == 'Perempuan') ? 'selected' : ''; ?>>Perempuan</option>
                                </select>
                            </div>
                        </div>
                        
                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label for="status" class="form-label">Status</label>
                                <select class="form-select" id="status" name="status">
                                    <option value="">Pilih Status</option>
                                    <option value="Belum Menikah" <?php echo (isset($data['status']) && $data['status'] == 'Belum Menikah') ? 'selected' : ''; ?>>Belum Menikah</option>
                                    <option value="Menikah" <?php echo (isset($data['status']) && $data['status'] == 'Menikah') ? 'selected' : ''; ?>>Menikah</option>
                                    <option value="Cerai" <?php echo (isset($data['status']) && $data['status'] == 'Cerai') ? 'selected' : ''; ?>>Cerai</option>
                                </select>
                            </div>
                            
                            <div class="col-md-6 mb-3">
                                <label for="pendidikan" class="form-label">Pendidikan Terakhir</label>
                                <select class="form-select" id="pendidikan" name="pendidikan">
                                    <option value="">Pilih Pendidikan</option>
                                    <option value="SD" <?php echo (isset($data['pendidikan']) && $data['pendidikan'] == 'SD') ? 'selected' : ''; ?>>SD</option>
                                    <option value="SMP" <?php echo (isset($data['pendidikan']) && $data['pendidikan'] == 'SMP') ? 'selected' : ''; ?>>SMP</option>
                                    <option value="SMA" <?php echo (isset($data['pendidikan']) && $data['pendidikan'] == 'SMA') ? 'selected' : ''; ?>>SMA</option>
                                    <option value="D3" <?php echo (isset($data['pendidikan']) && $data['pendidikan'] == 'D3') ? 'selected' : ''; ?>>D3</option>
                                    <option value="S1" <?php echo (isset($data['pendidikan']) && $data['pendidikan'] == 'S1') ? 'selected' : ''; ?>>S1</option>
                                    <option value="S2" <?php echo (isset($data['pendidikan']) && $data['pendidikan'] == 'S2') ? 'selected' : ''; ?>>S2</option>
                                    <option value="S3" <?php echo (isset($data['pendidikan']) && $data['pendidikan'] == 'S3') ? 'selected' : ''; ?>>S3</option>
                                </select>
                            </div>
                        </div>
                        
                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label for="pekerjaan" class="form-label">Pekerjaan</label>
                                <input type="text" class="form-control" id="pekerjaan" name="pekerjaan" 
                                       value="<?php echo isset($data['pekerjaan']) ? $data['pekerjaan'] : ''; ?>">
                            </div>
                            
                            <div class="col-md-6 mb-3">
                                <label for="no_hp" class="form-label">No HP</label>
                                <input type="text" class="form-control" id="no_hp" name="no_hp" 
                                       value="<?php echo isset($data['no_hp']) ? $data['no_hp'] : ''; ?>">
                            </div>
                        </div>
                        
                        <div class="d-flex gap-2 mt-4">
                            <button type="submit" class="btn btn-save">Simpan Perubahan</button>
                            <a href="profile.php" class="btn btn-cancel">Batal</a>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
    
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>

</html>
