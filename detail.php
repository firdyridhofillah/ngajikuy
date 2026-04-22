<?php
require_once 'config/database.php';
require_once 'includes/functions.php';

$nomor = $_GET['nomor'] ?? 1; // Ambil nomor dari URL, default ke 1 (Al-Fatihah)
$surah = getDetailSurah($nomor);

if (!$surah) {
    die("Surah tidak ditemukan atau koneksi bermasalah.");
}
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= $surah['namaLatin'] ?> - NgajiKuy</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Amiri&family=Inter:wght@400;600;700&display=swap" rel="stylesheet">
    <style>
        body { font-family: 'Inter', sans-serif; background-color: #f8f9fa; }
        .arabic-text { 
            font-family: 'Amiri', serif; 
            font-size: 2.5rem; 
            line-height: 1.8; 
            text-align: right; 
            color: #2d6a4f;
            word-spacing: 5px;
        }
        .verse-card { border: none; border-radius: 15px; border-bottom: 1px solid #dee2e6; }
        .nomor-ayat { 
            display: inline-block; 
            width: 35px; height: 35px; 
            border: 2px solid #2d6a4f; 
            border-radius: 50%; 
            text-align: center; 
            line-height: 31px; 
            font-weight: bold; 
            color: #2d6a4f;
        }
        .bismillah { font-family: 'Amiri', serif; font-size: 2rem; color: #2d6a4f; }
    </style>
</head>
<body>

<nav class="navbar navbar-light bg-white shadow-sm sticky-top">
    <div class="container">
        <a class="btn btn-outline-success btn-sm" href="index.php">← Kembali</a>
        <span class="navbar-brand mb-0 h1 mx-auto"><?= $surah['namaLatin'] ?> (<?= $surah['nama'] ?>)</span>
    </div>
</nav>

<div class="container mt-4">
    <div class="card bg-success text-white text-center p-4 mb-4 shadow-sm" style="border-radius: 20px; background: linear-gradient(135deg, #2d6a4f, #40916c);">
        <h2 class="fw-bold"><?= $surah['namaLatin'] ?></h2>
        <p class="mb-0 small"><?= $surah['arti'] ?> • <?= $surah['jumlahAyat'] ?> Ayat</p>
        <hr class="opacity-25">
        <audio controls class="w-100 mt-2" style="height: 35px;">
            <source src="<?= $surah['audioFull']['05'] ?>" type="audio/mpeg">
        </audio>
    </div>

    <?php if ($nomor != 1 && $nomor != 9): ?>
        <div class="text-center mb-5 bismillah">بِسْمِ اللّٰهِ الرَّحْمٰنِ الرَّحِيْمِ</div>
    <?php endif; ?>

    <?php foreach ($surah['ayat'] as $ayat): ?>
    <div class="card verse-card mb-3 shadow-sm">
        <div class="card-body p-4">
            <div class="d-flex justify-content-between mb-4">
                <span class="nomor-ayat"><?= $ayat['nomorAyat'] ?></span>
                <div class="arabic-text w-100"><?= $ayat['teksArab'] ?></div>
            </div>
            <p class="text-success small mb-1"><i><?= $ayat['teksLatin'] ?></i></p>
            <p class="text-muted mb-0"><?= $ayat['teksIndonesia'] ?></p>
        </div>
    </div>
    <?php endforeach; ?>
</div>

<footer class="text-center py-5">
    <p class="text-muted small">NgajiKuy - Teruslah Membaca</p>
</footer>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>