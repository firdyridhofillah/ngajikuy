<?php
session_start();
if (!isset($_SESSION['user_id'])) { header("Location: login.php"); exit(); }
require_once 'config/database.php';

// Menggunakan API Alternatif (Doa Harian)
$ch = curl_init("https://islamic-api-zhirrr.vercel.app/api/doaharian");
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
$res = curl_exec($ch);
curl_close($ch);
$data = json_decode($res, true);

// Di API ini datanya biasanya langsung di dalam ['data'] atau langsung di array utama
$daftar_doa = $data['data'] ?? $data ?? [];

$title = "Koleksi Doa Harian";
include 'includes/header.php';
?>

<style>
    :root { --p-green: #00a86b; --p-dark: #1e293b; }
    body { background-color: #f8fafc; font-family: 'Plus Jakarta Sans', sans-serif; }

    /* Header Styling */
    .header-gradient {
        background: linear-gradient(135deg, var(--p-green), #00d284);
        padding: 30px 20px 80px; border-radius: 0 0 40px 40px; color: white;
    }

    /* Floating Search */
    .search-wrapper { margin-top: -40px; padding: 0 20px; }
    .search-card {
        background: white; border-radius: 20px; padding: 5px 15px;
        box-shadow: 0 10px 30px rgba(0,0,0,0.05); border: 1px solid #f1f5f9;
    }

    /* Horizontal Category Slide */
    .cat-slide {
        display: flex; gap: 15px; overflow-x: auto; padding: 25px 20px;
        scrollbar-width: none;
    }
    .cat-slide::-webkit-scrollbar { display: none; }
    
    .cat-item {
        min-width: 100px; background: white; padding: 15px; border-radius: 20px;
        text-align: center; border: 1px solid #f1f5f9; transition: 0.3s; cursor: pointer;
    }
    .cat-item.active { background: var(--p-green); color: white; border-color: var(--p-green); transform: translateY(-5px); }
    .cat-item i { font-size: 1.8rem; display: block; margin-bottom: 8px; }
    .cat-item span { font-size: 0.75rem; font-weight: 700; }

    /* List Doa */
    .doa-card {
        background: white; border-radius: 24px; padding: 20px; margin-bottom: 15px;
        border: 1px solid #f1f5f9; transition: 0.3s; position: relative; overflow: hidden;
    }
    .doa-card:hover { border-color: var(--p-green); box-shadow: 0 12px 25px rgba(0,168,107,0.08); }
    .doa-card::before {
        content: '\F2B6'; font-family: "bootstrap-icons"; position: absolute;
        right: -10px; bottom: -10px; font-size: 4rem; color: var(--p-green); opacity: 0.03;
    }

    .arab-preview { font-family: 'Amiri', serif; font-size: 1.4rem; color: var(--p-green); text-align: right; margin-top: 10px; direction: rtl; }

    /* Modal Sheet */
    .panel-sheet {
        position: fixed; inset: auto 0 -100% 0; background: white;
        border-radius: 35px 35px 0 0; z-index: 2200; padding: 35px 25px;
        transition: 0.5s cubic-bezier(0.32, 0.72, 0, 1); max-height: 85vh; overflow-y: auto;
    }
    .panel-sheet.active { bottom: 0; }
    .overlay-blur { position: fixed; inset: 0; background: rgba(0,0,0,0.4); backdrop-filter: blur(8px); z-index: 2100; display: none; }
</style>

<div class="header-gradient">
    <div class="container d-flex align-items-center justify-content-between">
        <a href="index.php" class="text-white"><i class="bi bi-chevron-left fs-4"></i></a>
        <h5 class="fw-bold mb-0">Library Doa</h5>
        <div style="width: 24px;"></div>
    </div>
</div>

<div class="search-wrapper">
    <div class="search-card d-flex align-items-center">
        <i class="bi bi-search text-muted ms-2"></i>
        <input type="text" id="findDoa" class="form-control border-0 shadow-none py-3" placeholder="Cari doa harian...">
    </div>
</div>

<div class="cat-slide">
    <div class="cat-item active" onclick="filterCat('all', this)">
        <i class="bi bi-grid-1x2-fill"></i>
        <span>Semua</span>
    </div>
    <div class="cat-item" onclick="filterCat('pagi', this)">
        <i class="bi bi-brightness-high"></i>
        <span>Pagi</span>
    </div>
    <div class="cat-item" onclick="filterCat('malam', this)">
        <i class="bi bi-moon-stars"></i>
        <span>Malam</span>
    </div>
    <div class="cat-item" onclick="filterCat('sholat', this)">
        <i class="bi bi-mosque"></i>
        <span>Sholat</span>
    </div>
    <div class="cat-item" onclick="filterCat('makan', this)">
        <i class="bi bi-cup-hot"></i>
        <span>Makan</span>
    </div>
</div>

<div class="container pb-5">
    <div id="doaList">
        <?php foreach($daftar_doa as $d): 
            $title_doa = $d['title'] ?? $d['judul'] ?? 'Doa';
            $arabic = $d['arabic'] ?? $d['arab'] ?? '';
            $translation = $d['translation'] ?? $d['indo'] ?? '';
            $latin_text = $d['latin'] ?? '';
        ?>
            <div class="doa-card item-doa" 
                 data-title="<?= strtolower($title_doa) ?>"
                 onclick='openDoa(<?= json_encode([
                     "t" => $title_doa,
                     "a" => $arabic,
                     "tr" => $translation,
                     "l" => $latin_text
                 ], JSON_HEX_APOS | JSON_HEX_QUOT) ?>)'>
                <h6 class="fw-bold text-dark mb-1"><?= $title_doa ?></h6>
                <div class="arab-preview"><?= mb_strimwidth($arabic, 0, 75, "...") ?></div>
            </div>
        <?php endforeach; ?>
    </div>
