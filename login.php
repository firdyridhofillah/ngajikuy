<?php
require_once 'config/database.php';
require_once 'includes/functions.php';

// Judul Halaman
$title = "Login - NgajiKuy";

// Header kita include di sini
include 'includes/header.php'; 
?>

<style>
    .login-container {
        min-height: 70vh;
        display: flex;
        align-items: center;
        justify-content: center;
    }
    .login-card {
        width: 100%;
        max-width: 400px;
        background: white;
        border-radius: 30px;
        padding: 35px;
        border: 1px solid #f1f5f9;
        box-shadow: 0 10px 25px rgba(0,0,0,0.02);
    }
    .form-control-custom {
        background-color: #f8fafc;
        border: 1px solid #e2e8f0;
        border-radius: 15px;
        padding: 12px 20px;
        font-size: 0.95rem;
    }
    .form-control-custom:focus {
        background-color: white;
        border-color: var(--primary-teal);
        box-shadow: none;
        outline: 0;
    }
    .btn-login {
        background-color: var(--primary-teal);
        color: white;
        border-radius: 15px;
        padding: 12px;
        font-weight: 700;
        border: none;
        width: 100%;
        margin-top: 10px;
    }
    .login-header {
        text-align: center;
        margin-bottom: 30px;
    }
    .login-header i {
        font-size: 3rem;
        color: var(--primary-teal);
    }
</style>

<div class="container">
    <div class="login-container">
        <div class="login-card">
            <div class="login-header">
                <i class="bi bi-person-circle"></i>
                <h4 class="fw-bold mt-2">Selamat Datang</h4>
                <p class="text-muted small">Silahkan masuk ke akun NgajiKuy Anda</p>
            </div>

            <form action="proses_login.php" method="POST">
                <div class="mb-3">
                    <label class="form-label small fw-bold text-muted">Username atau Email</label>
                    <input type="text" name="username" class="form-control form-control-custom" placeholder="Masukkan username" required>
                </div>
                
                <div class="mb-4">
                    <label class="form-label small fw-bold text-muted">Password</label>
                    <div class="input-group">
                        <input type="password" name="password" id="passInput" class="form-control form-control-custom" placeholder="Masukkan password" required>
                    </div>
                </div>

                <button type="submit" class="btn-login">Masuk Sekarang</button>
            </form>

            <div class="text-center mt-4">
                <p class="small text-muted">Belum punya akun? <a href="register.php" class="text-success fw-bold text-decoration-none">Daftar</a></p>
            </div>
        </div>
    </div>
</div>

<?php 
// Footer kita include di sini
include 'includes/footer.php'; 
?>