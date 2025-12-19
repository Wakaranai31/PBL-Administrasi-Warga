<?php
$koneksi = mysqli_connect("localhost", "root", "", "wawaw");
function registrasi($data) {
	global $koneksi;

	$username = strtolower(stripslashes($data["username"]));
	$password = mysqli_real_escape_string($data["password"]);
	$password2 = mysqli_real_escape_string($data["password"]);

	$cek_username = mysqli_query($koneksi, "SELECT * FROM user WHERE username = '$username'");

    }

?>