<?php
session_start();





// Fungsi untuk mengambil data dari API EQuran.id
function getDaftarSurah() {
    $url = "https://equran.id/api/v2/surat";
    $response = @file_get_contents($url); // Pakai @ untuk meredam error jika internet mati
    
    if ($response === FALSE) {
        return []; // Kembalikan array kosong jika gagal
    }

    $data = json_decode($response, true);
    return $data['data'];
}

//getDetailSurah
function getDetailSurah($nomor) {
    $url = "https://equran.id/api/v2/surat/" . $nomor;
    $response = @file_get_contents($url);
    
    if ($response === FALSE) {
        return null;
    }

    $data = json_decode($response, true);
    return $data['data'];
}
?>