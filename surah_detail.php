<?php
session_start();
if (!isset($_SESSION['user_id'])) { header("Location: login.php"); exit(); }
require_once 'config/database.php';

$no_surah = isset($_GET['no']) ? $_GET['no'] : 1;
$ch = curl_init("https://equran.id/api/v2/surat/" . $no_surah);
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
$res = curl_exec($ch);
curl_close($ch);
$data = json_decode($res, true);
$surah = $data['data'] ?? null;

$title = "Surah " . $surah['namaLatin'];
include 'includes/header.php';
?>

<style>
    :root { --p-green: #00a86b; --p-dark: #1e293b; }
    body { background-color: #f8fafc; overflow-x: hidden; }

    /* Nav Header Clean */
    .nav-header-fixed {
        position: fixed; top: 0; left: 0; right: 0;
        background: var(--p-green); color: white;
        z-index: 1050; padding: 12px 20px; transition: 0.3s;
    }
    .nav-header-fixed.scrolled { 
        background: rgba(255, 255, 255, 0.9); backdrop-filter: blur(10px); 
        color: var(--p-dark); box-shadow: 0 4px 20px rgba(0,0,0,0.08); 
    }

    /* Navbar Buttons */
    .btn-clean {
        background: none !important; border: none !important; outline: none !important;
        box-shadow: none !important; color: inherit; display: flex; align-items: center; justify-content: center;
        transition: 0.3s; cursor: pointer;
    }
    
    /* Box Menu Style */
    .back-box {
        width: 40px; height: 40px; border-radius: 12px;
        background: rgba(255,255,255,0.15); border: 1px solid rgba(255,255,255,0.2);
    }
    .scrolled .back-box { background: #f1f5f9; border-color: #e2e8f0; color: var(--p-dark); }

    /* Hamburger Animation: Berjejer ke Samping */
    .menu-wrapper { display: flex; align-items: center; gap: 8px; }
    .btn-nav-ani {
        width: 38px; height: 38px; border-radius: 10px;
        background: rgba(255,255,255,0.2); color: white;
        display: none; transform: scale(0); transition: 0.4s cubic-bezier(0.175, 0.885, 0.32, 1.275);
    }
    .scrolled .btn-nav-ani { background: #f1f5f9; color: var(--p-dark); }
    .menu-wrapper.active .btn-nav-ani { display: flex; transform: scale(1); }

    /* Bintang Islami 8 Sudut */
    .star-wrapper { position: relative; width: 45px; height: 45px; display: flex; align-items: center; justify-content: center; margin-bottom: 10px; }
    .star-islamic {
        position: absolute; width: 32px; height: 32px; background: #f0fdf4; border: 2px solid var(--p-green);
        transform: rotate(45deg); border-radius: 4px;
    }
    .star-islamic::after { content: ""; position: absolute; inset: -2px; border: 2px solid var(--p-green); transform: rotate(45deg); border-radius: 4px; }
    .star-number { position: relative; z-index: 2; font-weight: 800; font-size: 0.8rem; color: var(--p-green); }

    /* Content Styling */
    .surah-banner { background: var(--p-green); color: white; text-align: center; padding: 110px 20px 60px; border-radius: 0 0 50px 50px; }
    .card-ayat { background: white; border-radius: 24px; padding: 30px; margin-bottom: 20px; border: 1px solid #f1f5f9; transition: 0.3s; }
    .card-ayat:hover { border-color: var(--p-green); box-shadow: 0 10px 30px rgba(0,168,107,0.05); }
    .arabic-text { font-family: 'Amiri', serif; font-size: 32px; text-align: right; line-height: 2.5; color: var(--p-dark); }
    
    /* Audio Bar */
    #audioBar {
        position: fixed; bottom: -150px; left: 0; right: 0; background: white; padding: 20px 25px; z-index: 2005;
        box-shadow: 0 -10px 40px rgba(0,0,0,0.1); transition: 0.5s; border-radius: 35px 35px 0 0;
    }
    #audioBar.active { bottom: 0; }

    /* Panel Kandungan Surah & Setting */
    .custom-panel { position: fixed; inset: auto 0 -100% 0; background: white; border-radius: 35px 35px 0 0; z-index: 2100; transition: 0.4s; padding: 35px 25px; }
    .custom-panel.active { bottom: 0; }
    .overlay-blur { position: fixed; inset: 0; background: rgba(0,0,0,0.3); backdrop-filter: blur(6px); z-index: 2099; display: none; }
    .hide-tr { display: none !important; }
</style>

<div class="nav-header-fixed d-flex align-items-center justify-content-between" id="navbar">
    <a href="quran.php" class="btn-clean back-box"><i class="bi bi-arrow-left fs-5"></i></a>
    
    <div class="menu-wrapper" id="navMenu">
        <button class="btn-clean btn-nav-ani" onclick="openPanel('setting')"><i class="bi bi-sliders"></i></button>
        <button class="btn-clean btn-nav-ani" onclick="openAudioPlayer('surah')"><i class="bi bi-play-circle"></i></button>
        <button class="btn-clean btn-nav-ani" onclick="openPanel('info')"><i class="bi bi-info-circle"></i></button>
        
        <button class="btn-clean ms-2" onclick="toggleHamburger()" style="font-size: 1.8rem;">
            <i class="bi bi-grid-fill" id="hamIcon"></i>
        </button>
    </div>
</div>

<div class="surah-banner shadow-sm">
    <h1 style="font-family: 'Amiri', serif; font-size: 4rem; margin-bottom: 0;"><?= $surah['nama'] ?></h1>
    <h2 class="fw-bold mt-1"><?= $surah['namaLatin'] ?></h2>
    <p class="text-white-50 fw-medium mb-3"><?= $surah['arti'] ?></p>
    <div class="d-flex justify-content-center gap-2">
        <span class="badge rounded-pill bg-white text-success px-3 py-2 fw-bold shadow-sm"><?= $surah['tempatTurun'] ?></span>
        <span class="badge rounded-pill bg-white text-success px-3 py-2 fw-bold shadow-sm"><?= $surah['jumlahAyat'] ?> Ayat</span>
    </div>
</div>

<div class="container py-4 mb-5">
    <?php foreach ($surah['ayat'] as $ayat): ?>
        <div class="card-ayat shadow-sm">
            <div class="d-flex justify-content-between align-items-start">
                <div class="star-wrapper">
                    <div class="star-islamic"></div>
                    <div class="star-number"><?= $ayat['nomorAyat'] ?></div>
                </div>
                <div class="d-flex gap-3 text-muted">
                    <i class="bi bi-play-circle btn-clean fs-4" onclick="openAudioPlayer('ayat', '<?= $ayat['nomorAyat'] ?>', <?= htmlspecialchars(json_encode($ayat['audio'])) ?>)"></i>
                    <i class="bi bi-journal-text btn-clean fs-4" onclick="toggleTafsir(<?= $ayat['nomorAyat'] ?>)"></i>
                    <i class="bi bi-bookmark-star btn-clean fs-4" onclick="saveBookmark(<?= $no_surah ?>, <?= $ayat['nomorAyat'] ?>, '<?= $surah['namaLatin'] ?>')"></i>
                </div>
            </div>
            <div class="arabic-text mb-4 mt-2"><?= $ayat['teksArab'] ?></div>
            <p class="translation-text text-muted lh-lg mb-0"><?= $ayat['teksIndonesia'] ?></p>
            <div class="tafsir-box" id="tafsir-<?= $ayat['nomorAyat'] ?>" style="display:none; background:#f0fdf4; padding:20px; border-radius:15px; margin-top:20px; border-left:5px solid var(--p-green); color: #334155;"> Memuat tafsir... </div>
        </div>
    <?php endforeach; ?>
</div>

<div id="audioBar">
    <div class="d-flex align-items-center justify-content-between mb-3">
        <div class="d-flex align-items-center gap-3">
            <div class="bg-success text-white rounded-circle d-flex align-items-center justify-content-center" style="width:45px; height:45px;">
                <i class="bi bi-music-note-beamed fs-5"></i>
            </div>
            <div>
                <h6 class="mb-1 fw-bold text-dark" id="audioTitle">Judul</h6>
                <select class="btn-clean fw-bold text-muted p-0" id="qariSelect" onchange="changeQari()" style="font-size: 0.8rem;">
                    <option value="05">Misyari Rasyid</option>
                    <option value="03">Abdurrahman as-Sudais</option>
                    <option value="01">Abdullah Al-Juhany</option>
                </select>
            </div>
        </div>
        <div class="d-flex align-items-center gap-3">
            <i class="bi bi-pause-circle-fill text-success" style="font-size: 2.8rem;" id="playPauseBtn" onclick="toggleAudio()"></i>
            <i class="bi bi-x-lg text-muted fs-5" onclick="closeAudio()"></i>
        </div>
    </div>
    <div class="progress" style="height: 6px; border-radius: 10px; background: #f1f5f9;">
        <div id="audioProgress" class="progress-bar bg-success" style="width: 0%; border-radius: 10px;"></div>
    </div>
</div>

<div class="overlay-blur" id="overlay" onclick="closePanel()"></div>

<div class="custom-panel" id="panel-info">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h5 class="fw-bold mb-0 text-dark"><i class="bi bi-info-circle me-2 text-success"></i> Kandungan Surah</h5>
        <i class="bi bi-x-lg text-muted" onclick="closePanel()"></i>
    </div>
    <div class="small text-muted lh-lg mb-4" style="max-height: 45vh; overflow-y: auto; text-align: justify; padding-right: 10px;">
        <?= !empty($surah['deskripsi']) ? $surah['deskripsi'] : "Deskripsi tidak tersedia untuk surah ini." ?>
    </div>
    <button class="btn btn-success w-100 rounded-pill py-3 fw-bold" onclick="closePanel()">Tutup</button>
</div>

<div class="custom-panel" id="panel-setting">
    <h5 class="fw-bold mb-4 text-dark"><i class="bi bi-sliders me-2 text-success"></i> Pengaturan Bacaan</h5>
    <div class="form-check form-switch mb-4">
        <input class="form-check-input" type="checkbox" id="swTr" checked onchange="document.querySelectorAll('.translation-text').forEach(el=>el.classList.toggle('hide-tr'))">
        <label class="form-check-label fw-bold small text-muted">Tampilkan Terjemahan</label>
    </div>
    <div class="mb-4">
        <label class="small fw-bold text-muted d-block mb-2">Ukuran Arab</label>
        <input type="range" class="form-range" min="20" max="60" value="32" oninput="document.querySelectorAll('.arabic-text').forEach(el=>el.style.fontSize=this.value+'px')">
    </div>
    <div class="mb-4">
        <label class="small fw-bold text-muted d-block mb-2">Ukuran Terjemah</label>
        <input type="range" class="form-range" min="12" max="28" value="16" oninput="document.querySelectorAll('.translation-text').forEach(el=>el.style.fontSize=this.value+'px')">
    </div>
    <button class="btn btn-success w-100 rounded-pill py-3 fw-bold" onclick="closePanel()">Simpan</button>
</div>

<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<script>
    let player = new Audio();
    let currentAudioData = null;

    // Header Scroll
    window.onscroll = () => document.getElementById('navbar').classList.toggle('scrolled', window.scrollY > 50);

    // FIX Hamburger Berjejer
    function toggleHamburger() {
        const menu = document.getElementById('navMenu');
        const icon = document.getElementById('hamIcon');
        menu.classList.toggle('active');
        
        if(menu.classList.contains('active')) {
            icon.classList.replace('bi-grid-fill', 'bi-x-lg');
        } else {
            icon.classList.replace('bi-x-lg', 'bi-grid-fill');
        }
    }

    // Panel Logic
    function openPanel(id) { 
        document.getElementById('overlay').style.display='block'; 
        document.getElementById('panel-'+id).classList.add('active'); 
    }
    function closePanel() { 
        document.getElementById('overlay').style.display='none'; 
        document.querySelectorAll('.custom-panel').forEach(p=>p.classList.remove('active')); 
    }

    // Audio Logic
    function openAudioPlayer(type, identifier = '', audioData = null) {
        if (type === 'surah') {
            currentAudioData = <?= json_encode($surah['audioFull']) ?>;
            document.getElementById('audioTitle').innerText = "Full Surah";
        } else {
            currentAudioData = audioData;
            document.getElementById('audioTitle').innerText = "Ayat " + identifier;
        }
        changeQari();
        document.getElementById('audioBar').classList.add('active');
    }

    function changeQari() {
        let qari = document.getElementById('qariSelect').value;
        player.src = currentAudioData[qari];
        player.play();
        document.getElementById('playPauseBtn').className = "bi bi-pause-circle-fill text-success";
    }

    function toggleAudio() {
        if (player.paused) { player.play(); document.getElementById('playPauseBtn').className = "bi bi-pause-circle-fill text-success"; }
        else { player.pause(); document.getElementById('playPauseBtn').className = "bi bi-play-circle-fill text-success"; }
    }

    function closeAudio() { player.pause(); document.getElementById('audioBar').classList.remove('active'); }

    player.ontimeupdate = () => {
        let pct = (player.currentTime / player.duration) * 100;
        document.getElementById('audioProgress').style.width = pct + '%';
    };

    function toggleTafsir(no) {
        let box = document.getElementById('tafsir-'+no);
        if(box.style.display==='block') { box.style.display='none'; } 
        else { 
            box.style.display='block'; 
            fetch(`https://equran.id/api/v2/tafsir/${<?= $no_surah ?>}`).then(r=>r.json()).then(d=>{
                box.innerText = d.data.tafsir.find(t=>t.ayat==no).teks;
            });
        }
    }

    function saveBookmark(sNo, aNo, sName) {
        fetch('proses_last_read.php', {
            method: 'POST',
            headers: {'Content-Type': 'application/x-www-form-urlencoded'},
            body: `surah_no=${sNo}&ayat_no=${aNo}&surah_name=${sName}`
        }).then(() => {
            Swal.fire({ icon: 'success', title: 'Berhasil', text: 'Ditandai sebagai terakhir baca!', showConfirmButton: false, timer: 1500 });
        });
    }
</script>

<?php include 'includes/footer.php'; ?>