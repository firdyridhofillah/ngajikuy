<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= $title ?? 'NgajiKuy'; ?></title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.1/font/bootstrap-icons.css">
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;600;700&display=swap" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Amiri:wght@400;700&display=swap" rel="stylesheet">
    <style>
        :root { 
            --primary-teal: #00a86b; 
            --bg-gray: #f8fafc; 
        }
        body { background-color: var(--bg-gray); font-family: 'Plus Jakarta Sans', sans-serif; padding-bottom: 100px; color: #1e293b; }
        
        /* Motif Bulat Elegan di Kartu */
        .hero-card {
            background-color: var(--primary-teal);
            border-radius: 30px; color: white; padding: 25px;
            position: relative; overflow: hidden; border: none;
            box-shadow: 0 10px 25px rgba(0, 168, 107, 0.15);
        }
        .hero-card::after {
            content: ""; position: absolute; width: 150px; height: 150px;
            background: rgba(255,255,255,0.1); border-radius: 50%;
            top: -50px; right: -50px;
        }
        .hero-card::before {
            content: ""; position: absolute; width: 80px; height: 80px;
            background: rgba(255,255,255,0.05); border-radius: 50%;
            bottom: -20px; left: 20px;
        }

        .card-main { background: white; border-radius: 25px; padding: 20px; border: 1px solid #f1f5f9; margin-bottom: 20px; }
        
        .menu-box { background: white; border-radius: 20px; padding: 15px; text-align: center; border: 1px solid #f1f5f9; display: block; text-decoration: none; }
        .menu-icon { width: 45px; height: 45px; background: #f0fdf4; color: var(--primary-teal); border-radius: 12px; display: flex; align-items: center; justify-content: center; margin: 0 auto 8px; font-size: 1.2rem; }

        /* Navbar Desktop */
        .nav-desktop { background: white; border-bottom: 1px solid #f1f5f9; padding: 15px 0; display: none; }
        @media (min-width: 992px) { .nav-desktop { display: block; } }

        /* Hilangkan Animasi */
        * { transition: none !important; animation: none !important; }
    </style>
</head>
<body>

<?php 
// Logika Sembunyikan Navigasi di Halaman Login & Register
$current_page = basename($_SERVER['PHP_SELF']);
$hide_nav = ['login.php', 'register.php'];
if (!in_array($current_page, $hide_nav)): 
?>
<nav class="nav-desktop sticky-top">
    <div class="container d-flex justify-content-between align-items-center">
        <h4 class="fw-bold mb-0 text-success">NgajiKuy</h4>
        <div class="d-flex gap-4">
            <a href="index.php" class="text-dark text-decoration-none small fw-bold">Home</a>
            <a href="quran.php" class="text-dark text-decoration-none small fw-bold">Al-Quran</a>
            <a href="#" class="text-dark text-decoration-none small fw-bold">Doa</a>
            <a class="nav-link" href="artikel_list.php">Artikel</a>
            <a href="logout.php" class="btn btn-danger btn-sm rounded-pill px-3">
             <i class="bi bi-box-arrow-right me-1"></i> Keluar
            </a>
        </div>
    </div>
</nav>
<?php endif; ?>