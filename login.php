<?php
session_start();

// Menangani logout
if (isset($_GET['logout'])) {
    session_destroy();
    header('Location: login.php');
    exit;
}

// Menangani login
$error = '';
if (isset($_POST['login'])) {
    $user = filter_input(INPUT_POST, 'username', FILTER_SANITIZE_STRING);
    $pass = filter_input(INPUT_POST, 'password', FILTER_SANITIZE_STRING);
    if ($user === 'admin' && $pass === '123') {
        $_SESSION['admin'] = true;
        header('Location: dashboard.php');
        exit;
    } else {
        $error = "Username atau password salah!";
    }
}
?>
<!DOCTYPE html>
<html>
<head>
    <link rel="stylesheet" href="style.css">
    <title>Login Admin</title>
</head>
<body>
    <div class="login-container">
        <div class="login-box">
            <h1>Login Admin</h1>
            <?php if ($error) echo "<p class='error-message'>$error</p>"; ?>
            <form method="POST" class="login-form">
                <div class="input-group">
                    <input type="text" name="username" placeholder="Username" required>
                </div>
                <div class="input-group">
                    <input type="password" name="password" placeholder="Password" required>
                </div>
                <button type="submit" name="login" class="login-button">Login</button>
            </form>
        </div>
    </div>
</body>
</html>