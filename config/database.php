<?php
$host = "sql206.infinityfree.com";
$user = "if0_41726396";     // Default XAMPP
$pass = "rVWbt1kWDy";         // Default XAMPP kosong
$db   = "if0_41726396_db_ngajikuy";

$koneksi = mysqli_connect($host, $user, $pass, $db);

// Cek apakah koneksi berhasil
if (!$koneksi) {
    die("Koneksi gagal: " . mysqli_connect_error());
}
?>
