<?php
include 'koneksi.php';

if (isset($_POST['daftar'])) {
    // Ambil inputan user
    $nik = mysqli_real_escape_string($koneksi, $_POST['nik']);
    $pass1 = $_POST['password'];
    $pass2 = $_POST['password2'];

    // 1. Validasi Password
    if ($pass1 !== $pass2) {
        echo "<script>alert('Password konfirmasi tidak sama!');</script>";
    } else {
        // 2. Cek apakah NIK sudah dipakai?
        $cek = mysqli_query($koneksi, "SELECT nik FROM warga WHERE nik = '$nik'");
        if (mysqli_num_rows($cek) > 0) {
             echo "<script>alert('NIK sudah terdaftar!');</script>";
        } else {
            // 3. Enkripsi Password
            $pass_hash = password_hash($pass1, PASSWORD_DEFAULT);

            // 4. Masukkan ke Database (Cuma NIK dan Password)
            // Kolom lain otomatis NULL (Kosong) karena kita sudah ubah di SQL tadi
            $query = "INSERT INTO warga (nik, password) VALUES ('$nik', '$pass_hash')";

            if (mysqli_query($koneksi, $query)) {
                echo "<script>alert('Daftar Berhasil! Silakan Login.'); window.location='login.php';</script>";
            } else {
                echo "<script>alert('Gagal Mendaftar: " . mysqli_error($koneksi) . "');</script>";
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
    <title>Daftar</title>
    <link rel="stylesheet" href="../bootstrap/css/bootstrap.min.css">
    <link rel="stylesheet" href="../fontawesome/all.min.css">
    <link rel="stylesheet" href="../css/style_login.css">
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">
</head>

<body>
    <div class="login-container">
        <div class="login-card glass-effect">
            <div class="login-header">
                <i class="fa-solid fa-user-plus"></i>
                <h2>Daftar</h2>
            </div>
            
            <form class="login-form" method="POST">
                <div class="input-group">
                    <label>NIK</label>
                    <input type="text" name="nik" placeholder="Masukkan NIK" required>
                </div>

                <div class="input-group">
                    <label>Password</label>
                    <input type="password" name="password" placeholder="Buat Password" required>
                </div>

                <div class="input-group">
                    <label>Ulangi Password</label>
                    <input type="password" name="password2" placeholder="Ulangi Password" required>
                </div>

                <button type="submit" name="daftar" class="btn btn-primary btn-block">Daftar Sekarang</button>
            </form>

            <div class="login-footer">
                <a href="login.php">Sudah punya akun? Login</a>
            </div>
        </div>
    </div>
</body>
</html>