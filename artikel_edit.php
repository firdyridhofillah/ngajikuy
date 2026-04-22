<?php
session_start();
require_once 'config/database.php';

// Cek akses: hanya penulis yang bisa masuk
if (!isset($_SESSION['is_writer']) || $_SESSION['is_writer'] != 1) { 
    die("Afwan Fir, akses ditolak!"); 
}

$user_id = $_SESSION['user_id'];
$artikel_id = $_GET['id'] ?? null;

if (!$artikel_id) { header("Location: artikel_saya.php"); exit(); }

// Ambil data artikel lama
$query = $koneksi->prepare("SELECT * FROM artikel WHERE id = ? AND penulis_id = ?");
$query->bind_param("ii", $artikel_id, $user_id);
$query->execute();
$art = $query->get_result()->fetch_assoc();

if (!$art) { die("Artikel tidak ditemukan atau kamu bukan pemiliknya!"); }

// Proses Update
if (isset($_POST['update'])) {
    $judul = $_POST['judul'];
    $isi = $_POST['isi'];
    $status = $_POST['status']; // public atau draft
    
    // Logika jika ganti thumbnail
    if (!empty($_FILES['thumbnail']['name'])) {
        $nama_file = $_FILES['thumbnail']['name'];
        $tmp_file = $_FILES['thumbnail']['tmp_name'];
        $ext = pathinfo($nama_file, PATHINFO_EXTENSION);
        $new_name = time() . '.' . $ext;
        move_uploaded_file($tmp_file, "uploads/" . $new_name);
        
        $sql = "UPDATE artikel SET judul = ?, isi = ?, thumbnail = ?, status = ? WHERE id = ? AND penulis_id = ?";
        $stmt = $koneksi->prepare($sql);
        $stmt->bind_param("ssssii", $judul, $isi, $new_name, $status, $artikel_id, $user_id);
    } else {
        $sql = "UPDATE artikel SET judul = ?, isi = ?, status = ? WHERE id = ? AND penulis_id = ?";
        $stmt = $koneksi->prepare($sql);
        $stmt->bind_param("sssii", $judul, $isi, $status, $artikel_id, $user_id);
    }
    
    if ($stmt->execute()) {
        header("Location: artikel_saya.php?pesan=update_berhasil");
        exit();
    }
}

$title = "Edit Artikel - NgajiKuy";
include 'includes/header.php';
?>

<link href="https://cdn.jsdelivr.net/npm/summernote@0.8.18/dist/summernote-lite.min.css" rel="stylesheet">

<div class="container py-5">
    <div class="row justify-content-center">
        <div class="col-lg-10">
            <div class="card border-0 shadow-sm rounded-4 overflow-hidden">
                <div class="bg-success p-3 text-white">
                    <h5 class="mb-0 fw-bold"><i class="bi bi-pencil-square me-2"></i> Edit Tulisan Kamu</h5>
                </div>
                <div class="card-body p-4">
                    <form action="" method="POST" enctype="multipart/form-data">
                        <div class="mb-3">
                            <label class="fw-bold mb-1">Judul Artikel</label>
                            <input type="text" name="judul" class="form-control rounded-3" value="<?= htmlspecialchars($art['judul']) ?>" required>
                        </div>

                        <div class="row mb-3">
                            <div class="col-md-6">
                                <label class="fw-bold mb-1">Ganti Thumbnail (Kosongkan jika tidak diganti)</label>
                                <input type="file" name="thumbnail" class="form-control rounded-3">
                                <small class="text-muted">File saat ini: <?= $art['thumbnail'] ?></small>
                            </div>
                            <div class="col-md-6">
                                <label class="fw-bold mb-1">Status Publikasi</label>
                                <select name="status" class="form-select rounded-3">
                                    <option value="published" <?= $art['status'] == 'published' ? 'selected' : '' ?>>Publikasikan</option>
                                    <option value="draft" <?= $art['status'] == 'draft' ? 'selected' : '' ?>>Simpan sebagai Draft</option>
                                </select>
                            </div>
                        </div>

                        <div class="mb-4">
                            <label class="fw-bold mb-1">Isi Artikel</label>
                            <textarea name="isi" id="editor-edit"><?= $art['isi'] ?></textarea>
                        </div>

                        <div class="d-flex gap-2">
                            <button type="submit" name="update" class="btn btn-success rounded-pill px-5 fw-bold">SIMPAN PERUBAHAN</button>
                            <a href="artikel_saya.php" class="btn btn-light rounded-pill px-4">Batal</a>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>

<script src="https://code.jquery.com/jquery-3.4.1.slim.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/summernote@0.8.18/dist/summernote-lite.min.js"></script>
<script>
    $('#editor-edit').summernote({
        placeholder: 'Edit tulisanmu di sini...',
        tabsize: 2,
        height: 400,
        toolbar: [
            ['style', ['style']],
            ['font', ['bold', 'underline', 'clear']],
            ['color', ['color']],
            ['para', ['ul', 'ol', 'paragraph']],
            ['table', ['table']],
            ['insert', ['link', 'picture', 'video']],
            ['view', ['fullscreen', 'codeview']]
        ]
    });
</script>

<?php include 'includes/footer.php'; ?>