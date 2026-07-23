<?php
session_start();
require_once __DIR__ . '/../data/koneksi.php';

if (!isset($_SESSION['admin'])) {
    header('Location: login_admin.php');
    exit;
}

$upload_dir = __DIR__ . '/../assets/uploads/dokumentasi/';

$flash = '';
$flash_type = '';

if (isset($_SESSION['flash'])) {
    $flash = $_SESSION['flash']['message'];
    $flash_type = $_SESSION['flash']['type'];
    unset($_SESSION['flash']);
}

function validasi_gambar_dok($file) {
    $allowed = ['jpg', 'jpeg', 'png', 'webp'];
    $ext = strtolower(pathinfo($file['name'], PATHINFO_EXTENSION));
    $max_size = 2 * 1024 * 1024;
    return in_array($ext, $allowed) && $file['size'] <= $max_size;
}

function simpan_gambar_dok($file) {
    global $upload_dir;
    $ext = strtolower(pathinfo($file['name'], PATHINFO_EXTENSION));
    $nama = time() . '_' . bin2hex(random_bytes(4)) . '.' . $ext;
    move_uploaded_file($file['tmp_name'], $upload_dir . $nama);
    return $nama;
}

function hapus_file_gambar_dok($nama) {
    global $upload_dir;
    if ($nama && file_exists($upload_dir . $nama)) {
        unlink($upload_dir . $nama);
    }
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $action = $_POST['action'] ?? '';

    if ($action === 'tambah') {
        $judul = trim($_POST['judul'] ?? '');
        $deskripsi = trim($_POST['deskripsi'] ?? '');

        if ($judul !== '') {
            $stmt = mysqli_prepare($koneksi, "INSERT INTO dokumentasi (judul, deskripsi) VALUES (?, ?)");
            mysqli_stmt_bind_param($stmt, 'ss', $judul, $deskripsi);
            if (mysqli_stmt_execute($stmt)) {
                $dok_id = mysqli_insert_id($koneksi);
                $urutan = 0;
                $gagal = false;

                if (isset($_FILES['gambar'])) {
                    $files = $_FILES['gambar'];
                    $jumlah = is_array($files['name']) ? count($files['name']) : 1;

                    for ($i = 0; $i < $jumlah; $i++) {
                        $file = [
                            'name' => is_array($files['name']) ? $files['name'][$i] : $files['name'],
                            'error' => is_array($files['error']) ? $files['error'][$i] : $files['error'],
                            'tmp_name' => is_array($files['tmp_name']) ? $files['tmp_name'][$i] : $files['tmp_name'],
                            'size' => is_array($files['size']) ? $files['size'][$i] : $files['size'],
                        ];
                        if ($file['error'] !== UPLOAD_ERR_OK) continue;
                        if (!validasi_gambar_dok($file)) { $gagal = true; continue; }

                        $nama = simpan_gambar_dok($file);
                        $stmt_img = mysqli_prepare($koneksi, "INSERT INTO dokumentasi_gambar (dokumentasi_id, nama_file, urutan) VALUES (?, ?, ?)");
                        mysqli_stmt_bind_param($stmt_img, 'isi', $dok_id, $nama, $urutan);
                        mysqli_stmt_execute($stmt_img);
                        $urutan++;
                    }
                }

                if ($gagal) {
                    $_SESSION['flash'] = ['message' => 'Dokumentasi berhasil ditambahkan, tetapi beberapa gambar tidak valid.', 'type' => 'success'];
                } else {
                    $_SESSION['flash'] = ['message' => 'Dokumentasi berhasil ditambahkan.', 'type' => 'success'];
                }
            } else {
                $_SESSION['flash'] = ['message' => 'Gagal menambahkan dokumentasi.', 'type' => 'error'];
            }
        } else {
            $_SESSION['flash'] = ['message' => 'Judul dokumentasi harus diisi.', 'type' => 'error'];
        }
        header('Location: dokumentasi.php');
        exit;
    }

    if ($action === 'edit') {
        $id = (int)($_POST['id'] ?? 0);
        $judul = trim($_POST['judul'] ?? '');
        $deskripsi = trim($_POST['deskripsi'] ?? '');

        if ($id > 0 && $judul !== '') {
            if (isset($_POST['hapus_gambar_id']) && is_array($_POST['hapus_gambar_id'])) {
                foreach ($_POST['hapus_gambar_id'] as $gid) {
                    $gid = (int)$gid;
                    $q = mysqli_query($koneksi, "SELECT nama_file FROM dokumentasi_gambar WHERE id = $gid AND dokumentasi_id = $id");
                    $row = mysqli_fetch_assoc($q);
                    if ($row) {
                        hapus_file_gambar_dok($row['nama_file']);
                        mysqli_query($koneksi, "DELETE FROM dokumentasi_gambar WHERE id = $gid");
                    }
                }
            }

            if (isset($_FILES['gambar_baru'])) {
                $files = $_FILES['gambar_baru'];
                $jumlah = is_array($files['name']) ? count($files['name']) : 1;

                $q_max = mysqli_query($koneksi, "SELECT COALESCE(MAX(urutan), -1) + 1 as next FROM dokumentasi_gambar WHERE dokumentasi_id = $id");
                $row_max = mysqli_fetch_assoc($q_max);
                $urutan = (int)$row_max['next'];

                for ($i = 0; $i < $jumlah; $i++) {
                    $file = [
                        'name' => is_array($files['name']) ? $files['name'][$i] : $files['name'],
                        'error' => is_array($files['error']) ? $files['error'][$i] : $files['error'],
                        'tmp_name' => is_array($files['tmp_name']) ? $files['tmp_name'][$i] : $files['tmp_name'],
                        'size' => is_array($files['size']) ? $files['size'][$i] : $files['size'],
                    ];
                    if ($file['error'] !== UPLOAD_ERR_OK) continue;
                    if (!validasi_gambar_dok($file)) {
                        $_SESSION['flash'] = ['message' => 'Beberapa gambar tidak valid.', 'type' => 'error'];
                        header('Location: dokumentasi.php');
                        exit;
                    }
                    $nama = simpan_gambar_dok($file);
                    $stmt_img = mysqli_prepare($koneksi, "INSERT INTO dokumentasi_gambar (dokumentasi_id, nama_file, urutan) VALUES (?, ?, ?)");
                    mysqli_stmt_bind_param($stmt_img, 'isi', $id, $nama, $urutan);
                    mysqli_stmt_execute($stmt_img);
                    $urutan++;
                }
            }

            $stmt = mysqli_prepare($koneksi, "UPDATE dokumentasi SET judul = ?, deskripsi = ? WHERE id = ?");
            mysqli_stmt_bind_param($stmt, 'ssi', $judul, $deskripsi, $id);
            mysqli_stmt_execute($stmt);

            $_SESSION['flash'] = ['message' => 'Dokumentasi berhasil diubah.', 'type' => 'success'];
        } else {
            $_SESSION['flash'] = ['message' => 'Data tidak valid.', 'type' => 'error'];
        }
        header('Location: dokumentasi.php');
        exit;
    }

    if ($action === 'hapus') {
        $id = (int)($_POST['id'] ?? 0);
        if ($id > 0) {
            $q = mysqli_query($koneksi, "SELECT nama_file FROM dokumentasi_gambar WHERE dokumentasi_id = $id");
            while ($row = mysqli_fetch_assoc($q)) {
                hapus_file_gambar_dok($row['nama_file']);
            }

            $stmt = mysqli_prepare($koneksi, "DELETE FROM dokumentasi WHERE id = ?");
            mysqli_stmt_bind_param($stmt, 'i', $id);
            mysqli_stmt_execute($stmt);
            $_SESSION['flash'] = ['message' => 'Dokumentasi berhasil dihapus.', 'type' => 'success'];
        }
        header('Location: dokumentasi.php');
        exit;
    }
}

