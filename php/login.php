<?php
session_start();
include 'koneksi.php';

if (isset($_POST['login'])) {
    $nik = mysqli_real_escape_string($koneksi, $_POST['nik']);
    $password = $_POST['password'];

    // 1. CEK TABEL ADMIN 
    $cek_admin = mysqli_query($koneksi, "SELECT * FROM admin WHERE nik = '$nik'");
    $data_admin = mysqli_fetch_assoc($cek_admin);

    // 2. CEK KE TABEL WARGA (Kalau di admin tidak ketemu)
    $cek_warga = mysqli_query($koneksi, "SELECT * FROM warga WHERE nik = '$nik'");
    $data_warga = mysqli_fetch_assoc($cek_warga);

    if ($data_admin) {
        // Jika username ditemukan di tabel ADMIN
        if (password_verify($password, $data_admin['password'])) {
            // Password Benar
            $_SESSION['nik'] = $data_admin['nik'];
            $_SESSION['nama'] = $data_admin['nama'];
            $_SESSION['role'] = 'admin'; // Tandai sebagai admin
            header("Location: Admin/index.php");
            exit();
        } else {
            $error = "Password Admin salah!";
        }
    } elseif ($data_warga) {
        // Jika username ditemukan di tabel WARGA
        if (password_verify($password, $data_warga['password'])) {
            // Password Benar
            $_SESSION['nik'] = $data_warga['nik'];
            $_SESSION['nama'] = $data_warga['nama'];
            $_SESSION['role'] = 'warga'; // Tandai sebagai warga
            header("Location: Warga/index.php"); // Ganti dengan halaman tujuan warga
            exit();
        } else {
            $error = "Password Warga salah!";
        }
    } else {
        $error = "NIK tidak terdaftar!";
    }
}
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login</title>
    <link rel="stylesheet" href="../bootstrap/css/bootstrap.min.css">
    <link rel="stylesheet" href="../fontawesome/css/all.min.css">
    <link rel="stylesheet" href="../css/style_login.css">
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">
</head>

<body>
    <div class="login-container">
        <div class="login-card glass-effect">
            <div class="login-header">
                <i class="fa-solid fa-user"></i>
                <h2>Login</h2>
            </div>
            
            <?php if(isset($error)) { echo "<div class='alert alert-danger'>$error</div>"; } ?>

            <form class="login-form" method="POST" action="">
                <div class="input-group">
                    <label for="username">NIK</label>
                    <input type="text" id="username" name="nik" placeholder="Masukkan NIK Anda" required>
                </div>
                <div class="input-group">
                    <label for="password">Password</label>
                    <input type="password" id="password" name="password" placeholder="Masukkan password Anda" required>
                </div>
                <button type="submit" name="login" class="btn btn-primary btn-block">Login</button>
            </form>
            <div class="login-footer">
                <a href="daftar.php">Daftar Akun</a><br>
                <a href="daftar_kk.php">Daftar Keluarga</a>
            </div>
        </div>
    </div>
    <script src="../bootstrap/js/bootstrap.bundle.min.js"></script>
</body>
</html>