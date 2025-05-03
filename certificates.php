<?php
session_start();
if (!isset($_SESSION['admin'])) {
    header('Location: login.php');
    exit;
}

include 'includes/db.php';

// Direktori penyimpanan gambar sertifikat
$upload_dir = '../assets/certificates/';
if (!is_dir($upload_dir)) {
    mkdir($upload_dir, 0755, true);
}

// Tambah sertifikat
$upload_error = '';
if (isset($_POST['add_certificate'])) {
    $title = filter_input(INPUT_POST, 'title', FILTER_SANITIZE_STRING);
    $text = filter_input(INPUT_POST, 'text', FILTER_SANITIZE_STRING);

    if (isset($_FILES['image']) && $_FILES['image']['error'] == UPLOAD_ERR_OK) {
        $file_tmp = $_FILES['image']['tmp_name'];
        $file_name = basename($_FILES['image']['name']);
        $file_ext = strtolower(pathinfo($file_name, PATHINFO_EXTENSION));
        $allowed_ext = ['jpg', 'jpeg', 'png'];

        if (in_array($file_ext, $allowed_ext)) {
            $new_file_name = 'certificate_' . time() . '.' . $file_ext;
            $dest_path = $upload_dir . $new_file_name;

            if (move_uploaded_file($file_tmp, $dest_path)) {
                $stmt = mysqli_prepare($conn, "INSERT INTO tbl_certificates (title_certificates, text_certificates, image_path) VALUES (?, ?, ?)");
                mysqli_stmt_bind_param($stmt, 'sss', $title, $text, $new_file_name);
                mysqli_stmt_execute($stmt);
                mysqli_stmt_close($stmt);
                header('Location: certificates.php');
                exit;
            } else {
                $upload_error = "Gagal mengunggah gambar.";
            }
        } else {
            $upload_error = "Format file tidak diizinkan. Gunakan JPG atau PNG.";
        }
    } else {
        $upload_error = "Pilih gambar untuk diunggah.";
    }
}

// Edit sertifikat
if (isset($_POST['edit_certificate'])) {
    $id = filter_input(INPUT_POST, 'id', FILTER_VALIDATE_INT);
    $title = filter_input(INPUT_POST, 'title', FILTER_SANITIZE_STRING);
    $text = filter_input(INPUT_POST, 'text', FILTER_SANITIZE_STRING);

    if ($id) {
        // Jika ada gambar baru
        if (isset($_FILES['image']) && $_FILES['image']['error'] == UPLOAD_ERR_OK) {
            $file_tmp = $_FILES['image']['tmp_name'];
            $file_name = basename($_FILES['image']['name']);
            $file_ext = strtolower(pathinfo($file_name, PATHINFO_EXTENSION));
            $allowed_ext = ['jpg', 'jpeg', 'png'];

            if (in_array($file_ext, $allowed_ext)) {
                $new_file_name = 'certificate_' . time() . '.' . $file_ext;
                $dest_path = $upload_dir . $new_file_name;

                // Hapus gambar lama
                $old_image_query = mysqli_query($conn, "SELECT image_path FROM tbl_certificates WHERE id_certificates = $id");
                $old_image = mysqli_fetch_assoc($old_image_query)['image_path'];
                if ($old_image && file_exists($upload_dir . $old_image)) {
                    unlink($upload_dir . $old_image);
                }

                if (move_uploaded_file($file_tmp, $dest_path)) {
                    $stmt = mysqli_prepare($conn, "UPDATE tbl_certificates SET title_certificates = ?, text_certificates = ?, image_path = ? WHERE id_certificates = ?");
                    mysqli_stmt_bind_param($stmt, 'sssi', $title, $text, $new_file_name, $id);
                    mysqli_stmt_execute($stmt);
                    mysqli_stmt_close($stmt);
                } else {
                    $upload_error = "Gagal mengunggah gambar.";
                }
            } else {
                $upload_error = "Format file tidak diizinkan. Gunakan JPG atau PNG.";
            }
        } else {
            // Tanpa gambar baru
            $stmt = mysqli_prepare($conn, "UPDATE tbl_certificates SET title_certificates = ?, text_certificates = ? WHERE id_certificates = ?");
            mysqli_stmt_bind_param($stmt, 'ssi', $title, $text, $id);
            mysqli_stmt_execute($stmt);
            mysqli_stmt_close($stmt);
        }
        header('Location: certificates.php');
        exit;
    }
}

