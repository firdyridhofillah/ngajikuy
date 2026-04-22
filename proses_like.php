<?php
session_start();
require_once 'config/database.php';
header('Content-Type: application/json');

if(!isset($_SESSION['user_id'])) { echo json_encode(['status'=>'error','message'=>'Login dulu ya!']); exit; }

$uid = $_SESSION['user_id'];
$aid = $_GET['id'];

$cek = $koneksi->query("SELECT id FROM artikel_likes WHERE artikel_id = $aid AND user_id = $uid");
if($cek->num_rows > 0) {
    $koneksi->query("DELETE FROM artikel_likes WHERE artikel_id = $aid AND user_id = $uid");
} else {
    $koneksi->query("INSERT INTO artikel_likes (artikel_id, user_id, type) VALUES ($aid, $uid, 'like')");
}

$count = $koneksi->query("SELECT COUNT(*) as total FROM artikel_likes WHERE artikel_id = $aid")->fetch_assoc();
echo json_encode(['status'=>'success', 'total'=>$count['total']]);