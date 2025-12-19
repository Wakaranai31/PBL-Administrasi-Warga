<?php

$hostname = "localhost";
$username = "root";
$password = ""; 
$database = "waw_db";

$koneksi = mysqli_connect($hostname, $username, $password, $database);

if (!$koneksi) {
    die("Koneksi Database Gagal: " . mysqli_connect_error());
} 
    // else {
    //     echo "Koneksi Berhasil!"; // ( echo kalau mau tes doang )
    // }
?>