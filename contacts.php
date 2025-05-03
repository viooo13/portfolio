<?php
session_start();
if (!isset($_SESSION['admin'])) {
    header('Location: login.php');
    exit;
}

include 'includes/db.php';

// Hapus kontak
if (isset($_GET['delete'])) {
    $id = filter_input(INPUT_GET, 'delete', FILTER_VALIDATE_INT);
    if ($id) {
        $stmt = mysqli_prepare($conn, "DELETE FROM tbl_contact WHERE id_contact = ?");
        mysqli_stmt_bind_param($stmt, 'i', $id);
        mysqli_stmt_execute($stmt);
        mysqli_stmt_close($stmt);
    }
    header('Location: contacts.php');
    exit;
}

// Ambil data kontak
$contacts = mysqli_query($conn, "SELECT * FROM tbl_contact ORDER BY id_contact DESC");
?>
<!DOCTYPE html>
<html>
<head>
    <link rel="stylesheet" href="style.css">
    <title>Pesan Kontak</title>
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
            <div class="contactpg">
                <h1>Pesan Masuk</h1>
                <div class="contacts">
                    <?php while ($row = mysqli_fetch_assoc($contacts)) { ?>
                        <div class="contact-card">
                            <h3><?= htmlspecialchars($row['name_contact']) ?> (<?= htmlspecialchars($row['email_contact']) ?>)</h3>
                            <p><?= htmlspecialchars($row['message_contact']) ?></p>
                            <a href="contacts.php?delete=<?= $row['id_contact'] ?>">Hapus</a>
                        </div>
                    <?php } ?>
                </div>
            </div>
        </div>
    </div>
</body>
</html>