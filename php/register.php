<?php
require 'koneksi.php';

if( isset($_POST["register"])) {
    if( registrasi($_POST) > 0) {
        echo "<script>alert('Registrasi berhasil')</script>";
    }   else {
        mysqli_error($_koneksi);
    }
}

?>

<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Register</title>
    <link rel="stylesheet" href="../bootstrap/css/bootstrap.min.css">
    <link rel="stylesheet" href="../css/style_login.css">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.13.1/font/bootstrap-icons.min.css">
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">
</head>

<body>
    <div class="login-container">
        <div class="login-card glass-effect">
            <div class="login-header">
                <i class="bi bi-person-fill-add"></i>
                <h2>Register</h2>
            </div>
            <form class="login-form" action="" method="post">
                <div class="input-group">
                    <label for="username">Username</label>
                    <input type="text" name="username" id="username"  placeholder="Masukkan username Anda" required>
                </div>
                <div class="input-group">
                    <label for="password">Password</label>
                    <input type="password" name="password" id="password" placeholder="Masukkan password Anda" required>
                </div>
                <div class="input-group">
                    <label for="password2">Konfirmasi Password</label>
                    <input type="password" name="password2" id="password2" placeholder="Konfirmasi password Anda" required>
                </div>
                <button type="submit" name="register" class="btn btn-primary btn-block">Register</button>
            </form>
        </div>
    </div>
    <script src="../bootstrap/js/bootstrap.bundle.min.js"></script>
</body>
</html>