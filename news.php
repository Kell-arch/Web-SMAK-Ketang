<?php

$page_title = "Berita";
include 'header.php';
include 'navigation/navbar.php';

$conn = mysqli_connect("localhost", "root", "", "smak_ketang");

if (!$conn) {
    die("Koneksi database gagal: " . mysqli_connect_error());
}

$result = mysqli_query($conn, "SELECT * FROM pengumuman ORDER BY tanggal DESC, id DESC");
$berita_list = [];
while ($row = mysqli_fetch_assoc($result)) {
    $pid = (int)$row['id'];
    $q_img = mysqli_query($conn, "SELECT nama_file FROM pengumuman_gambar WHERE pengumuman_id = $pid ORDER BY urutan ASC");
    $row['gambar_list'] = [];
    while ($img = mysqli_fetch_assoc($q_img)) {
        $row['gambar_list'][] = $img['nama_file'];
    }
    $berita_list[] = $row;
}
mysqli_close($conn);
?>

<main class="news fade-up">
    <h1 class="news-judul">Berita & Pengumuman</h1>

    <?php if (count($berita_list) > 0): ?>
        <div class="news-grid">
            <?php foreach ($berita_list as $b): $jml = count($b['gambar_list']); ?>
                <div class="news-card">
                    <div class="news-card-gambar">
                        <?php if ($jml > 0): ?>
                            <img src="assets/uploads/pengumuman/<?= htmlspecialchars($b['gambar_list'][0]) ?>" alt="<?= htmlspecialchars($b['judul']) ?>">
                            <?php if ($jml > 1): ?>
                                <span class="news-card-count">+<?= $jml - 1 ?></span>
                            <?php endif; ?>
                        <?php else: ?>
                            <div class="news-card-placeholder">Tidak ada gambar</div>
                        <?php endif; ?>
                    </div>
                    <div class="news-card-body">
                        <div class="news-card-header">
                            <h3><?= htmlspecialchars($b['judul']) ?></h3>
                            <span class="news-card-tanggal"><?= htmlspecialchars($b['tanggal']) ?></span>
                        </div>
                        <p class="news-card-isi"><?= nl2br(htmlspecialchars(mb_substr($b['isi'], 0, 200))) ?><?= strlen($b['isi']) > 200 ? '...' : '' ?></p>
                        <div class="news-card-footer">
                            <?php if ($b['penulis']): ?>
                                <span class="news-card-penulis">Oleh: <?= htmlspecialchars($b['penulis']) ?></span>
                            <?php endif; ?>
                            <button class="news-card-lihat" onclick='openPengumuman(<?= json_encode($b) ?>)'>Baca selengkapnya</button>
                        </div>
                    </div>
                </div>
            <?php endforeach; ?>
        </div>
    <?php else: ?>
        <div class="news-kosong">Belum ada berita atau pengumuman</div>
    <?php endif; ?>
</main>

<div class="pengumuman-modal-overlay" id="pengumumanModal">
    <div class="pengumuman-modal">
        <button class="pengumuman-modal-close" onclick="closePengumuman()">&times;</button>
        <div id="pengumumanModalContent"></div>
    </div>
</div>

<?php
include 'navigation/footer.php';
?>
