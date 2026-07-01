<?php
session_start();
session_destroy();

// Jika menggunakan token, hapus dari localStorage via JS, tapi untuk PHP, cukup destroy session
// Untuk API logout, bisa tambahkan fetch di JS
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Logout - Website Absensi</title>
    <script>
        // Hapus token dari localStorage
        localStorage.removeItem('token');
        // Redirect ke login
        window.location.href = 'login.php';
    </script>
</head>
<body>
    <p>Logging out...</p>
</body>
</html>