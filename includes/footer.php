<?php 
// Logika Sembunyikan Navigasi Mobile di Halaman Login & Register
$current_page = basename($_SERVER['PHP_SELF']);
$hide_nav = ['login.php', 'register.php'];
if (!in_array($current_page, $hide_nav)): 
?>
    <style>
        .mobile-nav {
            position: fixed; bottom: 0; left: 0; right: 0;
            background: white; height: 75px; display: flex;
            align-items: center; justify-content: space-around;
            border-top: 1px solid #f1f5f9; z-index: 1000;
        }
        .nav-item-custom { color: #94a3b8; text-decoration: none; font-size: 1.4rem; position: relative; }
        .nav-item-custom.active { color: var(--primary-teal); }
        .nav-item-custom.center-node {
            width: 55px; height: 55px; background: var(--primary-teal);
            color: white !important; border-radius: 50%;
            display: flex; align-items: center; justify-content: center;
            margin-top: -45px; border: 5px solid var(--bg-gray);
            box-shadow: 0 8px 15px rgba(0, 168, 107, 0.2);
        }
    </style>

    <nav class="mobile-nav d-lg-none">
        <a href="index.php" class="nav-item-custom <?= $current_page == 'index.php' ? 'active' : '' ?>">
            <i class="bi bi-house-door-fill"></i>
        </a>
        <a href="quiz.php" class="nav-item-custom"><i class="bi bi-controller"></i></a>
        <a href="quran.php" class="nav-item-custom center-node"><i class="bi bi-book"></i></a>
        <a href="doa.php" class="nav-item-custom"><i class="bi bi-chat-dots"></i></a>
        <a href="settings.php" class="nav-item-custom"><i class="bi bi-person"></i></a>
    </nav>
<?php endif; ?>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>