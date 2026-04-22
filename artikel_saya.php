<?php
session_start();
require_once 'config/database.php';

if (!isset($_SESSION['is_writer']) || $_SESSION['is_writer'] != 1) { die("Akses Ditolak"); }

$user_id = $_SESSION['user_id'];

// Proses Hapus Artikel
if (isset($_GET['hapus'])) {
    $id_hapus = $_GET['hapus'];
    $koneksi->query("DELETE FROM artikel WHERE id = $id_hapus AND penulis_id = $user_id");
    header("Location: artikel_saya.php"); exit();
}

$title = "Artikel Saya - NgajiKuy";
include 'includes/header.php';
?>

<div class="container py-4">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h4 class="fw-bold text-success">Kelola Tulisanmu</h4>
        <a href="artikel_tulis.php" class="btn btn-success btn-sm rounded-pill px-3">+ Tulis Baru</a>
    </div>

    <div class="table-responsive bg-white p-3 rounded-4 shadow-sm">
        <table class="table table-hover align-middle" style="font-size: 0.85rem;">
            <thead class="table-light">
                <tr>
                    <th>Judul</th>
                    <th>Dilihat</th>
                    <th>Interaksi</th>
                    <th>Aksi</th>
                </tr>
            </thead>
            <tbody>
                <?php
                $my_art = $koneksi->query("SELECT a.*, 
                          (SELECT COUNT(*) FROM artikel_likes WHERE artikel_id = a.id) as lks,
                          (SELECT COUNT(*) FROM artikel_komentar WHERE artikel_id = a.id) as cmt
                          FROM artikel a WHERE a.penulis_id = $user_id ORDER BY a.created_at DESC");
                while($row = $my_art->fetch_assoc()):
                ?>
                <tr>
                    <td>
                        <span class="fw-bold d-block"><?= htmlspecialchars($row['judul']) ?></span>
                        <small class="text-muted"><?= date('d/m/y', strtotime($row['created_at'])) ?></small>
                    </td>
                    <td><i class="bi bi-eye"></i> <?= $row['views'] ?></td>
                    <td>
                        <small class="d-block text-muted"><i class="bi bi-heart"></i> <?= $row['lks'] ?></small>
                        <small class="text-muted"><i class="bi bi-chat"></i> <?= $row['cmt'] ?></small>
                    </td>
                    <td>
                        <div class="btn-group">
                            <a href="artikel_edit.php?id=<?= $row['id'] ?>" class="btn btn-sm btn-outline-primary"><i class="bi bi-pencil text-primary"></i></a>
                            <a href="?hapus=<?= $row['id'] ?>" class="btn btn-sm btn-outline-danger" onclick="return confirm('Yakin mau hapus artikel ini?')"><i class="bi bi-trash text-danger"></i></a>
                        </div>
                    </td>
                </tr>
                <?php endwhile; ?>
            </tbody>
        </table>
    </div>
</div>

<?php include 'includes/footer.php'; ?>