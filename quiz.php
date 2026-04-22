<?php
session_start();
include 'includes/header.php';
?>

<style>
    .card-mode {
        border: none; border-radius: 25px; transition: 0.3s; cursor: pointer;
        overflow: hidden; position: relative; height: 180px;
    }
    .card-mode:hover { transform: translateY(-10px); }
    .bg-sambung { background: linear-gradient(135deg, #00a86b, #00d284); }
    .bg-tebak { background: linear-gradient(135deg, #1e293b, #334155); }
    .card-mode i { position: absolute; right: -10px; bottom: -20px; font-size: 8rem; opacity: 0.2; }
</style>

<div class="container py-5">
    <h3 class="fw-bold mb-4">Pilih Mode Quiz</h3>
    
    <div class="row g-4">
        <div class="col-md-6">
            <a href="quiz_lobby.php?mode=sambung" class="text-decoration-none">
                <div class="card card-mode bg-sambung text-white p-4">
                    <h2 class="fw-bold">Sambung Ayat</h2>
                    <p class="opacity-75">Lanjutkan potongan ayat yang hilang.</p>
                    <i class="bi bi- megaphone"></i>
                </div>
            </a>
        </div>
        
        <div class="col-md-6">
            <a href="quiz_lobby.php?mode=tebak" class="text-decoration-none">
                <div class="card card-mode bg-tebak text-white p-4">
                    <h2 class="fw-bold">Tebak Ayat</h2>
                    <p class="opacity-75">Tebak nama surah dari audio/teks.</p>
                    <i class="bi bi-question-circle"></i>
                </div>
            </a>
        </div>
    </div>
</div>

<?php include 'includes/footer.php'; ?>