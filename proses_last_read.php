<?php
session_start();
require_once 'config/database.php';

if (isset($_POST['surah_no']) && isset($_SESSION['user_id'])) {
    $uid = $_SESSION['user_id'];
    $s_no = mysqli_real_escape_string($koneksi, $_POST['surah_no']);
    $a_no = mysqli_real_escape_string($koneksi, $_POST['ayat_no']);
    $s_name = mysqli_real_escape_string($koneksi, $_POST['surah_name']);

    // Cek apakah user sudah punya riwayat baca
    $cek = mysqli_query($koneksi, "SELECT id FROM last_read WHERE user_id = '$uid'");

    if (mysqli_num_rows($cek) > 0) {
        // UPDATE (Sesuaikan nama kolom kamu)
        $query = "UPDATE last_read SET 
                  nomor_surah = '$s_no', 
                  nomor_ayat = '$a_no', 
                  nama_surah = '$s_name', 
                  updated_at = NOW() 
                  WHERE user_id = '$uid'";
    } else {
        // INSERT (Sesuaikan nama kolom kamu)
        $query = "INSERT INTO last_read (user_id, nomor_surah, nomor_ayat, nama_surah) 
                  VALUES ('$uid', '$s_no', '$a_no', '$s_name')";
    }

    if (mysqli_query($koneksi, $query)) {
        echo "success";
    } else {
        echo "error: " . mysqli_error($koneksi);
    }
}