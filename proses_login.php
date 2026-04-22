<?php
session_start();
require_once 'config/database.php';

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $username = mysqli_real_escape_string($koneksi, $_POST['username']);
    $password = $_POST['password'];

    // Query hanya mencari berdasarkan username (Tanpa Kolom Email)
    $sql = "SELECT * FROM users WHERE username = ?";
    $stmt = $koneksi->prepare($sql);
    
    if ($stmt) {
        $stmt->bind_param("s", $username);
        $stmt->execute();
        $result = $stmt->get_result();

        if ($result->num_rows > 0) {
            $user = $result->fetch_assoc();

            // Verifikasi Password
            if (password_verify($password, $user['password'])) {
                
                // Simpan data ke session
                $_SESSION['user_id']   = $user['id'];
                $_SESSION['username']  = $user['username'];
                $_SESSION['is_admin']  = $user['is_admin'] ?? 0;
                $_SESSION['is_writer'] = $user['is_writer'] ?? 0;

                // Lempar ke home.php
                header("Location: home.php");
                exit();

            } else {
                echo "<script>alert('Password salah!'); window.location='login.php';</script>";
            }
        } else {
            echo "<script>alert('Username tidak ditemukan!'); window.location='login.php';</script>";
        }
        $stmt->close();
    } else {
        echo "Error: " . $koneksi->error;
    }
} else {
    header("Location: login.php");
    exit();
}