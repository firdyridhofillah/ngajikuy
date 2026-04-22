<?php
include 'config/database.php';
// Query ambil top 10 bulan ini
$query = "SELECT users.username, MAX(leaderboard.score) as highscore 
          FROM leaderboard 
          JOIN users ON leaderboard.user_id = users.id 
          WHERE MONTH(leaderboard.created_at) = MONTH(CURRENT_DATE())
          GROUP BY users.id 
          ORDER BY highscore DESC LIMIT 10";
// ... (tampilkan dalam tabel Bootstrap yang cantik)