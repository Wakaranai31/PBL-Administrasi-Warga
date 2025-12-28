<?php
include 'koneksi.php';

// --- DATA ADMIN YANG MAU DIBUAT ---
$nik_admin  = "1234567890123456";       // NIK atau Username Admin
$pass_admin = "admin";    // Password Admin
$nama_admin = "Budi Hartanto"; // Nama Lengkap
// ----------------------------------

// Enkripsi passwordnya
$password_hashed = password_hash($pass_admin, PASSWORD_DEFAULT);

// Masukkan ke database
$query = "INSERT INTO admin (id_admin, nik, password, nama) 
          VALUES (1, '$nik_admin', '$password_hashed', '$nama_admin')";

if (mysqli_query($koneksi, $query)) {
    echo "<h1>SUKSES! ✅</h1>";
    echo "Admin berhasil ditambahkan.<br>";
    echo "Username/NIK: <b>$nik_admin</b><br>";
    echo "Password: <b>$pass_admin</b><br>";
    echo "<br><a href='login.php'>Klik disini untuk Login</a>";
} else {
    echo "<h1>GAGAL! ❌</h1>";
    echo "Error: " . mysqli_error($koneksi);
}
?>