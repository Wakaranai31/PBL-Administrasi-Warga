<?php
include 'koneksi.php';

if (isset($_POST['daftar'])) {
    // TANGKAP DATA INPUT
    $no_kk          = mysqli_real_escape_string($koneksi, $_POST['no_kk']);
    $nik            = mysqli_real_escape_string($koneksi, $_POST['nik']);
    $nama           = mysqli_real_escape_string($koneksi, $_POST['nama']);
    $pass1          = $_POST['password'];
    $pass2          = $_POST['password2'];
    
    $tempat_lahir   = $_POST['tempat_lahir'];
    $tanggal_lahir  = $_POST['tanggal_lahir'];
    $jenis_kelamin  = $_POST['jenis_kelamin'];
    $agama          = $_POST['agama'];
    $status_hub     = $_POST['status_hub_keluarga'];
    $status_kawin   = $_POST['status_perkawinan'];
    $pendidikan     = $_POST['pendidikan'];
    $pekerjaan      = $_POST['pekerjaan'];
    $no_hp          = $_POST['no_hp'];

    // VALIDASI PASSWORD
    if ($pass1 !== $pass2) {
        echo "<script>alert('Password konfirmasi tidak cocok!');</script>";
    } else {
        // 1. CEK NO KK (Apakah KK terdaftar?)
        $cek_kk = mysqli_query($koneksi, "SELECT no_kk FROM keluarga WHERE no_kk = '$no_kk'");
        
        // 2. CEK NIK (Apakah sudah ada?)
        $cek_nik = mysqli_query($koneksi, "SELECT nik FROM warga WHERE nik = '$nik'");

        if (mysqli_num_rows($cek_kk) == 0) {
            echo "<script>alert('GAGAL: Nomor KK tidak ditemukan! Silakan daftar KK dulu.'); window.location='daftar_kk.php';</script>";
        } elseif (mysqli_num_rows($cek_nik) > 0) {
            echo "<script>alert('NIK sudah terdaftar! Silakan Login.');</script>";
        } else {
            // [VALIDASI BARU] SATPAM KEPALA KELUARGA
            $validasi_aman = true;
            if($status_hub == 'Kepala Keluarga') {
                $cek_head = mysqli_query($koneksi, "SELECT nama FROM warga WHERE no_kk = '$no_kk' AND status_hub_keluarga = 'Kepala Keluarga'");
                if(mysqli_num_rows($cek_head) > 0) {
                    $existing = mysqli_fetch_assoc($cek_head);
                    $validasi_aman = false;
                    echo "<script>alert('PENDAFTARAN GAGAL: Kartu Keluarga ini sudah memiliki Kepala Keluarga atas nama ".$existing['nama'].". Anda tidak bisa mendaftar sebagai Kepala Keluarga.');</script>";
                }
            }

            if($validasi_aman) {
                // 3. SIMPAN DATA
                $pass_hash = password_hash($pass1, PASSWORD_DEFAULT);
                $query_warga = "INSERT INTO warga (
                    nik, no_kk, nama, password, 
                    tempat_lahir, tanggal_lahir, jenis_kelamin, agama,
                    status_hub_keluarga, status_perkawinan, 
                    pendidikan, pekerjaan, no_hp
                  ) VALUES (
                    '$nik', '$no_kk', '$nama', '$pass_hash',
                    '$tempat_lahir', '$tanggal_lahir', '$jenis_kelamin', '$agama',
                    '$status_hub', '$status_kawin',
                    '$pendidikan', '$pekerjaan', '$no_hp'
                  )";

                if (mysqli_query($koneksi, $query_warga)) {
                    // [SINKRONISASI] Jika pendaftar adalah Kepala Keluarga, update tabel keluarga
                    if($status_hub == 'Kepala Keluarga') {
                        mysqli_query($koneksi, "UPDATE keluarga SET kepala_keluarga = '$nama' WHERE no_kk = '$no_kk'");
                    }

                    echo "<script>alert('Pendaftaran Berhasil! Silakan Login.'); window.location='login.php';</script>";
                } else {
                    echo "<script>alert('Error: " . mysqli_error($koneksi) . "');</script>";
                }
            }
        }
    }
}
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Daftar Akun Warga</title>
    <link rel="stylesheet" href="../bootstrap/css/bootstrap.min.css">
    <link rel="stylesheet" href="../fontawesome/css/all.min.css">
    
    <link rel="stylesheet" href="../css/style_login.css">
</head>

