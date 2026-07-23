<?php

$page_title = "Galeri";
include 'header.php';
include 'navigation/navbar.php';

$conn = mysqli_connect("localhost", "root", "", "smak_ketang");

if (!$conn) {
    die("Koneksi database gagal: " . mysqli_connect_error());
}

$result = mysqli_query($conn, "SELECT * FROM dokumentasi ORDER BY tanggal DESC, id DESC");
$dok_list = [];
while ($row = mysqli_fetch_assoc($result)) {
    $did = (int)$row['id'];
    $q_img = mysqli_query($conn, "SELECT nama_file FROM dokumentasi_gambar WHERE dokumentasi_id = $did ORDER BY urutan ASC");
    $row['gambar_list'] = [];
    while ($img = mysqli_fetch_assoc($q_img)) {
        $row['gambar_list'][] = $img['nama_file'];
    }
    $dok_list[] = $row;
}
mysqli_close($conn);
?>

<main class="galeri fade-up">
    <h1 class="galeri-judul">Galeri Dokumentasi</h1>

    <?php if (count($dok_list) > 0): ?>
        <div class="galeri-grid">
            <?php foreach ($dok_list as $d): $jml = count($d['gambar_list']); ?>
                <div class="galeri-card" onclick='openGaleri(<?= json_encode($d) ?>)'>
                    <div class="galeri-card-gambar">
                        <?php if ($jml > 0): ?>
                            <img src="assets/uploads/dokumentasi/<?= htmlspecialchars($d['gambar_list'][0]) ?>" alt="<?= htmlspecialchars($d['judul']) ?>">
                            <?php if ($jml > 1): ?>
                                <span class="galeri-card-count">+<?= $jml - 1 ?></span>
                            <?php endif; ?>
                        <?php else: ?>
                            <div class="galeri-card-placeholder">Tidak ada gambar</div>
                        <?php endif; ?>
                    </div>
                    <div class="galeri-card-body">
                        <h3><?= htmlspecialchars($d['judul']) ?></h3>
                        <span class="galeri-card-tanggal"><?= htmlspecialchars($d['tanggal']) ?></span>
                    </div>
                </div>
            <?php endforeach; ?>
        </div>
    <?php else: ?>
        <div class="galeri-kosong">Belum ada dokumentasi</div>
    <?php endif; ?>
</main>

<div class="galeri-modal-overlay" id="galeriModal">
    <div class="galeri-modal">
        <button class="galeri-modal-close" onclick="closeGaleri()">&times;</button>
        <div id="galeriModalContent"></div>
    </div>
</div>



<?php
include 'navigation/footer.php';
?>
