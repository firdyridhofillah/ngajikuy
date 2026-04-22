<?php
session_start();
require_once 'config/database.php';

if (!isset($_SESSION['user_id'])) { 
    header("Location: login.php"); 
    exit(); 
}

$user_id = $_SESSION['user_id'];
$success = "";
$error = "";

$query = $koneksi->prepare("SELECT username, password, provinsi, kabkota FROM users WHERE id = ?");
$query->bind_param("i", $user_id);
$query->execute();
$userData = $query->get_result()->fetch_assoc();

if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['update_profile'])) {
    $new_username = mysqli_real_escape_string($koneksi, $_POST['username']);
    $new_provinsi = mysqli_real_escape_string($koneksi, $_POST['provinsi']);
    $new_kabkota  = mysqli_real_escape_string($koneksi, $_POST['kabkota']);
    $old_password = $_POST['old_password'];
    $new_password = $_POST['new_password'];

    if (password_verify($old_password, $userData['password'])) {
        $update = $koneksi->prepare("UPDATE users SET username = ?, provinsi = ?, kabkota = ? WHERE id = ?");
        $update->bind_param("sssi", $new_username, $new_provinsi, $new_kabkota, $user_id);
        
        if ($update->execute()) {
            $_SESSION['username'] = $new_username;
            if (!empty($new_password)) {
                $hashed = password_hash($new_password, PASSWORD_DEFAULT);
                $koneksi->query("UPDATE users SET password = '$hashed' WHERE id = $user_id");
            }
            $success = "Profil berhasil diperbarui!";
            $userData['username'] = $new_username;
            $userData['provinsi'] = $new_provinsi;
            $userData['kabkota']  = $new_kabkota;
        }
    } else {
        $error = "Password lama salah!";
    }
}

$title = "Settings - NgajiKuy";
include 'includes/header.php';
?>

<div class="container py-5">
    <div class="row justify-content-center">
        <div class="col-lg-7">
            <div class="card border-0 shadow-sm rounded-4 p-4">
                <h4 class="fw-bold text-success mb-4"><i class="bi bi-gear-wide-connected me-2"></i>Pengaturan Akun</h4>

                <?php if($success): ?> <div class="alert alert-success border-0"><?= $success ?></div> <?php endif; ?>
                <?php if($error): ?> <div class="alert alert-danger border-0"><?= $error ?></div> <?php endif; ?>

                <form action="" method="POST">
                    <input type="hidden" name="update_profile" value="1">
                    
                    <div class="mb-3">
                        <label class="form-label small fw-bold">Username</label>
                        <input type="text" name="username" class="form-control rounded-3" value="<?= htmlspecialchars($userData['username']) ?>" required>
                    </div>

                    <div class="row mb-3">
                        <div class="col-md-6">
                            <label class="form-label small fw-bold">Provinsi</label>
                            <select name="provinsi" id="prov-select" class="form-select rounded-3" required>
                                <option value="<?= $userData['provinsi'] ?>"><?= $userData['provinsi'] ?></option>
                            </select>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label small fw-bold">Kabupaten/Kota</label>
                            <select name="kabkota" id="kota-select" class="form-select rounded-3" required>
                                <option value="<?= $userData['kabkota'] ?>"><?= $userData['kabkota'] ?></option>
                            </select>
                        </div>
                    </div>

                    <hr class="my-4 opacity-10">

                    <div class="mb-3">
                        <label class="form-label small fw-bold">Password Sekarang</label>
                        <div class="input-group">
                            <input type="password" name="old_password" class="form-control border-end-0 rounded-start-3 pass-input" placeholder="Wajib untuk konfirmasi" required>
                            <button type="button" class="btn border border-start-0 rounded-end-3 bg-white btn-eye"><i class="bi bi-eye-slash"></i></button>
                        </div>
                    </div>

                    <div class="mb-4">
                        <label class="form-label small fw-bold">Password Baru (Opsional)</label>
                        <div class="input-group">
                            <input type="password" name="new_password" class="form-control border-end-0 rounded-start-3 pass-input" placeholder="Isi jika ingin ganti">
                            <button type="button" class="btn border border-start-0 rounded-end-3 bg-white btn-eye"><i class="bi bi-eye-slash"></i></button>
                        </div>
                    </div>

                    <button type="submit" class="btn btn-success w-100 py-3 rounded-pill fw-bold shadow">SIMPAN PERUBAHAN</button>
                </form>
            </div>
        </div>
    </div>
</div>

<script>
const provSelect = document.getElementById('prov-select');
const kotaSelect = document.getElementById('kota-select');

// 1. Ambil Daftar Provinsi (GET)
fetch('https://api.myquran.com/v2/shalat/provinsi')
    .then(res => res.json())
    .then(res => {
        if(res.code === 200) {
            res.data.forEach(p => {
                if(p !== "<?= $userData['provinsi'] ?>") {
                    let opt = document.createElement('option');
                    opt.value = p;
                    opt.innerText = p;
                    provSelect.appendChild(opt);
                }
            });
        }
    });

// 2. Ambil Daftar Kota saat Provinsi berubah (POST)
provSelect.addEventListener('change', function() {
    const provName = this.value;
    kotaSelect.innerHTML = '<option value="">Memuat Kota...</option>';

    fetch('https://api.myquran.com/v2/shalat/kabkota', {
        method: 'POST',
        headers: { 'Content-Type': 'application/json' },
        body: JSON.stringify({ provinsi: provName })
    })
    .then(res => res.json())
    .then(res => {
        if(res.code === 200) {
            kotaSelect.innerHTML = '<option value="">-- Pilih Kota --</option>';
            res.data.forEach(k => {
                let opt = document.createElement('option');
                opt.value = k;
                opt.innerText = k;
                kotaSelect.appendChild(opt);
            });
        }
    });
});

// 3. Toggle Password (Mata)
document.querySelectorAll('.btn-eye').forEach(btn => {
    btn.addEventListener('click', function() {
        const input = this.parentElement.querySelector('.pass-input');
        const icon = this.querySelector('i');
        input.type = input.type === 'password' ? 'text' : 'password';
        icon.classList.toggle('bi-eye');
        icon.classList.toggle('bi-eye-slash');
    });
});
</script>

<?php include 'includes/footer.php'; ?>