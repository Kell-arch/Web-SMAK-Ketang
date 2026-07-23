<?php
session_start();
require_once __DIR__ . '/../data/koneksi.php';

if (!isset($_SESSION['admin'])) {
    header('Location: login_admin.php');
    exit;
}

$flash = '';
$flash_type = '';

if (isset($_SESSION['flash'])) {
    $flash = $_SESSION['flash']['message'];
    $flash_type = $_SESSION['flash']['type'];
    unset($_SESSION['flash']);
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $action = $_POST['action'] ?? '';

    if ($action === 'tambah') {
        $nomor_siswa = trim($_POST['nomor_siswa'] ?? '');
        $nama = trim($_POST['nama_siswa'] ?? '');

        if ($nomor_siswa !== '' && $nama !== '') {
            $stmt = mysqli_prepare($koneksi, "INSERT INTO siswa (Nomor_Siswa, Nama_Siswa) VALUES (?, ?)");
            mysqli_stmt_bind_param($stmt, 'is', $nomor_siswa, $nama);
            if (mysqli_stmt_execute($stmt)) {
                $_SESSION['flash'] = ['message' => 'Data siswa berhasil ditambahkan.', 'type' => 'success'];
            } else {
                $_SESSION['flash'] = ['message' => 'Gagal menambahkan data siswa.', 'type' => 'error'];
            }
        } else {
            $_SESSION['flash'] = ['message' => 'Semua field harus diisi.', 'type' => 'error'];
        }
        header('Location: siswa.php');
        exit;
    }

    if ($action === 'edit') {
        $id = (int)($_POST['id'] ?? 0);
        $nomor_siswa = trim($_POST['nomor_siswa'] ?? '');
        $nama = trim($_POST['nama_siswa'] ?? '');

        if ($id > 0 && $nomor_siswa !== '' && $nama !== '') {
            $stmt = mysqli_prepare($koneksi, "UPDATE siswa SET Nomor_Siswa = ?, Nama_Siswa = ? WHERE id = ?");
            mysqli_stmt_bind_param($stmt, 'isi', $nomor_siswa, $nama, $id);
            if (mysqli_stmt_execute($stmt)) {
                $_SESSION['flash'] = ['message' => 'Data siswa berhasil diubah.', 'type' => 'success'];
            } else {
                $_SESSION['flash'] = ['message' => 'Gagal mengubah data siswa.', 'type' => 'error'];
            }
        } else {
            $_SESSION['flash'] = ['message' => 'Data tidak valid.', 'type' => 'error'];
        }
        header('Location: siswa.php');
        exit;
    }

    if ($action === 'hapus') {
        $id = (int)($_POST['id'] ?? 0);
        if ($id > 0) {
            $stmt = mysqli_prepare($koneksi, "DELETE FROM siswa WHERE id = ?");
            mysqli_stmt_bind_param($stmt, 'i', $id);
            if (mysqli_stmt_execute($stmt)) {
                $_SESSION['flash'] = ['message' => 'Data siswa berhasil dihapus.', 'type' => 'success'];
            } else {
                $_SESSION['flash'] = ['message' => 'Gagal menghapus data siswa.', 'type' => 'error'];
            }
        }
        header('Location: siswa.php');
        exit;
    }
}

