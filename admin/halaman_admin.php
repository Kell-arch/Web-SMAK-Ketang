<?php
session_start();
require_once __DIR__ . '/../data/koneksi.php';

if (!isset($_SESSION['admin'])) {
    header('Location: login_admin.php');
    exit;
}

$admin = $_SESSION['admin'];

$total_siswa = 0;
$total_mapel = 0;
$total_guru = 0;
$total_pengumuman = 0;

$q = mysqli_query($koneksi, "SELECT COUNT(*) as total FROM siswa");
if ($q) $total_siswa = (int)mysqli_fetch_assoc($q)['total'];

$q = mysqli_query($koneksi, "SELECT COUNT(*) as total FROM mapel");
if ($q) $total_mapel = (int)mysqli_fetch_assoc($q)['total'];

$q = mysqli_query($koneksi, "SELECT COUNT(*) as total FROM guru");
if ($q) $total_guru = (int)mysqli_fetch_assoc($q)['total'];

$q = mysqli_query($koneksi, "SELECT COUNT(*) as total FROM pengumuman");
if ($q) $total_pengumuman = (int)mysqli_fetch_assoc($q)['total'];
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard - Admin SMAK St. Stefanus Ketang</title>
    <link rel="shortcut icon" href="../assets/img/logo-sekolah.ico" type="image/x-icon">
    <link rel="stylesheet" href="admin.css">
    <script src="admin.js" defer></script>
</head>
<body>
    <aside class="sidebar">
        <div class="sidebar-brand">
            <img src="../assets/img/logo-sekolah.png" alt="Logo">
            <div class="brand-text">
                <strong>Admin Panel</strong>
                SMAK St. Stefanus
            </div>
        </div>
        <nav class="sidebar-menu">
            <div class="menu-label">Menu</div>
            <a href="halaman_admin.php" class="active">
                <span class="icon"><svg viewBox="0 0 24 24"><path d="M3 9l9-7 9 7v11a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2z"/><polyline points="9 22 9 12 15 12 15 22"/></svg></span>
                Dashboard
            </a>
            <a href="guru.php">
                <span class="icon"><svg viewBox="0 0 24 24"><path d="M17 21v-2a4 4 0 0 0-4-4H5a4 4 0 0 0-4 4v2"/><circle cx="9" cy="7" r="4"/><path d="M23 21v-2a4 4 0 0 0-3-3.87"/><path d="M16 3.13a4 4 0 0 1 0 7.75"/></svg></span>
                Guru
            </a>
            <a href="siswa.php">
                <span class="icon"><svg viewBox="0 0 24 24"><path d="M17 21v-2a4 4 0 0 0-4-4H5a4 4 0 0 0-4 4v2"/><circle cx="9" cy="7" r="4"/><path d="M23 21v-2a4 4 0 0 0-3-3.87"/><path d="M16 3.13a4 4 0 0 1 0 7.75"/></svg></span>
                Siswa
            </a>
            <a href="mapel.php">
                <span class="icon"><svg viewBox="0 0 24 24"><path d="M4 19.5A2.5 2.5 0 0 1 6.5 17H20"/><path d="M6.5 2H20v20H6.5A2.5 2.5 0 0 1 4 19.5v-15A2.5 2.5 0 0 1 6.5 2z"/></svg></span>
                Mata Pelajaran
            </a>
            <a href="pengumuman.php">
                <span class="icon"><svg viewBox="0 0 24 24"><path d="M18 8A6 6 0 0 0 6 8c0 7-3 9-3 9h18s-3-2-3-9"/><path d="M13.73 21a2 2 0 0 1-3.46 0"/></svg></span>
                Pengumuman
            </a>
            <a href="dokumentasi.php">
                <span class="icon"><svg viewBox="0 0 24 24"><rect x="3" y="3" width="18" height="18" rx="2" ry="2"/><circle cx="8.5" cy="8.5" r="1.5"/><polyline points="21 15 16 10 5 21"/></svg></span>
                Dokumentasi
            </a>
        </nav>
        <div class="sidebar-footer">
            <a href="login_admin.php?logout=1" onclick="return confirm('Yakin ingin logout?')">
                <span class="icon"><svg viewBox="0 0 24 24"><path d="M9 21H5a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2h4"/><polyline points="16 17 21 12 16 7"/><line x1="21" y1="12" x2="9" y2="12"/></svg></span>
                Logout
            </a>
        </div>
    </aside>

    <div class="sidebar-overlay" id="sidebarOverlay"></div>

    <main class="main-content">
        <div class="page-header">
            <div style="display:flex;align-items:center;gap:12px;flex-wrap:wrap;">
                <button class="mobile-toggle" id="mobileToggle" aria-label="Toggle sidebar">
                    <svg viewBox="0 0 24 24"><line x1="3" y1="6" x2="21" y2="6"/><line x1="3" y1="12" x2="21" y2="12"/><line x1="3" y1="18" x2="21" y2="18"/></svg>
                </button>
                <div>
                    <h1>Dashboard</h1>
                    <p>Selamat datang, <?= htmlspecialchars($admin['nama_lengkap']) ?></p>
                </div>
            </div>
        </div>

        <div class="card-grid">
            <div class="card">
                <div class="card-icon">
                    <svg viewBox="0 0 24 24"><path d="M17 21v-2a4 4 0 0 0-4-4H5a4 4 0 0 0-4 4v2"/><circle cx="9" cy="7" r="4"/></svg>
                </div>
                <div class="card-label">Jumlah Siswa</div>
                <div class="card-value"><?= $total_siswa ?></div>
            </div>
            <div class="card">
                <div class="card-icon yellow">
                    <svg viewBox="0 0 24 24"><path d="M4 19.5A2.5 2.5 0 0 1 6.5 17H20"/><path d="M6.5 2H20v20H6.5A2.5 2.5 0 0 1 4 19.5v-15A2.5 2.5 0 0 1 6.5 2z"/></svg>
                </div>
                <div class="card-label">Mata Pelajaran</div>
                <div class="card-value yellow"><?= $total_mapel ?></div>
            </div>
            <div class="card">
                <div class="card-icon green">
                    <svg viewBox="0 0 24 24"><path d="M17 21v-2a4 4 0 0 0-4-4H5a4 4 0 0 0-4 4v2"/><circle cx="9" cy="7" r="4"/><path d="M23 21v-2a4 4 0 0 0-3-3.87"/><path d="M16 3.13a4 4 0 0 1 0 7.75"/></svg>
                </div>
                <div class="card-label">Jumlah Guru</div>
                <div class="card-value"><?= $total_guru ?></div>
            </div>
            <div class="card">
                <div class="card-icon purple">
                    <svg viewBox="0 0 24 24"><path d="M18 8A6 6 0 0 0 6 8c0 7-3 9-3 9h18s-3-2-3-9"/><path d="M13.73 21a2 2 0 0 1-3.46 0"/></svg>
                </div>
                <div class="card-label">Pengumuman</div>
                <div class="card-value"><?= $total_pengumuman ?></div>
            </div>
        </div>
    </main>
</body>
</html>
