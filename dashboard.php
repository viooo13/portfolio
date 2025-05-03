<?php
session_start();

// Periksa sesi dan waktu kedaluwarsa
if (!isset($_SESSION['admin']) || !$_SESSION['admin'] || (isset($_SESSION['last_activity']) && (time() - $_SESSION['last_activity'] > 1800))) {
    session_destroy();
    header('Location: login.php?redirect=dashboard');
    exit;
}

// Set header untuk mencegah caching
header("Cache-Control: no-store, no-cache, must-revalidate, max-age=0");
header("Cache-Control: post-check=0, pre-check=0", false);
header("Pragma: no-cache");

$_SESSION['last_activity'] = time(); // Perbarui waktu aktivitas
?>
<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="style.css">
    <title>Dashboard</title>
</head>
<body>
    <div class="dashboard-container">
        <div class="sidebar">
            <h2>Admin Panel</h2>
            <ul>
                <li><a href="skills.php">Kelola Skills</a></li>
                <li><a href="projects.php">Kelola Projects</a></li>
                <li><a href="certificates.php">Kelola Certificates</a></li>
                <li><a href="contacts.php">Lihat Pesan</a></li>
                <li><a href="profile.php">Kelola Foto Profil</a></li>
                <li><a href="login.php?logout=1">Logout</a></li>
            </ul>
        </div>
        <div class="main-content">
            <div class="welcome-message">
                <h1>Selamat Datang, Admin!</h1>
                <p>Gunakan menu di sisi kiri untuk mengelola konten.</p>
            </div>
        </div>
    </div>
</body>
</html>