$result = mysqli_query($koneksi, "SELECT * FROM dokumentasi ORDER BY tanggal DESC, id DESC");
$dok_list = [];
while ($row = mysqli_fetch_assoc($result)) {
    $did = $row['id'];
    $q_img = mysqli_query($koneksi, "SELECT id, nama_file FROM dokumentasi_gambar WHERE dokumentasi_id = $did ORDER BY urutan ASC");
    $row['gambar_list'] = [];
    while ($img = mysqli_fetch_assoc($q_img)) {
        $row['gambar_list'][] = $img;
    }
    $dok_list[] = $row;
}
$total = count($dok_list);
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dokumentasi - Admin SMAK St. Stefanus Ketang</title>
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
            <a href="dokumentasi.php" class="active">
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
                    <h1>Dokumentasi</h1>
                    <p>Total <?= $total ?> dokumentasi</p>
                </div>
            </div>
            <button class="btn btn-primary" onclick="openModal('modalTambah')">+ Tambah Dokumentasi</button>
        </div>

        <?php if ($flash): ?>
            <div class="flash-message flash-<?= $flash_type ?>"><?= htmlspecialchars($flash) ?></div>
        <?php endif; ?>

        <div class="table-wrapper">
            <table>
                <thead>
                    <tr>
                        <th>No</th>
                        <th>Gambar</th>
                        <th>Judul</th>
                        <th>Tanggal</th>
                        <th>Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if (count($dok_list) > 0): $no = 1; ?>
                        <?php foreach ($dok_list as $d): $jml_gambar = count($d['gambar_list']); ?>
                            <tr>
                                <td data-label="No"><?= $no++ ?></td>
                                <td data-label="Gambar">
                                    <?php if ($jml_gambar > 0): ?>
                                        <img src="../assets/uploads/dokumentasi/<?= htmlspecialchars($d['gambar_list'][0]['nama_file']) ?>" class="pengumuman-thumb" alt="Gambar">
                                        <?php if ($jml_gambar > 1): ?><span style="display:block;font-size:11px;color:rgba(240,248,255,0.4);margin-top:4px;">+<?= $jml_gambar - 1 ?> lagi</span><?php endif; ?>
                                    <?php else: ?>
                                        <span class="pengumuman-thumb kosong">Tidak ada</span>
                                    <?php endif; ?>
                                </td>
                                <td data-label="Judul"><?= htmlspecialchars($d['judul']) ?></td>
                                <td data-label="Tanggal"><?= htmlspecialchars($d['tanggal']) ?></td>
                                <td data-label="Aksi">
                                    <button class="btn btn-sm btn-edit" onclick='editDokumentasi(<?= json_encode($d) ?>)'>Edit</button>
                                    <button class="btn btn-sm btn-delete" onclick="confirmHapus(<?= $d['id'] ?>, '<?= htmlspecialchars($d['judul'], ENT_QUOTES) ?>')">Hapus</button>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    <?php else: ?>
                        <tr><td colspan="5" class="no-data">Belum ada dokumentasi</td></tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </main>

    <div class="modal-overlay" id="modalTambah">
        <div class="modal">
            <form method="POST" enctype="multipart/form-data">
                <input type="hidden" name="action" value="tambah">
                <div class="modal-header">
                    <h3>Tambah Dokumentasi</h3>
                    <button type="button" class="modal-close" onclick="closeModal('modalTambah')">&times;</button>
                </div>
                <div class="modal-body">
                    <div class="form-group">
                        <label for="judul">Judul</label>
                        <input type="text" id="judul" name="judul" placeholder="Masukkan judul dokumentasi" required>
                    </div>
                    <div class="form-group">
                        <label for="deskripsi">Deskripsi <span style="color:rgba(240,248,255,0.35);font-weight:400">(opsional)</span></label>
                        <textarea id="deskripsi" name="deskripsi" placeholder="Tulis deskripsi dokumentasi..."></textarea>
                    </div>
                    <div class="form-group">
                        <label for="gambar">Gambar <span style="color:rgba(240,248,255,0.35);font-weight:400">(bisa pilih banyak, JPG/PNG/WEBP max 2MB)</span></label>
                        <input type="file" id="gambar" name="gambar[]" accept="image/jpeg,image/png,image/webp" multiple>
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
            <form method="POST" enctype="multipart/form-data">
                <input type="hidden" name="action" value="edit">
                <input type="hidden" name="id" id="edit_id">
                <div class="modal-header">
                    <h3>Edit Dokumentasi</h3>
                    <button type="button" class="modal-close" onclick="closeModal('modalEdit')">&times;</button>
                </div>
                <div class="modal-body">
                    <div class="form-group">
                        <label for="edit_judul">Judul</label>
                        <input type="text" id="edit_judul" name="judul" required>
                    </div>
                    <div class="form-group">
                        <label for="edit_deskripsi">Deskripsi</label>
                        <textarea id="edit_deskripsi" name="deskripsi"></textarea>
                    </div>
                    <div class="form-group" id="edit_gambar_list_wrapper" style="display:none">
                        <label>Gambar Saat Ini</label>
                        <div id="edit_gambar_list" style="display:flex;flex-wrap:wrap;gap:10px;margin-top:6px;"></div>
                    </div>
                    <div class="form-group">
                        <label for="edit_gambar_baru">Tambah Gambar Baru <span style="color:rgba(240,248,255,0.35);font-weight:400">(opsional, bisa pilih banyak)</span></label>
                        <input type="file" id="edit_gambar_baru" name="gambar_baru[]" accept="image/jpeg,image/png,image/webp" multiple>
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
        function editDokumentasi(d) {
            document.getElementById('edit_id').value = d.id;
            document.getElementById('edit_judul').value = d.judul;
            document.getElementById('edit_deskripsi').value = d.deskripsi || '';

            var wrapper = document.getElementById('edit_gambar_list_wrapper');
            var container = document.getElementById('edit_gambar_list');
            container.innerHTML = '';

            if (d.gambar_list && d.gambar_list.length > 0) {
                d.gambar_list.forEach(function(img) {
                    var div = document.createElement('div');
                    div.className = 'edit-gambar-item';
                    div.innerHTML = '<img src="../assets/uploads/dokumentasi/' + img.nama_file + '">' +
                        '<label><input type="checkbox" name="hapus_gambar_id[]" value="' + img.id + '"> Hapus</label>';
                    container.appendChild(div);
                });
                wrapper.style.display = 'block';
            } else {
                wrapper.style.display = 'none';
            }

            openModal('modalEdit');
        }
    </script>
</body>
</html>