// Hapus sertifikat
if (isset($_GET['delete'])) {
    $id = filter_input(INPUT_GET, 'delete', FILTER_VALIDATE_INT);
    if ($id) {
        $old_image_query = mysqli_query($conn, "SELECT image_path FROM tbl_certificates WHERE id_certificates = $id");
        $old_image = mysqli_fetch_assoc($old_image_query)['image_path'];
        if ($old_image && file_exists($upload_dir . $old_image)) {
            unlink($upload_dir . $old_image);
        }

        $stmt = mysqli_prepare($conn, "DELETE FROM tbl_certificates WHERE id_certificates = ?");
        mysqli_stmt_bind_param($stmt, 'i', $id);
        mysqli_stmt_execute($stmt);
        mysqli_stmt_close($stmt);
    }
    header('Location: certificates.php');
    exit;
}

// Ambil data sertifikat untuk edit
$edit_certificate = null;
if (isset($_GET['edit'])) {
    $id = filter_input(INPUT_GET, 'edit', FILTER_VALIDATE_INT);
    if ($id) {
        $stmt = mysqli_prepare($conn, "SELECT * FROM tbl_certificates WHERE id_certificates = ?");
        mysqli_stmt_bind_param($stmt, 'i', $id);
        mysqli_stmt_execute($stmt);
        $result = mysqli_stmt_get_result($stmt);
        $edit_certificate = mysqli_fetch_assoc($result);
        mysqli_stmt_close($stmt);
    }
}

// Ambil data sertifikat
$certificates = mysqli_query($conn, "SELECT * FROM tbl_certificates ORDER BY id_certificates DESC");
?>
<!DOCTYPE html>
<html>
<head>
    <link rel="stylesheet" href="style.css">
    <title>Kelola Certificates</title>
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
            <div class="certificatepg">
                <h1>Manajemen Certificates</h1>
                <form method="POST" enctype="multipart/form-data" class="contact-form">
                    <?php if ($edit_certificate) { ?>
                        <input type="hidden" name="id" value="<?= htmlspecialchars($edit_certificate['id_certificates']) ?>">
                        <input type="text" name="title" placeholder="Judul Certificate" value="<?= htmlspecialchars($edit_certificate['title_certificates']) ?>" required>
                        <textarea name="text" placeholder="Deskripsi Certificate" required><?= htmlspecialchars($edit_certificate['text_certificates']) ?></textarea>
                        <input type="file" name="image" accept="image/jpeg,image/png">
                        <button type="submit" name="edit_certificate">Update</button>
                        <a href="certificates.php" class="cancel-edit">Batal</a>
                    <?php } else { ?>
                        <input type="text" name="title" placeholder="Judul Certificate" required>
                        <textarea name="text" placeholder="Deskripsi Certificate" required></textarea>
                        <input type="file" name="image" accept="image/jpeg,image/png" required>
                        <button type="submit" name="add_certificate">Tambah</button>
                    <?php } ?>
                    <?php if ($upload_error) echo "<p class='error-message'>$upload_error</p>"; ?>
                </form>
                <div class="certificates">
                    <?php while ($row = mysqli_fetch_assoc($certificates)) { 
                        $cert_image = $row['image_path'] ? "../assets/certificates/" . htmlspecialchars($row['image_path']) : "../assets/certificates/default.jpg";
                    ?>
                        <div class="certificate-card">
                            <img src="<?= $cert_image ?>" alt="Certificate Image" class="certificate-image" onclick="openModal('<?= $cert_image ?>')">
                            <h3><?= htmlspecialchars($row['title_certificates']) ?></h3>
                            <p><?= htmlspecialchars($row['text_certificates']) ?></p>
                            <div class="certificate-actions">
                                <a href="certificates.php?edit=<?= $row['id_certificates'] ?>" class="edit-link">Edit</a>
                                <a href="certificates.php?delete=<?= $row['id_certificates'] ?>" class="delete-link" onclick="return confirm('Yakin ingin menghapus sertifikat ini?')">Hapus</a>
                            </div>
                        </div>
                    <?php } ?>
                </div>
            </div>
        </div>
    </div>

    <!-- Modal for viewing certificate images -->
    <div id="imageModal" class="modal">
        <span class="modal-close" onclick="closeModal()">×</span>
        <img id="modalImage" class="modal-content" alt="Certificate Image">
    </div>

    <script>
    function openModal(imageSrc) {
        const modal = document.getElementById('imageModal');
        const modalImg = document.getElementById('modalImage');
        modal.style.display = 'block';
        modalImg.src = imageSrc;
    }

    function closeModal() {
        const modal = document.getElementById('imageModal');
        modal.style.display = 'none';
    }

    // Close modal when clicking outside the image
    window.onclick = function(event) {
        const modal = document.getElementById('imageModal');
        if (event.target === modal) {
            modal.style.display = 'none';
        }
    }
    </script>
</body>
</html>