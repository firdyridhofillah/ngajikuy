<?php
require_once 'config/database.php';
$aid = $_GET['id'];
$comments = $koneksi->query("SELECT k.*, u.username FROM artikel_komentar k JOIN users u ON k.user_id = u.id WHERE k.artikel_id = $aid ORDER BY k.created_at DESC");

if($comments->num_rows == 0) {
    echo "<p class='text-center text-muted py-4'>Belum ada komentar. Jadilah yang pertama!</p>";
}

while($c = $comments->fetch_assoc()) {
    echo "
    <div class='mb-4 d-flex gap-3'>
        <div class='flex-shrink-0' style='width:40px; height:40px; background:#f1f5f9; border-radius:50%; display:flex; align-items:center; justify-content:center; font-weight:bold; color:#64748b;'>
            ".strtoupper(substr($c['username'],0,1))."
        </div>
        <div class='w-100'>
            <div class='d-flex justify-content-between mb-1'>
                <span class='fw-bold' style='font-size:0.9rem;'>{$c['username']}</span>
                <small class='text-muted' style='font-size:0.75rem;'>".date('d/m/y', strtotime($c['created_at']))."</small>
            </div>
            <p class='mb-0 text-dark' style='font-size:0.95rem; line-height:1.5;'>".nl2br(htmlspecialchars($c['komentar']))."</p>
        </div>
    </div>";
}