</div>

<div class="overlay-blur" id="blurLayer" onclick="closeDoa()"></div>
<div class="panel-sheet" id="sheetDoa">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div class="bg-success bg-opacity-10 text-success px-3 py-1 rounded-pill small fw-bold">BACAAN DOA</div>
        <i class="bi bi-x-circle-fill text-muted fs-4" onclick="closeDoa()"></i>
    </div>
    
    <h4 class="fw-bold text-dark mb-4" id="shTitle"></h4>
    
    <div class="p-4 rounded-4 mb-4" style="background: #fcfdfd; border: 1px dashed #e2e8f0;">
        <div class="arab-preview" id="shArab" style="font-size: 2rem; line-height: 2.8; margin-bottom: 20px;"></div>
        <p class="text-success fw-bold small mb-3" id="shLatin" style="font-style: italic;"></p>
        <hr>
        <p class="text-muted mb-0" id="shIndo" style="line-height: 1.8; text-align: justify;"></p>
    </div>
    
    <button class="btn btn-success w-100 rounded-pill py-3 fw-bold" onclick="closeDoa()">Selesai</button>
</div>

<script>
    // Fitur Search
    document.getElementById('findDoa').addEventListener('input', function() {
        let keyword = this.value.toLowerCase();
        document.querySelectorAll('.item-doa').forEach(card => {
            card.style.display = card.getAttribute('data-title').includes(keyword) ? 'block' : 'none';
        });
    });

    // Fitur Filter Kategori
    function filterCat(cat, btn) {
        document.querySelectorAll('.cat-item').forEach(i => i.classList.remove('active'));
        btn.classList.add('active');
        
        // Catatan: Karena API ini daftar doanya bercampur, kita filter berdasarkan teks judul
        document.querySelectorAll('.item-doa').forEach(card => {
            let judul = card.getAttribute('data-title');
            if(cat === 'all' || judul.includes(cat)) {
                card.style.display = 'block';
            } else {
                card.style.display = 'none';
            }
        });
    }

    // Fungsi Pop Up
    function openDoa(data) {
        document.getElementById('shTitle').innerText = data.t;
        document.getElementById('shArab').innerText = data.a;
        document.getElementById('shLatin').innerText = data.l || 'Latin tidak tersedia';
        document.getElementById('shIndo').innerText = data.tr;
        
        document.getElementById('blurLayer').style.display = 'block';
        document.getElementById('sheetDoa').classList.add('active');
    }

    function closeDoa() {
        document.getElementById('blurLayer').style.display = 'none';
        document.getElementById('sheetDoa').classList.remove('active');
    }
</script>

<?php include 'includes/footer.php'; ?>