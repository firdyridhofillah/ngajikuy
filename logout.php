<?php
// 1. Mulai session supaya PHP tahu session mana yang mau dihapus
session_start();

// 2. Kosongkan semua data di dalam session (ID, Username, Admin, Writer)
$_SESSION = array();

// 3. Hancurkan session secara total dari server
session_destroy();

// 4. Lempar kembali ke halaman login atau beranda
header("Location: login.php");
exit();
?>