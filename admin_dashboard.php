<?php
session_start();
require_once 'config/database.php';

// Proteksi: Hanya admin yang boleh masuk
if (!isset($_SESSION['user_id']) || $_SESSION['is_admin'] != 1) {
    header("Location: index.php");
    exit();
}

// Logika Update Izin Menulis
if (isset($_GET['toggle_writer'])) {
    $uid = $_GET['toggle_writer'];
    $val = $_GET['val'];
    $koneksi->query("UPDATE users SET is_writer = $val WHERE id = $uid");
    header("Location: admin_dashboard.php");
}

include 'includes/header.php';
?>

<div class="container py-5">
    <h2 class="fw-bold mb-4">Admin Dashboard</h2>
    
    <div class="card border-0 shadow-sm rounded-4 p-4">
        <h5 class="fw-bold mb-3">Manajemen Penulis & Admin</h5>
        <div class="table-responsive">
            <table class="table table-hover align-middle">
                <thead>
                    <tr>
                        <th>Username</th>
                        <th>Status Penulis</th>
                        <th>Status Admin</th>
                        <th>Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    <?php
                    $users = $koneksi->query("SELECT * FROM users");
                    while($u = $users->fetch_assoc()):
                    ?>
                    <tr>
                        <td><?= $u['username'] ?></td>
                        <td>
                            <span class="badge <?= $u['is_writer'] ? 'bg-success' : 'bg-secondary' ?>">
                                <?= $u['is_writer'] ? 'Boleh Menulis' : 'Pembaca' ?>
                            </span>
                        </td>
                        <td><?= $u['is_admin'] ? '⭐ Admin' : 'User Biasa' ?></td>
                        <td>
                            <?php if($u['is_writer']): ?>
                                <a href="?toggle_writer=<?= $u['id'] ?>&val=0" class="btn btn-sm btn-danger">Cabut Izin</a>
                            <?php else: ?>
                                <a href="?toggle_writer=<?= $u['id'] ?>&val=1" class="btn btn-sm btn-success">Beri Izin Nulis</a>
                            <?php endif; ?>
                        </td>
                    </tr>
                    <?php endwhile; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>

<?php include 'includes/footer.php'; ?>