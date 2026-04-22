<?php
session_start();
if (!isset($_POST['query'])) { header("Location: quran.php"); exit(); }

$query_user = $_POST['query'];

// Tembak ke API Vector
$url = "https://equran.id/api/vector"; // Ganti dengan URL asli API-nya
$payload = json_encode([
    "query" => $query_user,
    "limit" => 5,
    "types" => ["ayat"],
    "minScore" => 0.4
]);

$ch = curl_init($url);
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
curl_setopt($ch, CURLOPT_POST, true);
curl_setopt($ch, CURLOPT_POSTFIELDS, $payload);
curl_setopt($ch, CURLOPT_HTTPHEADER, ['Content-Type: application/json']);
$res = curl_exec($ch);
curl_close($ch);

$hasil = json_decode($res, true);

$title = "Hasil Pencarian AI";
include 'includes/header.php';
?>

<div class="container py-4">
    <div class="mb-4">
        <h5 class="fw-bold">Hasil Pencarian AI</h5>
        <small class="text-muted">Menampilkan hasil untuk: "<?= htmlspecialchars($query_user) ?>"</small>
    </div>

    <?php if(isset($hasil['data'])): foreach($hasil['data'] as $r): ?>
        <div class="card-main mb-3">
            <div class="d-flex justify-content-between mb-2">
                <span class="badge bg-success rounded-pill small">Surah <?= $r['surat_name'] ?>:<?= $r['ayat_no'] ?></span>
                <small class="text-muted">Skor Relevansi: <?= round($r['score'] * 100) ?>%</small>
            </div>
            <p class="text-end fw-bold h4 mb-3" style="font-family: 'Amiri', serif; line-height: 1.8;"><?= $r['ar'] ?></p>
            <p class="small text-muted mb-0"><?= $r['tr'] ?></p>
        </div>
    <?php endforeach; else: ?>
        <div class="text-center py-5">
            <i class="bi bi-search" style="font-size: 3rem; color: #dee2e6;"></i>
            <p class="text-muted mt-3">Tidak ditemukan ayat yang relevan.</p>
        </div>
    <?php endif; ?>
    
    <a href="quran.php" class="btn btn-outline-success w-100 rounded-pill fw-bold">Kembali</a>
</div>

<?php include 'includes/footer.php'; ?>