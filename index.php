<?php
$conn = mysqli_connect("localhost", "root", "", "db_porto");
if (!$conn) die("Koneksi gagal: " . mysqli_connect_error());

// Proses form contact
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['submit_contact'])) {
    $name = filter_input(INPUT_POST, 'name', FILTER_SANITIZE_STRING);
    $email = filter_input(INPUT_POST, 'email', FILTER_SANITIZE_EMAIL);
    $message = filter_input(INPUT_POST, 'message', FILTER_SANITIZE_STRING);

    $stmt = mysqli_prepare($conn, "INSERT INTO tbl_contact (name_contact, email_contact, message_contact) VALUES (?, ?, ?)");
    mysqli_stmt_bind_param($stmt, 'sss', $name, $email, $message);
    if (mysqli_stmt_execute($stmt)) {
        header("Location: index.php");
        exit;
    } else {
        echo "Gagal menyimpan pesan.";
    }
    mysqli_stmt_close($stmt);
}

// Ambil foto profil
$photo_query = mysqli_query($conn, "SELECT file_name FROM tbl_profile_photo ORDER BY uploaded_at DESC LIMIT 1");
$photo = mysqli_fetch_assoc($photo_query);
$photo_path = $photo ? "assets/uploads/" . $photo['file_name'] : "assets/uploads/default.jpg";
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Vio Adytia Syahputra - Web Developer</title>
    <link rel="stylesheet" href="admin/style.css">
</head>
<body>
<header>
    <nav>
        <div class="hamburger" onclick="toggleMenu()">
            <span></span>
            <span></span>
            <span></span>
        </div>
        <ul id="nav-menu">
            <li><a href="#about">About</a></li>
            <li><a href="#skills">Skills</a></li>
            <li><a href="#projects">Projects</a></li>
            <li><a href="#certificates">Certificates</a></li>
            <li><a href="#contact">Contact</a></li>
        </ul>
    </nav>
</header>

<div class="hmpg">
    <section id="about">
        <div class="about-content">
            <div class="about-text">
                <h1>Hi, I’m <br>Vio Adytia Syahputra</h1>
                <p>Saya seorang pengembang perangkat lunak dengan fokus pada web development,<br>
                    menggunakan PHP, MySQL, HTML, CSS, dan JavaScript. Saya selalu berusaha<br>
                    menciptakan solusi yang inovatif dan mudah digunakan.</p>
                <a href="#projects"><button>See My Work</button></a>
                <a href="#contact"><button>Contact Me</button></a>
            </div>
            <div class="about-photo">
                <img src="<?= htmlspecialchars($photo_path) ?>" alt="Profile Photo" class="profile-photo">
            </div>
        </div>
    </section>
</div>

<section class="skillpg" id="skills">
    <h1>My Skills</h1>
    <div class="skills">
        <?php
        $res = mysqli_query($conn, "SELECT * FROM tbl_skills ORDER BY id_skills DESC");
        while ($row = mysqli_fetch_assoc($res)) {
            echo "
            <div class='skill-card'>
                <h3>" . htmlspecialchars($row['title_skills']) . "</h3>
                <p>" . htmlspecialchars($row['text_skills']) . "</p>
            </div>";
        }
        ?>
    </div>
</section>

<section class="projectpg" id="projects">
    <h1>My Projects</h1>
    <div class="projects">
        <?php
        $res = mysqli_query($conn, "SELECT * FROM tbl_projects ORDER BY id_projects DESC");
        while ($row = mysqli_fetch_assoc($res)) {
            echo "
            <div class='project-card'>
                <h3>" . htmlspecialchars($row['title_projects']) . "</h3>
                <p>" . htmlspecialchars($row['text_projects']) . "</p>";
            if ($row['link_hosting']) {
                echo "<a href='" . htmlspecialchars($row['link_hosting']) . "' target='_blank' class='project-link'>Lihat Hosting</a>";
            }
            echo "</div>";
        }
        ?>
    </div>
</section>

<section class="certificatepg" id="certificates">
    <h1>My Certificates</h1>
    <div class="certificates">
        <?php
        $res = mysqli_query($conn, "SELECT * FROM tbl_certificates ORDER BY id_certificates DESC");
        while ($row = mysqli_fetch_assoc($res)) {
            $cert_image = $row['image_path'] ? "assets/certificates/" . htmlspecialchars($row['image_path']) : "assets/certificates/default.jpg";
            echo "
            <div class='certificate-card'>
                <img src='$cert_image' alt='Certificate Image' class='certificate-image' onclick='openModal(\"$cert_image\")'>
                <h3>" . htmlspecialchars($row['title_certificates']) . "</h3>
                <p>" . htmlspecialchars($row['text_certificates']) . "</p>
            </div>";
        }
        ?>
    </div>
</section>

<section class="contactpg" id="contact">
    <h1>Contact Me</h1>
    <form class="contact-form" method="POST">
        <input type="text" name="name" placeholder="Your Name" required>
        <input type="email" name="email" placeholder="Your Email" required>
        <textarea name="message" rows="5" placeholder="Your Message" required></textarea>
        <button type="submit" name="submit_contact">Send</button>
    </form>
</section>

<footer>
    <p>© <?= date("Y") ?> Vio Adytia Syahputra. All rights reserved.</p>
    <div class="social-icons">
        <a href="https://www.instagram.com/vioadytia.s/" target="_blank"><img src="https://img.icons8.com/ios-filled/25/ffffff/instagram-new.png"/></a>
        <a href="https://github.com/viooo13/" target="_blank"><img src="https://img.icons8.com/ios-glyphs/25/ffffff/github.png"/></a>
        <a href="https://twitter.com/vioadytia30" target="_blank"><img src="https://img.icons8.com/ios-filled/25/ffffff/twitter.png"/></a>
        <a href="https://web.whatsapp.com/085282952503" target="_blank"><img src="https://img.icons8.com/ios-filled/25/ffffff/whatsapp.png"/></a>
    </div>
</footer>

<!-- Modal for viewing certificate images -->
<div id="imageModal" class="modal">
    <span class="modal-close" onclick="closeModal()">&times;</span>
    <img id="modalImage" class="modal-content" alt="Certificate Image">
</div>

<script>
function toggleMenu() {
    const menu = document.getElementById('nav-menu');
    menu.classList.toggle('active');
}

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