<?php
session_start();
require_once 'config/database.php';
if(!isset($_SESSION['is_writer']) || $_SESSION['is_writer'] != 1) { die("Akses ditolak!"); }

if(isset($_POST['simpan'])) {
    $judul = $_POST['judul'];
    $isi = $_POST['isi'];
    $penulis = $_SESSION['user_id'];
    
    // Upload Thumbnail Judul
    $nama_file = $_FILES['thumbnail']['name'];
    $tmp_file = $_FILES['thumbnail']['tmp_name'];
    $ekstensi = pathinfo($nama_file, PATHINFO_EXTENSION);
    $new_name = time().'.'.$ekstensi;
    move_uploaded_file($tmp_file, "uploads/".$new_name);

    $sql = "INSERT INTO artikel (judul, isi, thumbnail, penulis_id, status) VALUES (?, ?, ?, ?, 'published')";
    $stmt = $koneksi->prepare($sql);
    $stmt->bind_param("sssi", $judul, $isi, $new_name, $penulis);
    $stmt->execute();
    header("Location: artikel_list.php");
}

include 'includes/header.php';
?>

<link href="https://cdn.jsdelivr.net/npm/summernote@0.8.18/dist/summernote-lite.min.css" rel="stylesheet">

<div class="container py-5">
    <div class="card border-0 shadow-sm rounded-4 p-4">
        <h4 class="fw-bold mb-4 text-success">Tulis Artikel Baru</h4>
        <form action="" method="POST" enctype="multipart/form-data">
            <div class="mb-3">
                <label class="fw-bold small">Judul Artikel</label>
                <input type="text" name="judul" class="form-control rounded-3" required>
            </div>
            <div class="mb-3">
                <label class="fw-bold small">Gambar Sampul (Thumbnail)</label>
                <input type="file" name="thumbnail" class="form-control rounded-3" required>
            </div>
            <div class="mb-3">
                <label class="fw-bold small">Isi Artikel</label>
                <textarea name="isi" id="summernote"></textarea>
            </div>
            <button type="submit" name="simpan" class="btn btn-success w-100 py-3 rounded-pill fw-bold">TERBITKAN ARTIKEL</button>
        </form>
    </div>
</div>

<script src="https://code.jquery.com/jquery-3.4.1.slim.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/summernote@0.8.18/dist/summernote-lite.min.js"></script>
<script>
    $('#summernote').summernote({
        placeholder: 'Tulis ilmu harianmu di sini, Fir...',
        tabsize: 2,
        height: 400,
        toolbar: [
            ['style', ['style']],
            ['font', ['bold', 'underline', 'clear']],
            ['color', ['color']],
            ['para', ['ul', 'ol', 'paragraph']],
            ['table', ['table']],
            ['insert', ['link', 'picture', 'video']],
            ['view', ['fullscreen', 'codeview', 'help']]
        ]
    });
</script>