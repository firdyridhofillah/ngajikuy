<?php


require_once 'config/database.php';
require_once 'includes/functions.php';

// Ambil status admin/penulis dari session
$is_admin = $_SESSION['is_admin'] ?? 0;
$is_writer = $_SESSION['is_writer'] ?? 0;

// Kita kunci lokasinya di sini biar gak error "Memuat" terus
$provinsi = "Banten"; 
$kabkota = "Kota Serang"; 

// Ambil Data Jadwal Sholat via cURL
$url = "https://equran.id/api/v2/shalat";
$payload = json_encode([
    "provinsi" => $provinsi, 
    "kabkota" => $kabkota, 
    "bulan" => date('n'), 
    "tahun" => date('Y')
]);

$ch = curl_init($url);
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
curl_setopt($ch, CURLOPT_POST, true);
curl_setopt($ch, CURLOPT_POSTFIELDS, $payload);
curl_setopt($ch, CURLOPT_HTTPHEADER, ['Content-Type: application/json']);
$res = curl_exec($ch);
curl_close($ch);

$data = json_decode($res, true);
$jadwal = [];
if (isset($data['data']['jadwal'])) {
    foreach ($data['data']['jadwal'] as $j) {
        if ($j['tanggal_lengkap'] == date('Y-m-d')) {
            $jadwal = $j;
            break;
        }
    }
}

$title = "Home - NgajiKuy";
include 'includes/header.php'; 
?>

