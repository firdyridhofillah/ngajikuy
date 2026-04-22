<?php
require_once 'config/database.php';

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $nama = mysqli_real_escape_string($koneksi, $_POST['nama']);
    $username = mysqli_real_escape_string($koneksi, $_POST['username']);
    $password = password_hash($_POST['password'], PASSWORD_DEFAULT); // Biar aman di database

    $insert = mysqli_query($koneksi, "INSERT INTO users (nama, username, password, role) VALUES ('$nama', '$username', '$password', 'user')");

    if ($insert) {
        echo "<script>alert('Pendaftaran Berhasil! Silahkan Login'); window.location='login.php';</script>";
    } else {
        echo "<script>alert('Gagal Daftar, Username mungkin sudah ada'); window.location='register.php';</script>";
    }
}