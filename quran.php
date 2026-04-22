<?php
session_start();
// Cek Login
if (!isset($_SESSION['user_id'])) { 
    header("Location: login.php"); 
    exit(); 
}

require_once 'config/database.php';

// 1. Ambil Data Surah dari API e-Quran v2
$ch = curl_init("https://equran.id/api/v2/surat");
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
$res = curl_exec($ch);
curl_close($ch);
$data = json_decode($res, true);
$daftar_surah = $data['data'] ?? [];

// 2. Ambil Data Terakhir Baca dari Database (Sesuai kolom kamu)
$uid = $_SESSION['user_id'];
$last_read_query = mysqli_query($koneksi, "SELECT * FROM last_read WHERE user_id = '$uid' LIMIT 1");
$last_read = mysqli_fetch_assoc($last_read_query);

$title = "Al-Qur'an Digital";
include 'includes/header.php';
?>

<style>
    :root { --p-green: #00a86b; --p-dark: #1e293b; }
    body { background-color: #f8fafc; }

    /* Animasi Klik & Hover */
    .btn-interact:active { transform: scale(0.96); }
    .btn-interact { transition: 0.2s; cursor: pointer; }

    /* Hero Section Terakhir Baca */
    .hero-quran {
        background: linear-gradient(135deg, #00a86b, #00d284);
        border-radius: 30px; color: white; padding: 30px; margin-bottom: 25px; 
        position: relative; overflow: hidden; border: none;
    }
    .hero-quran .bg-icon { position: absolute; right: -20px; bottom: -20px; font-size: 8rem; opacity: 0.15; transform: rotate(-15deg); }

    /* Search & Filter */
    .search-container {
        background: white; border-radius: 20px; padding: 12px 20px;
        box-shadow: 0 4px 15px rgba(0,0,0,0.03); margin-bottom: 20px;
        border: 1px solid #f1f5f9; display: flex; align-items: center;
    }
    .filter-wrapper { display: flex; gap: 10px; overflow-x: auto; scrollbar-width: none; padding-bottom: 15px; }
    .filter-wrapper::-webkit-scrollbar { display: none; }
    .filter-btn {
        padding: 10px 25px; border-radius: 15px; border: none; background: white; 
        color: #64748b; font-size: 0.9rem; font-weight: 700; white-space: nowrap;
        box-shadow: 0 2px 8px rgba(0,0,0,0.02);
    }
    .filter-btn.active { background: var(--p-green); color: white; }

    /* Card Surah */
    .card-surah {
        background: white; border-radius: 22px; padding: 18px;
        display: flex; align-items: center; text-decoration: none !important;
        color: inherit; margin-bottom: 12px; border: 1px solid #f1f5f9; transition: 0.3s;
    }
    .card-surah:hover { border-color: var(--p-green); transform: translateY(-3px); box-shadow: 0 10px 25px rgba(0,168,107,0.06); }

    /* Desain Nomor Surah Segi 8 */
    .star-container {
        position: relative; width: 50px; height: 50px;
        display: flex; align-items: center; justify-content: center; margin-right: 15px;
    }
    .star-shape {
        position: absolute; width: 32px; height: 32px; background: #f0fdf4; 
        border: 1.5px solid var(--p-green); transform: rotate(22.5deg); border-radius: 4px;
    }
    .star-shape::after {
        content: ""; position: absolute; inset: -1.5px; border: 1.5px solid var(--p-green);
        transform: rotate(45deg); border-radius: 4px; background: transparent;
    }
    .star-txt { position: relative; z-index: 5; font-weight: 800; color: var(--p-green); font-size: 0.85rem; }

    /* Badge Makkiyah / Madaniyah */
    .tag-mekah { color: #3b82f6; font-weight: 800; font-size: 0.7rem; letter-spacing: 0.5px; }
    .tag-madinah { color: #10b981; font-weight: 800; font-size: 0.7rem; letter-spacing: 0.5px; }
</style>

<div class="container py-4">
    
    <div class="hero-quran shadow-sm btn-interact">
        <div style="position:relative; z-index:2;">
            <div class="d-flex align-items-center gap-2 mb-2">
                <i class="bi bi-bookmark-heart-fill fs-5"></i>
                <span class="fw-bold small">TERAKHIR BACA</span>
            </div>
            
            <?php if($last_read): ?>
                <h2 class="fw-bold mb-0"><?= $last_read['nama_surah'] ?></h2>
                <p class="opacity-75 mb-3">Ayat Ke-<?= $last_read['nomor_ayat'] ?></p>
                <a href="surah_detail.php?no=<?= $last_read['nomor_surah'] ?>" class="btn btn-light rounded-pill px-4 fw-bold text-success shadow-sm btn-interact">
                    Lanjut Baca <i class="bi bi-arrow-right ms-1"></i>
                </a>
            <?php else: ?>
                <h2 class="fw-bold mb-1">Mulai Mengaji</h2>
                <p class="opacity-75 mb-0">Klik bookmark di ayat untuk menyimpan riwayat.</p>
            <?php endif; ?>
        </div>
        <i class="bi bi-moon-stars-fill bg-icon"></i>
    </div>

    <div class="search-container">
        <i class="bi bi-search text-muted me-3"></i>
        <input type="text" id="cariSurah" class="form-control border-0 shadow-none p-0" placeholder="Cari nama surah (misal: Al-Fatihah)...">
    </div>

    <div class="filter-wrapper">
        <button class="filter-btn active btn-interact" onclick="filterType('all', this)">Semua Surah</button>
        <button class="filter-btn btn-interact" onclick="filterType('mekah', this)">Makkiyah</button>
        <button class="filter-btn btn-interact" onclick="filterType('madinah', this)">Madaniyah</button>
    </div>

    <div id="listSurah" class="mt-2">
        <?php foreach($daftar_surah as $s): 
            $tipe = strtolower($s['tempatTurun']);
            $isMekah = ($tipe == 'mekah');
        ?>
            <a href="surah_detail.php?no=<?= $s['nomor'] ?>" 
               class="card-surah item-surah" 
               data-nama="<?= strtolower($s['namaLatin']) ?>" 
               data-tipe="<?= $tipe ?>">
                
                <div class="star-container">
                    <div class="star-shape"></div>
                    <div class="star-txt"><?= $s['nomor'] ?></div>
                </div>

                <div class="flex-grow-1">
                    <h6 class="fw-bold mb-1 text-dark"><?= $s['namaLatin'] ?></h6>
                    <div class="d-flex align-items-center gap-2">
                        <span class="<?= $isMekah ? 'tag-mekah' : 'tag-madinah' ?>">
                            <?= $isMekah ? 'MAKKIYAH' : 'MADANIYAH' ?>
                        </span>
                        <span class="text-muted small">• <?= $s['jumlahAyat'] ?> Ayat</span>
                    </div>
                </div>

                <div class="text-success h4 mb-0" style="font-family: 'Amiri', serif;">
                    <?= $s['nama'] ?>
                </div>
            </a>
        <?php endforeach; ?>
    </div>

</div>

<script>
    // FUNGSI SEARCH
    document.getElementById('cariSurah').addEventListener('input', function() {
        let val = this.value.toLowerCase();
        document.querySelectorAll('.item-surah').forEach(el => {
            let nama = el.getAttribute('data-nama');
            el.style.display = nama.includes(val) ? 'flex' : 'none';
        });
    });

    // FUNGSI FILTER (MAKKIYAH/MADANIYAH)
    function filterType(type, btn) {
        // Reset Tombol
        document.querySelectorAll('.filter-btn').forEach(b => b.classList.remove('active'));
        btn.classList.add('active');

        // Filter List
        document.querySelectorAll('.item-surah').forEach(el => {
            if(type === 'all' || el.getAttribute('data-tipe') === type) {
                el.style.display = 'flex';
            } else {
                el.style.display = 'none';
            }
        });
    }
</script>

<?php include 'includes/footer.php'; ?>