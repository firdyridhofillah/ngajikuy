<?php
session_start();

// Jika sudah ada session user_id, langsung ke home
if (isset($_SESSION['user_id'])) {
    header("Location: home.php");
    exit();
} else {
    // Jika belum, paksa ke login
    header("Location: login.php");
    exit();
}