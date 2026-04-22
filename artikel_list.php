<?php
session_start();
require_once 'config/database.php';

// Judul Halaman
$title = "Artikel Islami - NgajiKuy";
include 'includes/header.php';

// Ambil data artikel beserta nama penulisnya
$sql = "SELECT a.*, u.username 
        FROM artikel a 
        JOIN users u ON a.penulis_id = u.id 
        WHERE a.status = 'published' 
        ORDER BY a.created_at DESC";
$result = $koneksi->query($sql);
?>

<style>
    .article-card {
        transition: transform 0.3s ease, box-shadow 0.3s ease;
        border: none;
        border-radius: 20px;
        overflow: hidden;
    }
    .article-card:hover {
        transform: translateY(-10px);
        box-shadow: 0 15px 30px rgba(0,0,0,0.1);
    }
    .badge-category {
        background-color: rgba(0, 168, 107, 0.1);
        color: #00a86b;
        font-weight: 600;
        font-size: 0.75rem;
        padding: 5px 12px;
        border-radius: 50px;
    }
    .article-title {
        color: #1e293b;
        font-weight: 700;
        text-decoration: none;
        line-height: 1.4;
    }
    .article-title:hover {
        color: #00a86b;
    }
    .btn-write {
        background: linear-gradient(135deg, #00a86b, #059669);
        color: white;
        border: none;
        padding: 12px 25px;
        border-radius: 50px;
        font-weight: 600;
    }
</style>

<div class="container py-5">
    <div class="d-flex justify-content-between align-items-center mb-5">
        <div>
            <h2 class="fw-bold mb-1">Artikel Terbaru</h2>
            <p class="text-muted">Inspirasi dan ilmu harian untukmu</p>
        </div>
        <?php if (isset($_SESSION['is_writer']) && $_SESSION['is_writer'] == 1): ?>
            <a href="artikel_tulis.php" class="btn btn-write shadow-sm">
                <i class="bi bi-pencil-square me-2"></i> Tulis Artikel
            </a>
        <?php endif; ?>
    </div>

    <div class="row g-4">
        <?php if ($result->num_rows > 0): ?>
            <?php while($row = $result->fetch_assoc()): ?>
                <div class="col-md-4">
                    <div class="card h-100 article-card shadow-sm">
                        <img src="https://images.unsplash.com/photo-1584551246679-0daf3d275d0f?auto=format&fit=crop&q=80&w=800" 
                             class="card-img-top" alt="Cover Artikel" style="height: 200px; object-fit: cover;">
                        
                        <div class="card-body p-4">
                            <div class="mb-2">
                                <span class="badge-category">Ilmu & Dakwah</span>
                            </div>
                            <h5 class="mb-3">
                                <a href="artikel_detail.php?id=<?= $row['id'] ?>" class="article-title">
                                    <?= htmlspecialchars($row['judul']) ?>
                                </a>
                            </h5>
                            <p class="text-muted small">
                                <?= substr(strip_tags($row['isi']), 0, 100) ?>...
                            </p>
                        </div>
                        
                        <div class="card-footer bg-white border-0 p-4 pt-0">
                            <hr class="mt-0 mb-3 opacity-10">
                            <div class="d-flex align-items-center">
                                <div class="bg-success rounded-circle d-flex align-items-center justify-content-center text-white" 
                                     style="width: 35px; height: 35px; font-size: 0.8rem;">
                                    <?= strtoupper(substr($row['username'], 0, 1)) ?>
                                </div>
                                <div class="ms-2">
                                    <p class="mb-0 small fw-bold text-dark"><?= htmlspecialchars($row['username']) ?></p>
                                    <p class="mb-0 text-muted" style="font-size: 0.7rem;">
                                        <?= date('d M Y', strtotime($row['created_at'])) ?>
                                    </p>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            <?php endwhile; ?>
        <?php else: ?>
            <div class="col-12 text-center py-5">
                <i class="bi bi-journal-x display-1 text-muted"></i>
                <h4 class="mt-3 text-muted">Belum ada artikel yang diterbitkan.</h4>
            </div>
        <?php endif; ?>
    </div>
</div>

<?php include 'includes/footer.php'; ?>