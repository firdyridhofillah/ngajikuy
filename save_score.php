<?php
session_start();
require_once 'config/database.php';

// Pastikan user sudah login
if(!isset($_SESSION['user_id'])) {
    die("User belum login");
}

$user_id = $_SESSION['user_id'];
$mode    = $_POST['mode'] ?? 'sambung';
$score   = (int)($_POST['score'] ?? 0);
$t_soal  = (int)($_POST['total_soal'] ?? 0);
$t_benar = (int)($_POST['total_benar'] ?? 0);
$ketepatan = (float)($_POST['ketepatan'] ?? 0);

// 1. Cek apakah user sudah pernah main di mode ini
$check = $koneksi->prepare("SELECT id, total_soal, total_benar FROM leaderboard WHERE user_id = ? AND mode = ?");
$check->bind_param("is", $user_id, $mode);
$check->execute();
$res = $check->get_result();

if($res->num_rows > 0) {
    // 2. Jika SUDAH ADA, kita Update (Jumlahkan total soal dan benar)
    $row = $res->fetch_assoc();
    $new_total_soal = $row['total_soal'] + $t_soal;
    $new_total_benar = $row['total_benar'] + $t_benar;
    $new_ketepatan = ($new_total_benar / $new_total_soal) * 100;

    $sql = "UPDATE leaderboard SET 
            score = GREATEST(score, ?), 
            total_soal = ?, 
            total_benar = ?, 
            ketepatan = ? 
            WHERE user_id = ? AND mode = ?";
    $stmt = $koneksi->prepare($sql);
    $stmt->bind_param("iiidis", $score, $new_total_soal, $new_total_benar, $new_ketepatan, $user_id, $mode);
} else {
    // 3. Jika BELUM ADA, kita Insert baru
    $sql = "INSERT INTO leaderboard (user_id, mode, score, total_soal, total_benar, ketepatan) VALUES (?, ?, ?, ?, ?, ?)";
    $stmt = $koneksi->prepare($sql);
    $stmt->bind_param("issiid", $user_id, $mode, $score, $t_soal, $t_benar, $ketepatan);
}

if($stmt->execute()) {
    echo "Berhasil simpan!";
} else {
    echo "Error: " . $koneksi->error;
}
?>