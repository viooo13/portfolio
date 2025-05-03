<?php
session_start();
if (!isset($_SESSION['admin'])) {
    header('Location: login.php');
    exit;
}

include 'includes/db.php';

// Tambah project
if (isset($_POST['add_project'])) {
    $title = filter_input(INPUT_POST, 'title', FILTER_SANITIZE_STRING);
    $text = filter_input(INPUT_POST, 'text', FILTER_SANITIZE_STRING);
    $link = filter_input(INPUT_POST, 'link', FILTER_SANITIZE_URL);
    $stmt = mysqli_prepare($conn, "INSERT INTO tbl_projects (title_projects, text_projects, link_hosting) VALUES (?, ?, ?)");
    mysqli_stmt_bind_param($stmt, 'sss', $title, $text, $link);
    mysqli_stmt_execute($stmt);
    mysqli_stmt_close($stmt);
    header('Location: projects.php');
    exit;
}

// Edit project
if (isset($_POST['edit_project'])) {
    $id = filter_input(INPUT_POST, 'id', FILTER_VALIDATE_INT);
    $title = filter_input(INPUT_POST, 'title', FILTER_SANITIZE_STRING);
    $text = filter_input(INPUT_POST, 'text', FILTER_SANITIZE_STRING);
    $link = filter_input(INPUT_POST, 'link', FILTER_SANITIZE_URL);
    if ($id) {
        $stmt = mysqli_prepare($conn, "UPDATE tbl_projects SET title_projects = ?, text_projects = ?, link_hosting = ? WHERE id_projects = ?");
        mysqli_stmt_bind_param($stmt, 'sssi', $title, $text, $link, $id);
        mysqli_stmt_execute($stmt);
        mysqli_stmt_close($stmt);
    }
    header('Location: projects.php');
    exit;
}

// Hapus project
if (isset($_GET['delete'])) {
    $id = filter_input(INPUT_GET, 'delete', FILTER_VALIDATE_INT);
    if ($id) {
        $stmt = mysqli_prepare($conn, "DELETE FROM tbl_projects WHERE id_projects = ?");
        mysqli_stmt_bind_param($stmt, 'i', $id);
        mysqli_stmt_execute($stmt);
        mysqli_stmt_close($stmt);
    }
    header('Location: projects.php');
    exit;
}

// Ambil data project untuk edit
$edit_project = null;
if (isset($_GET['edit'])) {
    $id = filter_input(INPUT_GET, 'edit', FILTER_VALIDATE_INT);
    if ($id) {
        $stmt = mysqli_prepare($conn, "SELECT * FROM tbl_projects WHERE id_projects = ?");
        mysqli_stmt_bind_param($stmt, 'i', $id);
        mysqli_stmt_execute($stmt);
        $result = mysqli_stmt_get_result($stmt);
        $edit_project = mysqli_fetch_assoc($result);
        mysqli_stmt_close($stmt);
    }
}

// Ambil data projects
$projects = mysqli_query($conn, "SELECT * FROM tbl_projects ORDER BY id_projects DESC");
?>
<!DOCTYPE html>
<html>
<head>
    <link rel="stylesheet" href="style.css">
    <title>Kelola Projects</title>
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
            <div class="projectpg">
                <h1>Manajemen Projects</h1>
                <form method="POST" class="contact-form">
                    <?php if ($edit_project) { ?>
                        <input type="hidden" name="id" value="<?= htmlspecialchars($edit_project['id_projects']) ?>">
                        <input type="text" name="title" placeholder="Judul Project" value="<?= htmlspecialchars($edit_project['title_projects']) ?>" required>
                        <textarea name="text" placeholder="Deskripsi Project" required><?= htmlspecialchars($edit_project['text_projects']) ?></textarea>
                        <input type="url" name="link" placeholder="Link Hosting (opsional)" value="<?= htmlspecialchars($edit_project['link_hosting']) ?>">
                        <button type="submit" name="edit_project">Update</button>
                        <a href="projects.php" class="cancel-edit">Batal</a>
                    <?php } else { ?>
                        <input type="text" name="title" placeholder="Judul Project" required>
                        <textarea name="text" placeholder="Deskripsi Project" required></textarea>
                        <input type="url" name="link" placeholder="Link Hosting (opsional)">
                        <button type="submit" name="add_project">Tambah</button>
                    <?php } ?>
                </form>
                <div class="projects">
                    <?php while ($row = mysqli_fetch_assoc($projects)) { ?>
                        <div class="project-card">
                            <h3><?= htmlspecialchars($row['title_projects']) ?></h3>
                            <p><?= htmlspecialchars($row['text_projects']) ?></p>
                            <?php if ($row['link_hosting']) { ?>
                                <a href="<?= htmlspecialchars($row['link_hosting']) ?>" target="_blank" class="project-link">Lihat Hosting</a>
                            <?php } ?>
                            <div class="project-actions">
                                <a href="projects.php?edit=<?= $row['id_projects'] ?>" class="edit-link">Edit</a>
                                <a href="projects.php?delete=<?= $row['id_projects'] ?>" class="delete-link" onclick="return confirm('Yakin ingin menghapus project ini?')">Hapus</a>
                            </div>
                        </div>
                    <?php } ?>
                </div>
            </div>
        </div>
    </div>
</body>
</html>