<style>
    .hero-card { background: linear-gradient(135deg, #00a86b, #059669); border-radius: 20px; border: none; }
    .menu-box { transition: 0.3s; border: 1px solid #f1f5f9; background: #fff; border-radius: 15px; text-decoration: none; display: block; padding: 15px; }
    .menu-box:hover { transform: translateY(-3px); border-color: #00a86b; background: #f0fdf4; }
    .text-hijau { color: #00a86b !important; }
    .line-clamp { display: -webkit-box; -webkit-line-clamp: 2; -webkit-box-orient: vertical; overflow: hidden; }
</style>

<div class="container py-4">
    <div class="hero-card mb-4 text-white p-4 shadow-sm">
        <div class="d-flex justify-content-between">
            <div>
                <p class="mb-0 opacity-75 small">Assalamu'alaikum, <?= $_SESSION['username'] ?? 'Sobat' ?></p>
                <h6 class="fw-bold"><i class="bi bi-geo-alt-fill"></i> <?= $kabkota ?></h6>
            </div>
            <div class="text-end">
                <h5 class="fw-bold mb-0" id="timer">00:00:00</h5>
                <small style="font-size: 0.6rem;">Menuju <span id="next-name">...</span></small>
            </div>
        </div>
        
        <div class="mt-3">
            <span class="badge bg-white text-success rounded-pill mb-1" style="font-size: 0.6rem;">JADWAL BERIKUTNYA</span>
            <h2 class="fw-bold mb-0" id="next-time">--:--</h2>
        </div>
    </div>

    <div class="row g-2 text-center mb-4">
        <?php 
        $list = ['subuh'=>'Subuh','dzuhur'=>'Dzuhur','ashar'=>'Ashar','maghrib'=>'Maghrib','isya'=>'Isya'];
        foreach($list as $k => $v): 
        ?>
        <div class="col">
            <div id="box-<?= $k ?>" class="p-2 rounded-3 bg-white border shadow-sm">
                <small class="d-block text-muted" style="font-size: 0.6rem;"><?= $v ?></small>
                <b class="sholat-val" data-key="<?= $k ?>" data-name="<?= $v ?>" style="font-size: 0.8rem;">
                    <?= $jadwal[$k] ?? '--:--' ?>
                </b>
            </div>
        </div>
        <?php endforeach; ?>
    </div>

    <div class="row g-3 mb-4">
        <div class="col-4">
            <a href="quran.php" class="menu-box text-center">
                <i class="bi bi-book fs-3 text-hijau d-block mb-1"></i>
                <small class="fw-bold text-dark">Quran</small>
            </a>
        </div>
        <div class="col-4">
            <a href="doa.php" class="menu-box text-center">
                <i class="bi bi-chat-dots fs-3 text-hijau d-block mb-1"></i>
                <small class="fw-bold text-dark">Doa</small>
            </a>
        </div>
        <div class="col-4">
            <a href="quiz.php" class="menu-box text-center">
                <i class="bi bi-controller fs-3 text-hijau d-block mb-1"></i>
                <small class="fw-bold text-dark">Game</small>
            </a>
        </div>
        <div class="col-4">
            <?php if($is_admin == 1): ?>
                <a href="admin_dashboard.php" class="menu-box text-center">
                    <i class="bi bi-clipboard2-data fs-3 text-hijau d-block mb-1"></i>
                    <small class="fw-bold text-dark">Admin</small>
                </a>
            <?php else: ?>
                <a href="settings.php" class="menu-box text-center">
                    <i class="bi bi-gear fs-3 text-hijau d-block mb-1"></i>
                    <small class="fw-bold text-dark">Setting</small>
                </a>
            <?php endif; ?>
        </div>
        <div class="col-4">
            <a href="kiblat.php" class="menu-box text-center">
                <i class="bi bi-compass fs-3 text-hijau d-block mb-1"></i>
                <small class="fw-bold text-dark">Kiblat</small>
            </a>
        </div>
        <div class="col-4">
            <a href="javascript:void(0)" onclick="cekTulis()" class="menu-box text-center">
                <i class="bi bi-pencil-square fs-3 text-hijau d-block mb-1"></i>
                <small class="fw-bold text-dark">Tulis</small>
            </a>
        </div>
    </div>

    <div class="d-flex justify-content-between align-items-center mb-3">
        <h6 class="fw-bold mb-0">Artikel Terbaru</h6>
        <a href="artikel_list.php" class="text-success text-decoration-none small fw-bold">Semua</a>
    </div>

    <div class="row g-3">
        <?php
        $articles = $koneksi->query("SELECT a.*, u.username, 
                    (SELECT COUNT(*) FROM artikel_likes WHERE artikel_id = a.id AND type='like') as lks,
                    (SELECT COUNT(*) FROM artikel_komentar WHERE artikel_id = a.id) as cmt
                    FROM artikel a JOIN users u ON a.penulis_id = u.id 
                    WHERE a.status = 'published' ORDER BY a.created_at DESC LIMIT 5");
        while($art = $articles->fetch_assoc()):
            $img = !empty($art['thumbnail']) ? 'uploads/'.$art['thumbnail'] : 'https://via.placeholder.com/150';
        ?>
        <div class="col-12">
            <a href="artikel_detail.php?id=<?= $art['id'] ?>" class="text-decoration-none text-dark">
                <div class="card border-0 shadow-sm rounded-4 overflow-hidden">
                    <div class="row g-0 align-items-center">
                        <div class="col-4">
                            <img src="<?= $img ?>" class="img-fluid" style="height: 85px; width: 100%; object-fit: cover;">
                        </div>
                        <div class="col-8 p-2 px-3">
                            <h6 class="fw-bold mb-1 line-clamp" style="font-size: 0.85rem;"><?= htmlspecialchars($art['judul']) ?></h6>
                            <div class="d-flex gap-3 text-muted" style="font-size: 0.65rem;">
                                <span><i class="bi bi-eye"></i> <?= $art['views'] ?></span>
                                <span><i class="bi bi-heart"></i> <?= $art['lks'] ?></span>
                                <span><i class="bi bi-chat"></i> <?= $art['cmt'] ?></span>
                            </div>
                        </div>
                    </div>
                </div>
            </a>
        </div>
        <?php endwhile; ?>
    </div>
</div>

<script>
function cekTulis() {
    <?php if(isset($_SESSION['is_writer']) && $_SESSION['is_writer'] == 1): ?>
        // Tampilkan pilihan mau Tulis atau Kelola
        const tanya = confirm("Pilih OK untuk 'Tulis Artikel Baru' atau Cancel untuk 'Kelola Artikel Saya'");
        if(tanya) window.location.href = 'artikel_tulis.php';
        else window.location.href = 'artikel_saya.php';
    <?php else: ?>
        alert('Afwan Fir! Kamu tidak memiliki akses menulis. Hubungi admin ya!');
    <?php endif; ?>
}

function startTimer() {
    const now = new Date();
    const sholat = {};
    document.querySelectorAll('.sholat-val').forEach(el => {
        if(el.innerText !== '--:--') sholat[el.dataset.key] = el.innerText;
    });

    let nextK = null; let nextN = null; let diffMin = Infinity;

    for (const [k, v] of Object.entries(sholat)) {
        const [h, m] = v.split(':');
        const t = new Date(); t.setHours(h, m, 0);
        let d = t - now;
        if (d < 0) { t.setDate(t.getDate() + 1); d = t - now; }
        if (d < diffMin) { diffMin = d; nextK = k; nextN = document.querySelector(`[data-key="${k}"]`).dataset.name; }
    }

    if (nextK) {
        document.getElementById('next-name').innerText = nextN;
        document.getElementById('next-time').innerText = sholat[nextK];
        
        // Timer
        const hh = Math.floor(diffMin/3600000).toString().padStart(2,'0');
        const mm = Math.floor((diffMin%3600000)/60000).toString().padStart(2,'0');
        const ss = Math.floor((diffMin%60000)/1000).toString().padStart(2,'0');
        document.getElementById('timer').innerText = `${hh}:${mm}:${ss}`;

        // Highlight box
        document.querySelectorAll('[id^="box-"]').forEach(b => { b.style.borderColor = '#f1f5f9'; b.style.background = '#fff'; });
        document.getElementById('box-' + nextK).style.borderColor = '#00a86b';
        document.getElementById('box-' + nextK).style.background = '#f0fdf4';
    }
}
setInterval(startTimer, 1000); startTimer();
</script>

<?php include 'includes/footer.php'; ?>