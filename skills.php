<?php
session_start();
if (!isset($_SESSION['admin'])) {
    header('Location: login.php');
    exit;
}

include 'includes/db.php';

// Tambah skill
if (isset($_POST['add_skill'])) {
    $title = filter_input(INPUT_POST, 'title', FILTER_SANITIZE_STRING);
    $text = filter_input(INPUT_POST, 'text', FILTER_SANITIZE_STRING);
    $stmt = mysqli_prepare($conn, "INSERT INTO tbl_skills (title_skills, text_skills) VALUES (?, ?)");
    mysqli_stmt_bind_param($stmt, 'ss', $title, $text);
    mysqli_stmt_execute($stmt);
    mysqli_stmt_close($stmt);
    header('Location: skills.php');
    exit;
}

// Hapus skill
if (isset($_GET['delete'])) {
    $id = filter_input(INPUT_GET, 'delete', FILTER_VALIDATE_INT);
    if ($id) {
        $stmt = mysqli_prepare($conn, "DELETE FROM tbl_skills WHERE id_skills = ?");
        mysqli_stmt_bind_param($stmt, 'i', $id);
        mysqli_stmt_execute($stmt);
        mysqli_stmt_close($stmt);
    }
    header('Location: skills.php');
    exit;
}

// Ambil data skills
$skills = mysqli_query($conn, "SELECT * FROM tbl_skills ORDER BY id_skills DESC");
?>
<!DOCTYPE html>
<html>
<head>
    <link rel="stylesheet" href="style.css">
    <title>Kelola Skills</title>
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
            <div class="skillpg">
                <h1>Manajemen Skills</h1>
                <form method="POST" class="contact-form">
                    <input type="text" name="title" placeholder="Judul Skill" required>
                    <textarea name="text" placeholder="Deskripsi Skill" required></textarea>
                    <button type="submit" name="add_skill">Tambah</button>
                </form>
                <div class="skills">
                    <?php while ($row = mysqli_fetch_assoc($skills)) { ?>
                        <div class="skill-card">
                            <h3><?= htmlspecialchars($row['title_skills']) ?></h3>
                            <p><?= htmlspecialchars($row['text_skills']) ?></p>
                            <a href="skills.php?delete=<?= $row['id_skills'] ?>">Hapus</a>
                        </div>
                    <?php } ?>
                </div>
            </div>
        </div>
    </div>
</body>
</html>