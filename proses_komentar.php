<?php
session_start();
require_once 'config/database.php';
header('Content-Type: application/json');

if(isset($_POST['komentar'])) {
    $aid = $_POST['artikel_id'];
    $uid = $_SESSION['user_id'];
    $isi = mysqli_real_escape_string($koneksi, $_POST['komentar']);
    $koneksi->query("INSERT INTO artikel_komentar (artikel_id, user_id, komentar) VALUES ($aid, $uid, '$isi')");
    echo json_encode(['status'=>'success']);
}