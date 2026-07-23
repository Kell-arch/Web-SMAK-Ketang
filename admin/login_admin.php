<?php
session_start();
require_once __DIR__ . '/../data/koneksi.php';

if (isset($_GET['logout']) && $_GET['logout'] == 1) {
    session_destroy();
    header('Location: login_admin.php');
    exit;
}

if (isset($_SESSION['admin'])) {
    header('Location: halaman_admin.php');
    exit;
}

$error = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['login'])) {
    $username = trim($_POST['username'] ?? '');
    $password = $_POST['password'] ?? '';

    if ($username === '' || $password === '') {
        $error = 'Username dan password harus diisi.';
    } else {
        $stmt = mysqli_prepare($koneksi, "SELECT id, username, password, nama_lengkap FROM admin WHERE username = ?");
        mysqli_stmt_bind_param($stmt, 's', $username);
        mysqli_stmt_execute($stmt);
        $result = mysqli_stmt_get_result($stmt);
        $user = mysqli_fetch_assoc($result);

        if ($user && password_verify($password, $user['password'])) {
            $_SESSION['admin'] = [
                'id' => $user['id'],
                'username' => $user['username'],
                'nama_lengkap' => $user['nama_lengkap'],
            ];
            header('Location: halaman_admin.php');
            exit;
        } else {
            $error = 'Username atau password salah.';
        }
    }
}
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login Admin - SMAK St. Stefanus Ketang</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Lexend:wght@100..900&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="login.css">
</head>
<body>
    <div class="login-container">
        <div class="login-card">
            <div class="login-header">
                <img src="../assets/img/logo-sekolah.png" alt="Logo Sekolah">
                <h1>Login Admin</h1>
                <p>SMAK St. Stefanus Ketang — <span>Cerdas Berkarakter</span></p>
            </div>

            <?php if ($error !== ''): ?>
                <div class="error-message"><?= htmlspecialchars($error) ?></div>
            <?php endif; ?>

            <form action="" method="POST">
                <div class="form-group">
                    <label for="username">Username</label>
                    <div class="input-wrapper">
                        <span class="icon">
                            <svg viewBox="0 0 24 24"><circle cx="12" cy="8" r="4"/><path d="M4 20c0-4 3.6-7 8-7s8 3 8 7"/></svg>
                        </span>
                        <input type="text" id="username" name="username" placeholder="Masukkan username" required autocomplete="username">
                    </div>
                </div>

                <div class="form-group">
                    <label for="password">Password</label>
                    <div class="input-wrapper">
                        <span class="icon">
                            <svg viewBox="0 0 24 24"><rect x="3" y="11" width="18" height="11" rx="2"/><path d="M7 11V7a5 5 0 0 1 10 0v4"/></svg>
                        </span>
                        <input type="password" id="password" name="password" placeholder="Masukkan password" required autocomplete="current-password">
                    </div>
                </div>

                <button type="submit" class="login-btn" name="login">Masuk</button>
            </form>

            <div class="login-footer">
                <a href="../index.php">&larr; Kembali ke Beranda</a>
            </div>
        </div>
    </div>
</body>
</html>
