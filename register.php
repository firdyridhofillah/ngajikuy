<?php
$title = "Daftar Akun - NgajiKuy";
include 'includes/header.php'; 
?>

<div class="container py-5">
    <div class="row justify-content-center">
        <div class="col-md-5">
            <div class="card-main p-4 shadow-sm">
                <div class="text-center mb-4">
                    <h4 class="fw-bold">Daftar Akun</h4>
                    <p class="text-muted small">Buat akun untuk mulai menulis artikel</p>
                </div>
                
                <form action="proses_register.php" method="POST">
                    <div class="mb-3">
                        <label class="small fw-bold">Nama Lengkap</label>
                        <input type="text" name="nama" class="form-control rounded-4 p-3" placeholder="Nama asli kamu" required>
                    </div>
                    <div class="mb-3">
                        <label class="small fw-bold">Username</label>
                        <input type="text" name="username" class="form-control rounded-4 p-3" placeholder="Username untuk login" required>
                    </div>
                    <div class="mb-4">
                        <label class="small fw-bold">Password</label>
                        <input type="password" name="password" class="form-control rounded-4 p-3" placeholder="Minimal 6 karakter" required>
                    </div>
                    <button type="submit" class="btn btn-success w-100 rounded-pill p-3 fw-bold">Daftar Sekarang</button>
                </form>
                
                <div class="text-center mt-4">
                    <p class="small">Sudah punya akun? <a href="login.php" class="text-success fw-bold text-decoration-none">Login di sini</a></p>
                </div>
            </div>
        </div>
    </div>
</div>

<?php include 'includes/footer.php'; ?>