<?php
require_once 'config/database.php';

$username = 'admin_firdy';
$password = password_hash('admin123', PASSWORD_DEFAULT); // Ganti password sesukamu

$sql = "INSERT INTO users (username, password, is_admin, is_writer) VALUES (?, ?, 1, 1)";
$stmt = $koneksi->prepare($sql);
$stmt->bind_param("ss", $username, $password);

if($stmt->execute()) {
    echo "Admin berhasil dibuat! Username: admin_firdy, Pass: admin123";
}
?>