$result = mysqli_query($koneksi, "SELECT * FROM siswa ORDER BY Nama_Siswa ASC");
$siswa_list = [];
while ($row = mysqli_fetch_assoc($result)) {
    $siswa_list[] = $row;
}
$total = count($siswa_list);
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Data Siswa - Admin SMAK St. Stefanus Ketang</title>
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
            <a href="halaman_admin.php">
                <span class="icon"><svg viewBox="0 0 24 24"><path d="M3 9l9-7 9 7v11a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2z"/><polyline points="9 22 9 12 15 12 15 22"/></svg></span>
                Dashboard
            </a>
            <a href="guru.php">
                <span class="icon"><svg viewBox="0 0 24 24"><path d="M17 21v-2a4 4 0 0 0-4-4H5a4 4 0 0 0-4 4v2"/><circle cx="9" cy="7" r="4"/><path d="M23 21v-2a4 4 0 0 0-3-3.87"/><path d="M16 3.13a4 4 0 0 1 0 7.75"/></svg></span>
                Guru
            </a>
            <a href="siswa.php" class="active">
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
                    <h1>Data Siswa</h1>
                    <p>Total <?= $total ?> siswa</p>
                </div>
            </div>
            <button class="btn btn-primary" onclick="openModal('modalTambah')">+ Tambah Siswa</button>
        </div>

        <?php if ($flash): ?>
            <div class="flash-message flash-<?= $flash_type ?>"><?= htmlspecialchars($flash) ?></div>
        <?php endif; ?>

        <div class="table-wrapper">
            <table>
                <thead>
                    <tr>
                        <th>No</th>
                        <th>Nomor Siswa</th>
                        <th>Nama Siswa</th>
                        <th>Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if (count($siswa_list) > 0): $no = 1; ?>
                        <?php foreach ($siswa_list as $s): ?>
                            <tr>
                                <td data-label="No"><?= $no++ ?></td>
                                <td data-label="Nomor"><?= htmlspecialchars($s['Nomor_Siswa']) ?></td>
                                <td data-label="Nama"><?= htmlspecialchars($s['Nama_Siswa']) ?></td>
                                <td data-label="Aksi">
                                    <button class="btn btn-sm btn-edit" onclick='editSiswa(<?= json_encode($s) ?>)'>Edit</button>
                                    <button class="btn btn-sm btn-delete" onclick="confirmHapus(<?= $s['id'] ?>, '<?= htmlspecialchars($s['Nama_Siswa'], ENT_QUOTES) ?>')">Hapus</button>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    <?php else: ?>
                        <tr><td colspan="4" class="no-data">Belum ada data siswa</td></tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </main>

    <div class="modal-overlay" id="modalTambah">
        <div class="modal">
            <form method="POST">
                <input type="hidden" name="action" value="tambah">
                <div class="modal-header">
                    <h3>Tambah Siswa</h3>
                    <button type="button" class="modal-close" onclick="closeModal('modalTambah')">&times;</button>
                </div>
                <div class="modal-body">
                    <div class="form-group">
                        <label for="nomor_siswa">Nomor Siswa</label>
                        <input type="number" id="nomor_siswa" name="nomor_siswa" placeholder="Masukkan nomor siswa" required>
                    </div>
                    <div class="form-group">
                        <label for="nama_siswa">Nama Siswa</label>
                        <input type="text" id="nama_siswa" name="nama_siswa" placeholder="Masukkan nama siswa" required>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-cancel" onclick="closeModal('modalTambah')">Batal</button>
                    <button type="submit" class="btn btn-submit">Simpan</button>
                </div>
            </form>
        </div>
    </div>

    <div class="modal-overlay" id="modalEdit">
        <div class="modal">
            <form method="POST">
                <input type="hidden" name="action" value="edit">
                <input type="hidden" name="id" id="edit_id">
                <div class="modal-header">
                    <h3>Edit Siswa</h3>
                    <button type="button" class="modal-close" onclick="closeModal('modalEdit')">&times;</button>
                </div>
                <div class="modal-body">
                    <div class="form-group">
                        <label for="edit_nomor_siswa">Nomor Siswa</label>
                        <input type="number" id="edit_nomor_siswa" name="nomor_siswa" required>
                    </div>
                    <div class="form-group">
                        <label for="edit_nama_siswa">Nama Siswa</label>
                        <input type="text" id="edit_nama_siswa" name="nama_siswa" required>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-cancel" onclick="closeModal('modalEdit')">Batal</button>
                    <button type="submit" class="btn btn-submit">Simpan</button>
                </div>
            </form>
        </div>
    </div>

    <form method="POST" id="formHapus" style="display:none">
        <input type="hidden" name="action" value="hapus">
        <input type="hidden" name="id" id="hapus_id">
    </form>

    <script>
        function editSiswa(siswa) {
            document.getElementById('edit_id').value = siswa.id;
            document.getElementById('edit_nomor_siswa').value = siswa.Nomor_Siswa;
            document.getElementById('edit_nama_siswa').value = siswa.Nama_Siswa;
            openModal('modalEdit');
        }
    </script>
</body>
</html>
