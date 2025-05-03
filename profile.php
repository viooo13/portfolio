<?php
session_start();
if (!isset($_SESSION['admin'])) {
    header('Location: login.php');
    exit;
}

include 'includes/db.php';

// Direktori penyimpanan foto
$upload_dir = '../assets/uploads/';
if (!is_dir($upload_dir)) {
    mkdir($upload_dir, 0755, true);
}

// Proses unggah foto
$upload_error = '';
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['upload_photo'])) {
    if (isset($_FILES['photo']) && $_FILES['photo']['error'] == UPLOAD_ERR_OK) {
        $file_tmp = $_FILES['photo']['tmp_name'];
        $file_name = basename($_FILES['photo']['name']);
        $file_ext = strtolower(pathinfo($file_name, PATHINFO_EXTENSION));
        $allowed_ext = ['jpg', 'jpeg', 'png'];

        if (in_array($file_ext, $allowed_ext)) {
            $new_file_name = 'profile_' . time() . '.' . $file_ext;
            $dest_path = $upload_dir . $new_file_name;

            // Hapus foto lama dari database dan direktori
            $old_photo = mysqli_query($conn, "SELECT file_name FROM tbl_profile_photo LIMIT 1");
            if ($old_photo && mysqli_num_rows($old_photo) > 0) {
                $old_file = mysqli_fetch_assoc($old_photo)['file_name'];
                if (file_exists($upload_dir . $old_file)) {
                    unlink($upload_dir . $old_file);
                }
                mysqli_query($conn, "DELETE FROM tbl_profile_photo");
            }

            // Simpan foto baru
            if (move_uploaded_file($file_tmp, $dest_path)) {
                $stmt = mysqli_prepare($conn, "INSERT INTO tbl_profile_photo (file_name) VALUES (?)");
                mysqli_stmt_bind_param($stmt, 's', $new_file_name);
                mysqli_stmt_execute($stmt);
                mysqli_stmt_close($stmt);
                header('Location: profile.php');
                exit;
            } else {
                $upload_error = "Gagal mengunggah foto.";
            }
        } else {
            $upload_error = "Format file tidak diizinkan. Gunakan JPG atau PNG.";
        }
    } else {
        $upload_error = "Pilih file untuk diunggah.";
    }
}

// Proses hapus foto
if (isset($_GET['delete'])) {
    $old_photo = mysqli_query($conn, "SELECT file_name FROM tbl_profile_photo LIMIT 1");
    if ($old_photo && mysqli_num_rows($old_photo) > 0) {
        $old_file = mysqli_fetch_assoc($old_photo)['file_name'];
        if (file_exists($upload_dir . $old_file)) {
            unlink($upload_dir . $old_file);
        }
        mysqli_query($conn, "DELETE FROM tbl_profile_photo");
    }
    header('Location: profile.php');
    exit;
}

// Ambil foto saat ini
$photo_query = mysqli_query($conn, "SELECT file_name FROM tbl_profile_photo ORDER BY uploaded_at DESC LIMIT 1");
$photo = mysqli_fetch_assoc($photo_query);
$photo_path = $photo ? $upload_dir . $photo['file_name'] : '../assets/uploads/default.jpg';
?>
<!DOCTYPE html>
<html>
<head>
    <link rel="stylesheet" href="style.css">
    <title>Kelola Foto Profil</title>
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
            <div class="profilepg">
                <h1>Kelola Foto Profil</h1>
                <div class="current-photo">
                    <h3>Foto Saat Ini</h3>
                    <img src="<?= htmlspecialchars($photo_path) ?>" alt="Profile Photo" class="profile-photo">
                    <?php if ($photo) { ?>
                        <a href="profile.php?delete=1" class="delete-photo">Hapus Foto</a>
                    <?php } ?>
                </div>
                <form method="POST" enctype="multipart/form-data" class="contact-form">
                    <h3>Unggah Foto Baru</h3>
                    <?php if ($upload_error) echo "<p class='error-message'>$upload_error</p>"; ?>
                    <input type="file" name="photo" accept="image/jpeg,image/png" required>
                    <button type="submit" name="upload_photo">Unggah</button>
                </form>
            </div>
        </div>
    </div>
</body>
</html>