<?php
session_start();
require_once 'config/database.php';

$artikel_id = $_GET['id'] ?? null;
if (!$artikel_id) { header("Location: index.php"); exit(); }

// Hitung View
if (!isset($_SESSION['viewed_'.$artikel_id])) {
    $koneksi->query("UPDATE artikel SET views = views + 1 WHERE id = $artikel_id");
    $_SESSION['viewed_'.$artikel_id] = true;
}

// Ambil Data
$query = $koneksi->query("SELECT a.*, u.username, 
          (SELECT COUNT(*) FROM artikel_likes WHERE artikel_id = a.id) as total_likes 
          FROM artikel a JOIN users u ON a.penulis_id = u.id WHERE a.id = $artikel_id");
$art = $query->fetch_assoc();

$title = $art['judul'];
include 'includes/header.php';
?>

<style>
    body { background-color: #ffffff; }
    .main-content { max-width: 700px; margin: 0 auto; }
    .article-header { border-bottom: 1px solid #eee; padding-bottom: 20px; margin-bottom: 30px; }
    .author-meta { display: flex; align-items: center; gap: 12px; margin-bottom: 20px; }
    .author-img { width: 45px; height: 45px; background: #00a86b; color: white; border-radius: 50%; display: flex; align-items: center; justify-content: center; font-weight: bold; }
    
    /* Gaya Tulisan ala Kompasiana */
    .article-body { 
        font-family: 'Georgia', serif; 
        font-size: 1.2rem; 
        line-height: 1.8; 
        color: #292929;
    }
    .article-body p { margin-bottom: 1.5rem; }
    .article-body img { max-width: 100%; border-radius: 8px; margin: 20px 0; }
    
    .interaction-bar { 
        position: sticky; bottom: 20px; background: white; 
        border: 1px solid #eee; border-radius: 50px; 
        padding: 10px 25px; display: flex; gap: 20px; 
        box-shadow: 0 4px 12px rgba(0,0,0,0.05); width: fit-content; margin: 40px auto;
    }
    .btn-interact { border: none; background: none; color: #666; font-weight: 500; cursor: pointer; }
    .btn-interact.liked { color: #ef4444; }
</style>

<div class="container py-5">
    <article class="main-content">
        <div class="article-header">
            <h1 class="fw-bold mb-4" style="font-size: 2.5rem; letter-spacing: -0.5px;"><?= htmlspecialchars($art['judul']) ?></h1>
            <div class="author-meta">
                <div class="author-img"><?= strtoupper(substr($art['username'], 0, 1)) ?></div>
                <div>
                    <span class="d-block fw-bold"><?= $art['username'] ?></span>
                    <small class="text-muted"><?= date('d M Y', strtotime($art['created_at'])) ?> • <?= $art['views'] ?> Baca</small>
                </div>
            </div>
        </div>

        <img src="uploads/<?= $art['thumbnail'] ?>" class="w-100 rounded-3 mb-4 shadow-sm">

        <div class="article-body">
            <?= $art['isi'] ?>
        </div>

        <div class="interaction-bar">
            <button class="btn-interact" id="btn-like" onclick="prosesLike(<?= $artikel_id ?>)">
                <i class="bi bi-heart-fill"></i> <span id="l-count"><?= $art['total_likes'] ?></span>
            </button>
            <button class="btn-interact" onclick="document.getElementById('komentar-section').scrollIntoView({behavior: 'smooth'})">
                <i class="bi bi-chat-dots-fill"></i> Komentar
            </button>
        </div>

        <div id="komentar-section" class="mt-5 pt-5 border-top">
            <h4 class="fw-bold mb-4">Komentar</h4>
            
            <?php if(isset($_SESSION['user_id'])): ?>
            <div class="mb-4">
                <textarea id="isi_komentar" class="form-control border-0 bg-light rounded-4 p-3" placeholder="Tulis komentar kamu..." rows="3"></textarea>
                <div class="text-end mt-2">
                    <button onclick="kirimKomen(<?= $artikel_id ?>)" class="btn btn-success rounded-pill px-4 fw-bold">Kirim</button>
                </div>
            </div>
            <?php endif; ?>

            <div id="box-komentar">
                </div>
        </div>
    </article>
</div>

<script>
// Pastikan semua fungsi ini memanggil file yang benar (ada di bawah)
function prosesLike(id) {
    fetch('proses_like.php?id=' + id)
    .then(r => r.json())
    .then(data => {
        if(data.status === 'success') {
            document.getElementById('l-count').innerText = data.total;
            document.getElementById('btn-like').classList.toggle('liked');
        } else { alert(data.message); }
    }).catch(() => alert("File proses_like.php tidak ditemukan!"));
}

function kirimKomen(id) {
    const text = document.getElementById('isi_komentar').value;
    if(!text) return;

    const fd = new FormData();
    fd.append('artikel_id', id);
    fd.append('komentar', text);

    fetch('proses_komentar.php', { method: 'POST', body: fd })
    .then(r => r.json())
    .then(data => {
        if(data.status === 'success') {
            document.getElementById('isi_komentar').value = '';
            muatKomentar(id);
        }
    }).catch(() => alert("File proses_komentar.php tidak ditemukan!"));
}

function muatKomentar(id) {
    fetch('ambil_komentar.php?id=' + id)
    .then(r => r.text())
    .then(html => {
        document.getElementById('box-komentar').innerHTML = html;
    });
}

// Jalankan muat komentar saat halaman dibuka
muatKomentar(<?= $artikel_id ?>);
</script>

<?php include 'includes/footer.php'; ?>