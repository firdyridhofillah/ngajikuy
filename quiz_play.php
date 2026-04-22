<?php
session_start();
if (!isset($_SESSION['user_id'])) { header("Location: login.php"); exit(); }
require_once 'config/database.php';

$mode = $_GET['mode'] ?? 'sambung';
$juz_start = (int)($_GET['juz_start'] ?? 1);
$juz_end = (int)($_GET['juz_end'] ?? 30);
$limit = (int)($_GET['limit'] ?? 10);

include 'includes/header.php';
?>

<style>
    @import url('https://fonts.googleapis.com/css2?family=Amiri&display=swap');

    /* Teks Arab untuk Soal (Besar) */
    .arab-soal { 
        font-family: 'Amiri', serif; 
        font-size: 2.2rem; 
        line-height: 1.8; 
        direction: rtl; 
    }

    /* Teks Arab untuk Pilihan Jawaban (Lebih Kecil & Rapi) */
    .option-arab { 
        font-family: 'Amiri', serif; 
        font-size: 1.35rem !important; 
        font-weight: normal !important; 
        direction: rtl; 
    }

    .option-btn { 
        background: white; 
        border: 2px solid #e2e8f0; 
        border-radius: 15px; 
        padding: 15px; 
        font-weight: 600; 
        min-height: 75px; 
        width: 100%;
        transition: all 0.2s ease;
        display: flex;
        align-items: center;
        justify-content: center;
        text-align: center;
    }

    .option-btn:hover:not(:disabled) { 
        border-color: #00a86b; 
        background: #f0fdf4; 
        transform: translateY(-2px);
    }

    .option-btn:disabled { 
        opacity: 0.7; 
        cursor: not-allowed; 
    }

    #timer-bar { 
        height: 8px; 
        background: #00a86b; 
        width: 100%; 
        transition: linear 1s; 
        border-radius: 10px; 
    }

    .review-card { 
        border-radius: 15px; 
        margin-bottom: 12px; 
        border-left: 6px solid #ccc; 
        transition: 0.3s;
    }

    .review-correct { border-left-color: #22c55e; background: #f0fdf4; }
    .review-wrong { border-left-color: #ef4444; background: #fef2f2; }
</style>

<div class="container py-4">
    <div id="game-area">
        <div class="d-flex justify-content-between align-items-center mb-2">
            <div>
                <h5 class="fw-bold text-success mb-0"><?= $mode == 'sambung' ? 'Sambung Ayat' : 'Tebak Ayat' ?></h5>
                <small class="text-muted">Qori: Misyari Rasyid Al-Afasy</small>
            </div>
            <span class="badge bg-white text-dark border shadow-sm px-3 py-2">
                Soal <span id="currentQ">1</span>/<?= $limit ?>
            </span>
        </div>
        
        <div class="progress mb-3" style="height: 8px; background-color: #e2e8f0; border-radius: 10px;">
            <div id="timer-bar" class="progress-bar"></div>
        </div>

        <div id="quizBox" class="card border-0 shadow-sm rounded-4 p-4 text-center">
            <div id="loader">
                <div class="spinner-border text-success mb-3" role="status"></div>
                <p class="text-muted fw-bold">Menyiapkan ayat untukmu...</p>
            </div>

            <div id="gameContent" style="display:none;">
                <div class="mb-4">
                    <button class="btn btn-success rounded-circle p-3 mb-3 shadow-lg" id="btn-audio">
                        <i class="bi bi-volume-up-fill fs-2"></i>
                    </button>
                    <div class="arab-soal mb-4 p-3 bg-light rounded-4 shadow-sm" id="ayatTeks"></div>
                    <p class="text-muted small fw-bold" id="instruksi"></p>
                </div>
                <div class="row g-3" id="optionsGrid"></div>
            </div>
        </div>
    </div>

    <div id="result-area" style="display:none;">
        <div class="card border-0 shadow-lg rounded-4 p-4 text-center mb-4 bg-white">
            <h3 class="fw-bold text-dark">Ringkasan Hasil</h3>
            <div class="row my-4">
                <div class="col-6">
                    <h1 class="display-4 fw-bold text-success" id="final-score">0</h1>
                    <p class="text-muted small fw-bold text-uppercase">Highscore</p>
                </div>
                <div class="col-6 border-start">
                    <h1 class="display-4 fw-bold text-primary" id="final-accuracy">0%</h1>
                    <p class="text-muted small fw-bold text-uppercase">Ketepatan</p>
                </div>
            </div>
            <div id="time-stat" class="alert alert-success border-0 small mb-4 py-2"></div>
            <div class="d-grid gap-2">
                <button class="btn btn-success rounded-pill py-3 fw-bold shadow" onclick="location.reload()">
                    <i class="bi bi-arrow-clockwise me-2"></i> MAIN LAGI
                </button>
                <a href="quiz.php" class="btn btn-outline-secondary rounded-pill py-2 fw-bold">KEMBALI KE MENU</a>
            </div>
        </div>

        <h5 class="fw-bold mb-3"><i class="bi bi-journal-check me-2 text-success"></i>Koreksi Hafalan:</h5>
        <div id="review-list"></div>
    </div>
</div>

<script>
let questions = [];
let currentIndex = 0;
let userAnswers = [];
let timeLeft = 30;
let timerInterval;
let currentAudio = null;
let startTime;
const mode = "<?= $mode ?>";

async function fetchQuestions() {
    try {
        // Daftar surah acak untuk sample (Juz disesuaikan di sisi API atau logic database)
        const surahIds = [1, 36, 55, 67, 78, 87, 93, 112, 114].sort(() => 0.5 - Math.random());
        
        for(let i=0; i < <?= $limit ?>; i++) {
            let sId = surahIds[i % surahIds.length];
            let res = await fetch(`https://equran.id/api/v2/surat/${sId}`);
            let d = await res.json();
            let ayats = d.data.ayat;
            
            let idx = Math.floor(Math.random() * (ayats.length - 1));
            let ayatSekarang = ayats[idx];
            let ayatBerikutnya = ayats[idx+1];

            let qData = {
                soalArab: ayatSekarang.teksArab,
                audio: ayatSekarang.audio['01'], // 01 = Misyari Rasyid Al-Afasy
                jawabanBenar: mode === "sambung" ? ayatBerikutnya.teksArab : `${d.data.namaLatin} Ayat ${ayatSekarang.nomorAyat}`,
                options: []
            };

            // Setup Options
            let opts = [qData.jawabanBenar];
            if(mode === "sambung") {
                opts.push("عَلِيمٌ حَكِيمٌ", "إِنَّ اللَّهَ غَفُورٌ رَحِيمٌ", "بِمَا تَعْمَلُونَ بَصِيرٌ");
            } else {
                opts.push("Al-Baqarah Ayat 10", "Yasin Ayat 5", "An-Naba Ayat 12");
            }
            qData.options = opts.sort(() => 0.5 - Math.random());
            questions.push(qData);
        }
        initGame();
    } catch(e) { 
        alert("Gagal memuat data ayat. Pastikan internetmu aktif ya, Fir!"); 
    }
}

function initGame() {
    startTime = Date.now();
    document.getElementById('loader').style.display = 'none';
    document.getElementById('gameContent').style.display = 'block';
    renderQuestion();
}

function renderQuestion() {
    if(currentIndex >= questions.length) return showResults();

    const q = questions[currentIndex];
    document.getElementById('currentQ').innerText = currentIndex + 1;
    document.getElementById('ayatTeks').innerText = q.soalArab;
    document.getElementById('instruksi').innerText = mode === "sambung" ? "PILIH KELANJUTAN AYATNYA:" : "NAMA SURAH & AYAT DI ATAS:";

    const grid = document.getElementById('optionsGrid');
    grid.innerHTML = "";
    
    q.options.forEach(opt => {
        const btn = document.createElement('button');
        btn.className = `option-btn ${mode === 'sambung' ? 'option-arab' : ''}`;
        btn.innerText = opt;
        btn.onclick = () => handleAnswer(opt);
        const col = document.createElement('div');
        col.className = "col-12 col-md-6";
        col.appendChild(btn);
        grid.appendChild(col);
    });

    playAudio(q.audio);
    startTimer();
}

function startTimer() {
    clearInterval(timerInterval);
    timeLeft = 30;
    const bar = document.getElementById('timer-bar');
    bar.style.width = "100%";
    
    timerInterval = setInterval(() => {
        timeLeft--;
        bar.style.width = (timeLeft / 30 * 100) + "%";
        if(timeLeft <= 0) handleAnswer("Waktu Habis");
    }, 1000);
}

function playAudio(url) {
    if(currentAudio) currentAudio.pause();
    currentAudio = new Audio(url);
    currentAudio.play();
    document.getElementById('btn-audio').onclick = () => { 
        currentAudio.currentTime = 0; 
        currentAudio.play(); 
    };
}

function handleAnswer(chosen) {
    // Disable all buttons to prevent spam
    const btns = document.querySelectorAll('.option-btn');
    btns.forEach(b => b.disabled = true);
    
    const q = questions[currentIndex];
    userAnswers.push({
        teksSoal: q.soalArab,
        jawabanUser: chosen,
        jawabanBenar: q.jawabanBenar,
        isCorrect: chosen === q.jawabanBenar
    });

    currentIndex++;
    // Delay sedikit agar tidak terlalu kaget saat pindah soal
    setTimeout(renderQuestion, 400);
}

function showResults() {
    clearInterval(timerInterval);
    if(currentAudio) currentAudio.pause();
    
    document.getElementById('game-area').style.display = 'none';
    document.getElementById('result-area').style.display = 'block';

    const correctCount = userAnswers.filter(a => a.isCorrect).length;
    const totalSoal = questions.length;
    const score = Math.round((correctCount / totalSoal) * 100);
    const duration = Math.round((Date.now() - startTime) / 1000);

    document.getElementById('final-score').innerText = score;
    document.getElementById('final-accuracy').innerText = score + "%";
    document.getElementById('time-stat').innerHTML = `<i class="bi bi-clock-history me-2"></i>Selesai dalam <b>${duration} detik</b>`;

    const review = document.getElementById('review-list');
    review.innerHTML = "";
    userAnswers.forEach((item, i) => {
        review.innerHTML += `
            <div class="card review-card p-3 shadow-sm ${item.isCorrect ? 'review-correct' : 'review-wrong'}">
                <div class="d-flex justify-content-between align-items-center mb-2">
                    <span class="small fw-bold text-muted">SOAL #${i+1}</span>
                    <span class="badge ${item.isCorrect ? 'bg-success' : 'bg-danger'} text-uppercase">
                        ${item.isCorrect ? 'Benar' : 'Salah'}
                    </span>
                </div>
                <div class="arab-soal text-end mb-3 p-2 bg-white rounded shadow-sm" style="font-size: 1.6rem;">${item.teksSoal}</div>
                <div class="small">
                    <div class="mb-1">Jawaban Anda: <b class="${item.isCorrect ? 'text-success' : 'text-danger'}">${item.jawabanUser}</b></div>
                    ${!item.isCorrect ? `<div class="text-success fw-bold">Jawaban Benar: ${item.jawabanBenar}</div>` : ''}
                </div>
            </div>
        `;
    });

    // Panggil fungsi simpan skor
    saveToDB(score, correctCount, totalSoal);
}

function saveToDB(score, correct, total) {
    let fd = new FormData();
    fd.append('score', score);
    fd.append('mode', mode);
    fd.append('total_soal', total);
    fd.append('total_benar', correct);
    fd.append('ketepatan', (correct / total) * 100);

    fetch('save_score.php', { 
        method: 'POST', 
        body: fd 
    })
    .then(res => res.text())
    .then(msg => console.log("Leaderboard Update:", msg));
}

window.onload = fetchQuestions;
</script>

<?php include 'includes/footer.php'; ?>