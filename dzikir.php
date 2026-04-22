<?php
session_start();
if (!isset($_SESSION['user_id'])) { header("Location: login.php"); exit(); }
$title = "Tasbih Digital";
include 'includes/header.php';
?>

<style>
    :root { --p-green: #00a86b; --p-dark: #1e293b; }
    body { background-color: #f8fafc; }

    /* Hero Section - Tempat Angka & Tombol Utama */
    .dzikir-hero {
        background: linear-gradient(135deg, var(--p-green), #00d284);
        padding: 20px 20px 40px;
        border-radius: 0 0 40px 40px;
        color: white;
        text-align: center;
        box-shadow: 0 10px 30px rgba(0,168,107,0.2);
    }

    /* Ukuran Angka Besar */
    #displayNum { 
        font-size: 6.5rem; 
        font-weight: 900; 
        margin: 10px 0; 
        font-family: 'JetBrains Mono', monospace;
        line-height: 1;
    }

    /* Tombol Utama (Bentuk Lingkaran di Tengah Hero) */
    .tap-container {
        display: flex;
        justify-content: center;
        margin-top: 20px;
    }
    
    .btn-main-tap {
        width: 140px;
        height: 140px;
        background: white;
        border-radius: 50%;
        display: flex;
        justify-content: center;
        align-items: center;
        border: 6px solid rgba(255,255,255,0.3);
        box-shadow: 0 10px 25px rgba(0,0,0,0.15);
        cursor: pointer;
        transition: 0.1s;
    }
    .btn-main-tap:active { transform: scale(0.9); box-shadow: 0 5px 10px rgba(0,0,0,0.2); }
    .btn-main-tap i { font-size: 3.5rem; color: var(--p-green); }

    /* Progress Bar Kecil */
    .pg-bar-container {
        width: 60%;
        height: 6px;
        background: rgba(255,255,255,0.2);
        margin: 15px auto;
        border-radius: 10px;
        overflow: hidden;
    }
    #pgBar { width: 0%; height: 100%; background: white; transition: 0.3s; }

    /* Daftar Bacaan */
    .dzikir-content { padding: 30px 20px; }
    .card-dzikir {
        background: white; border-radius: 20px; padding: 18px; margin-bottom: 12px;
        border: 1.5px solid #f1f5f9; cursor: pointer; transition: 0.3s;
        display: flex; align-items: center; justify-content: space-between;
    }
    .card-dzikir.active { border-color: var(--p-green); background: #f0fdf4; box-shadow: 0 5px 15px rgba(0,168,107,0.05); }

    /* Alert Melayang */
    .custom-alert {
        position: fixed; top: -100px; left: 50%; transform: translateX(-50%);
        background: #1e293b; color: white; padding: 12px 25px; border-radius: 50px;
        z-index: 9999; transition: 0.5s cubic-bezier(0.175, 0.885, 0.32, 1.275);
        box-shadow: 0 10px 20px rgba(0,0,0,0.2); font-weight: 600;
    }
    .custom-alert.show { top: 30px; }
</style>

<div id="alertBox" class="custom-alert">Alhamdulillah, target tercapai!</div>

<div class="dzikir-hero">
    <div class="d-flex justify-content-between align-items-center mb-2">
        <a href="quran.php" class="text-white fs-4"><i class="bi bi-chevron-left"></i></a>
        <span class="fw-bold small" id="titleTask">PILIH BACAAN</span>
        <i class="bi bi-arrow-counterclockwise fs-4" onclick="resetCounter()"></i>
    </div>

    <div id="displayNum">0</div>
    <div class="fw-bold opacity-75" id="targetInfo">Target: -</div>
    
    <div class="pg-bar-container">
        <div id="pgBar"></div>
    </div>

    <div class="tap-container">
        <div class="btn-main-tap" id="btnTap">
            <i class="bi bi-fingerprint"></i>
        </div>
    </div>
</div>

<div class="dzikir-content">
    <div class="d-flex justify-content-between align-items-center mb-3">
        <h6 class="fw-bold mb-0">Daftar Dzikir</h6>
        <div class="btn-group">
            <button class="btn btn-sm btn-success rounded-pill px-3 me-2" onclick="loadData('pagi')">Pagi</button>
            <button class="btn btn-sm btn-outline-success rounded-pill px-3" onclick="loadData('petang')">Petang</button>
        </div>
    </div>

    <div id="listContainer">
        </div>
</div>

<script>
    const dataDzikir = {
        pagi: [
            { judul: "Subhanallah", target: 33 },
            { judul: "Alhamdulillah", target: 33 },
            { judul: "Allahu Akbar", target: 33 },
            { judul: "Astaghfirullah", target: 100 }
        ],
        petang: [
            { judul: "Sholawat", target: 10 },
            { judul: "Istighfar", target: 100 },
            { judul: "Laa Ilaha Illallah", target: 100 }
        ]
    };

    let counter = 0;
    let target = 0;
    let audio = new Audio('https://www.soundjay.com/buttons/sounds/button-16.mp3');

    function loadData(mode) {
        const container = document.getElementById('listContainer');
        container.innerHTML = "";
        dataDzikir[mode].forEach(item => {
            container.innerHTML += `
                <div class="card-dzikir" onclick="startDzikir('${item.judul}', ${item.target}, this)">
                    <div>
                        <div class="fw-bold text-dark">${item.judul}</div>
                        <div class="small text-muted">Target: ${item.target}x</div>
                    </div>
                    <i class="bi bi-play-circle-fill text-success fs-4"></i>
                </div>
            `;
        });
    }

    function startDzikir(nama, goal, el) {
        counter = 0;
        target = goal;
        document.getElementById('displayNum').innerText = "0";
        document.getElementById('pgBar').style.width = "0%";
        document.getElementById('targetInfo').innerText = "Target: " + goal + "x";
        document.getElementById('titleTask').innerText = nama.toUpperCase();

        document.querySelectorAll('.card-dzikir').forEach(c => c.classList.remove('active'));
        el.classList.add('active');
        
        window.scrollTo({ top: 0, behavior: 'smooth' });
    }

    document.getElementById('btnTap').addEventListener('click', function() {
        if(target === 0) {
            showAlert("Pilih bacaan di bawah dulu, Fir!");
            return;
        }

        if(counter < target) {
            counter++;
            document.getElementById('displayNum').innerText = counter;
            
            // Suara & Getar
            audio.currentTime = 0; audio.play();
            if(navigator.vibrate) navigator.vibrate(40);

            // Progress Bar
            let percent = (counter / target) * 100;
            document.getElementById('pgBar').style.width = percent + "%";

            // Selesai
            if(counter === target) {
                showAlert("Alhamdulillah! Lanjut dzikir berikutnya?");
            }
        }
    });

    function showAlert(msg) {
        const box = document.getElementById('alertBox');
        box.innerText = msg;
        box.classList.add('show');
        setTimeout(() => box.classList.remove('show'), 3000);
    }

    function resetCounter() {
        counter = 0;
        document.getElementById('displayNum').innerText = "0";
        document.getElementById('pgBar').style.width = "0%";
    }

    // Load awal
    loadData('pagi');
</script>

<?php include 'includes/footer.php'; ?>