<body>
    
    <div class="container d-flex align-items-center justify-content-center min-vh-100 py-4">
        
        <div class="col-12 col-md-11 col-lg-9 col-xl-8">
            
            <div class="glass-effect border-0 shadow-lg" style="border-radius: 15px;">
                <div class="card-body p-4"> <div class="text-center mb-4 border-bottom border-light pb-2">
                        <h3 class="text-dark fw-bold">Pendaftaran Akun Warga</h3>
                        <p class="text-light small">Lengkapi biodata diri Anda untuk akses layanan</p>
                    </div>

                    <form method="POST">
                        <div class="row g-4"> 
                            <div class="col-md-5 border-end border-light border-opacity-25">

                                <div class="mb-2">
                                    <label class="form-label text-dark ">Nomor KK</label>
                                    <input type="text" name="no_kk" class="form-control" required>
                                </div>
                                
                                <div class="mb-2">
                                    <label class="form-label text-dark ">NIK</label>
                                    <input type="text" name="nik" class="form-control"  required>
                                </div>

                                <div class="mb-2">
                                    <label class="form-label text-dark ">Nama Lengkap</label>
                                    <input type="text" name="nama" class="form-control" required>
                                </div>

                                <div class="row">
                                    <div class="col-6 mb-2">
                                        <label class="form-label text-dark ">Password</label>
                                        <input type="password" name="password" class="form-control" required>
                                    </div>
                                    <div class="col-6 mb-2">
                                        <label class="form-label text-dark ">Ulangi</label>
                                        <input type="password" name="password2" class="form-control" required>
                                    </div>
                                </div>
                            </div>

                            <div class="col-md-7">

                                <div class="row">
                                    <div class="col-md-6 mb-2">
                                        <label class="form-label text-dark ">Tempat Lahir</label>
                                        <input type="text" name="tempat_lahir" class="form-control" required>
                                    </div>
                                    <div class="col-md-6 mb-2">
                                        <label class="form-label text-dark ">Tgl Lahir</label>
                                        <input type="date" name="tanggal_lahir" class="form-control" required>
                                    </div>
                                </div>

                                <div class="row">
                                    <div class="col-md-6 mb-2">
                                        <label class="form-label text-dark ">Gender</label>
                                        <select name="jenis_kelamin" class="form-select">
                                            <option value="L">Laki-laki</option>
                                            <option value="P">Perempuan</option>
                                        </select>
                                    </div>
                                    <div class="col-md-6 mb-2">
                                        <label class="form-label text-dark ">Agama</label>
                                        <input type="text" name="agama" class="form-control"> 
                                    </div>
                                </div>

                                <div class="row">
                                    <div class="col-md-6 mb-2">
                                        <label class="form-label text-dark ">Hubungan</label>
                                        <select name="status_hub_keluarga" class="form-select">
                                            <option value="Kepala Keluarga">Kepala Keluarga</option>
                                            <option value="Istri">Istri</option>
                                            <option value="Anak">Anak</option>
                                        </select>
                                    </div>
                                    <div class="col-md-6 mb-2">
                                        <label class="form-label text-dark ">Status Kawin</label>
                                        <select name="status_perkawinan" class="form-select">
                                            <option value="Belum Kawin">Belum Kawin</option>
                                            <option value="Kawin">Kawin</option>
                                            <option value="Cerai Hidup">Cerai Hidup</option>
                                            <option value="Cerai Mati">Cerai Mati</option>
                                        </select>
                                    </div>
                                </div>

                                <div class="row">
                                    <div class="col-md-6 mb-2">
                                        <label class="form-label text-dark ">Pendidikan</label>
                                        <input type="text" name="pendidikan" class="form-control">
                                    </div>
                                    <div class="col-md-6 mb-2">
                                        <label class="form-label text-dark ">Pekerjaan</label>
                                        <input type="text" name="pekerjaan" class="form-control">
                                    </div>
                                </div>
                                
                                <div class="mb-3">
                                    <label class="form-label text-dark ">No HP</label>
                                    <input type="text" name="no_hp" class="form-control"2..." required>
                                </div>

                                <div class="d-grid gap-2 mt-3">
                                    <button type="submit" name="daftar" class="btn btn-primary fw-bold py-2 shadow btn-sm">
                                        DAFTAR SEKARANG
                                    </button>
                                </div>
                            </div>
                        </div> </form>

                    <div class="text-center mt-4">
                        <p class="text-primary mb-0 small">Sudah punya akun? <a href="login.php" class="text-primary text-decoration-underline small">Login disini</a></p>
                    </div>

                </div>
            </div>
        </div>
    </div>

    <script src="../bootstrap/js/bootstrap.bundle.min.js"></script>
</body>
</html>