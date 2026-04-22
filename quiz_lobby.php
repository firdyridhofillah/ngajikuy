<?php
session_start();
if (!isset($_SESSION['user_id'])) { header("Location: login.php"); exit(); }
require_once 'config/database.php';

$mode = $_GET['mode'] ?? 'sambung';
$title = ($mode == 'sambung') ? "Sambung Ayat" : "Tebak Ayat";

include 'includes/header.php';
?>

<div class="container py-4">
    <div class="card border-0 shadow-sm rounded-4 p-4 mb-4 bg-light">
        <div class="d-flex align-items-center mb-3">
            <a href="quiz.php" class="btn btn-sm btn-outline-success rounded-circle me-3"><i class="bi bi-arrow-left"></i></a>
            <div>
                <h4 class="fw-bold text-success mb-0">Lobby <?= $title ?></h4>
                <small class="text-muted">Waktu: 30 detik per soal</small>
            </div>
        </div>

        <form action="quiz_play.php" method="GET" class="row g-3">
            <input type="hidden" name="mode" value="<?= htmlspecialchars($mode) ?>">
            <div class="col-md-4 col-6">
                <label class="small fw-bold">Dari Juz</label>
                <input type="number" name="juz_start" class="form-control rounded-pill border-0 shadow-sm" value="1" min="1" max="30">
            </div>
            <div class="col-md-4 col-6">
                <label class="small fw-bold">Sampai Juz</label>
                <input type="number" name="juz_end" class="form-control rounded-pill border-0 shadow-sm" value="30" min="1" max="30">
            </div>
            <div class="col-md-4 col-12">
                <label class="small fw-bold">Jumlah Soal</label>
                <input type="number" name="limit" class="form-control rounded-pill border-0 shadow-sm" value="10" min="1">
            </div>
            <div class="col-12 mt-4">
                <button type="submit" class="btn btn-success w-100 rounded-pill py-3 fw-bold shadow">MULAI QUIZ</button>
            </div>
        </form>
    </div>

    <div class="card border-0 shadow-sm rounded-4 p-4">
        <h5 class="fw-bold mb-4">🏆 Leaderboard Global</h5>
        <div class="table-responsive">
            <table class="table table-hover align-middle">
                <thead class="bg-light">
                    <tr class="small text-muted">
                        <th>RANK</th>
                        <th>USER</th>
                        <th>TOTAL SOAL</th>
                        <th>BENAR</th>
                        <th>KETEPATAN</th>
                        <th class="text-end">HIGHSCORE</th>
                    </tr>
                </thead>
                <tbody>
                    <?php
                    $sql = "SELECT u.username, l.score, l.total_soal, l.total_benar, l.ketepatan 
                            FROM leaderboard l 
                            JOIN users u ON l.user_id = u.id 
                            WHERE l.mode = ? 
                            ORDER BY l.score DESC, l.ketepatan DESC LIMIT 10";
                    $stmt = $koneksi->prepare($sql);
                    $stmt->bind_param("s", $mode);
                    $stmt->execute();
                    $res = $stmt->get_result();

                    $rank = 1;
                    while($row = $res->fetch_assoc()): ?>
                        <tr>
                            <td><span class="badge bg-success rounded-pill">#<?= $rank++ ?></span></td>
                            <td class="fw-bold"><?= htmlspecialchars($row['username']) ?></td>
                            <td><?= $row['total_soal'] ?></td>
                            <td class="text-success fw-bold"><?= $row['total_benar'] ?></td>
                            <td><?= round($row['ketepatan']) ?>%</td>
                            <td class="text-end fw-bold text-success"><?= $row['score'] ?></td>
                        </tr>
                    <?php endwhile; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>
<?php include 'includes/footer.php'; ?>