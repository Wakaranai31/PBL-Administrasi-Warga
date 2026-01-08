<?php
include 'koneksi.php';

if (isset($_POST['daftar_kk'])) {
    $no_kk          = mysqli_real_escape_string($koneksi, $_POST['no_kk']);
    $kepala_keluarga= mysqli_real_escape_string($koneksi, $_POST['kepala_keluarga']);
    $alamat         = mysqli_real_escape_string($koneksi, $_POST['alamat']);
    $rt             = mysqli_real_escape_string($koneksi, $_POST['rt']);
    $rw             = mysqli_real_escape_string($koneksi, $_POST['rw']);
    $kode_pos       = mysqli_real_escape_string($koneksi, $_POST['kode_pos']);
    $kelurahan      = mysqli_real_escape_string($koneksi, $_POST['kelurahan']);
    $kecamatan      = mysqli_real_escape_string($koneksi, $_POST['kecamatan']);
    $kota           = mysqli_real_escape_string($koneksi, $_POST['kota']);
    $provinsi       = mysqli_real_escape_string($koneksi, $_POST['provinsi']);

    // CEK DUPLIKAT
    $cek_kk = mysqli_query($koneksi, "SELECT no_kk FROM keluarga WHERE no_kk = '$no_kk'");
    
    if (mysqli_num_rows($cek_kk) > 0) {
        echo "<script>alert('Nomor KK sudah terdaftar! Silakan langsung daftar akun warga.'); window.location='daftar.php';</script>";
    } else {
        $query = "INSERT INTO keluarga (no_kk, kepala_keluarga, alamat, rt, rw, kode_pos, kelurahan, kecamatan, kota, provinsi)
                  VALUES ('$no_kk', '$kepala_keluarga', '$alamat', '$rt', '$rw', '$kode_pos', '$kelurahan', '$kecamatan', '$kota', '$provinsi')";
        
        if (mysqli_query($koneksi, $query)) {
            echo "<script>alert('Pendaftaran KK Berhasil! Silakan lanjut mendaftarkan Akun Warga.'); window.location='daftar.php';</script>";
        } else {
            echo "<script>alert('Gagal daftar KK: " . mysqli_error($koneksi) . "');</script>";
        }
    }
}
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Registrasi KK Baru</title>
    <link rel="stylesheet" href="../bootstrap/css/bootstrap.min.css">
    <link rel="stylesheet" href="../fontawesome/css/all.min.css">
    <link rel="stylesheet" href="../css/style_login.css">
</head>

<body>
    
    <div class="container d-flex align-items-center justify-content-center min-vh-100 py-4">
        
        <div class="col-12 col-md-9 col-lg-7 col-xl-6">
            
            <div class="glass-effect border-0 shadow-lg rounded-4">
                <div class="p-4"> 
                    
                    <div class="text-center mb-3 border-bottom border-light pb-2">
                        <h4 class="text-dark fw-bold mb-0">Registrasi Kartu Keluarga</h4>
                        <p class="text-muted small mb-0" style="font-size: 0.8rem;">Isi data KK sebelum membuat akun warga</p>
                    </div>

                    <form method="POST">
                        
                        <div class="row g-2 mb-2">
                            <div class="col-md-6">
                                <label class="form-label text-dark fw-bold small mb-1">Nomor KK</label>
                                <input type="text" name="no_kk" class="form-control form-control-sm" required>
                            </div>
                            <div class="col-md-6">
                                <label class="form-label text-dark fw-bold small mb-1">Kepala Keluarga</label>
                                <input type="text" name="kepala_keluarga" class="form-control form-control-sm" required>
                            </div>
                        </div>

                        <div class="mb-2">
                            <label class="form-label text-dark fw-bold small mb-1">Alamat Lengkap</label>
                            <textarea name="alamat" class="form-control form-control-sm" rows="2" required></textarea>
                        </div>

                        <div class="row g-2 mb-2">
                            <div class="col-4">
                                <label class="form-label text-dark fw-bold small mb-1">RT</label>
                                <input type="text" name="rt" class="form-control form-control-sm" required>
                            </div>
                            <div class="col-4">
                                <label class="form-label text-dark fw-bold small mb-1">RW</label>
                                <input type="text" name="rw" class="form-control form-control-sm" required>
                            </div>
                            <div class="col-4">
                                <label class="form-label text-dark fw-bold small mb-1">Kode Pos</label>
                                <input type="text" name="kode_pos" class="form-control form-control-sm" required>
                            </div>
                        </div>

                        <div class="row g-2 mb-2">
                            <div class="col-6">
                                <label class="form-label text-dark fw-bold small mb-1">Kelurahan/Desa</label>
                                <input type="text" name="kelurahan" class="form-control form-control-sm" required>
                            </div>
                            <div class="col-6">
                                <label class="form-label text-dark fw-bold small mb-1">Kecamatan</label>
                                <input type="text" name="kecamatan" class="form-control form-control-sm" required>
                            </div>
                        </div>

                        <div class="row g-2 mb-4">
                            <div class="col-6">
                                <label class="form-label text-dark fw-bold small mb-1">Kota/Kabupaten</label>
                                <input type="text" name="kota" class="form-control form-control-sm" required>
                            </div>
                            <div class="col-6">
                                <label class="form-label text-dark fw-bold small mb-1">Provinsi</label>
                                <input type="text" name="provinsi" class="form-control form-control-sm" required>
                            </div>
                        </div>

                        <div class="d-grid gap-2">
                            <button type="submit" name="daftar_kk" class="btn btn-success btn-sm fw-bold py-2 shadow-sm">
                                Simpan Data KK
                            </button>
                        </div>
                        
                        <div class="text-center mt-3">
                            <a href="login.php" class="text-primary text-decoration-none small">Kembali ke Login</a>
                        </div>

                    </form>

                </div>
            </div>
        </div>
    </div>
    
    <script src="../bootstrap/js/bootstrap.bundle.min.js"></script>
</body>
</html>