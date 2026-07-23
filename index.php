<?php

$page_title = "Beranda";


include 'header.php';
include 'navigation/navbar.php';


$conn = mysqli_connect("localhost", "root", "", "smak_ketang");


if (!$conn) {
    die("Koneksi database gagal: " . mysqli_connect_error());
}
?>


<main class="beranda-utama fade-up">
    <section class="beranda" id="beranda">
        <h1>Selamat Datang di SMAK St. Stefanus Ketang</h1>
        <em class="moto">"Cerdas Berkarakter"</em>

        <p class="deskripsi-singkat">
            SMAK St. Stefanus Ketang adalah sebuah sekolah menengah Agama Katolik yang berada di bawah naungan Kementrian Agama Republik Indonesia.
            SMAK St. Stefanus Ketang terletak di desa ketang Kecamatan Lelak, Kabupaten, Manggarai, Provinsi Nusa Tenggara Timur
        </p>

        <h2 class="selengkapnya">
            <a href="#data-sekolah">Selengkapnya</a>
        </h2>
    </section>

    <section id="data-sekolah">
        <div class="data-sekolah" id="data-sekolah">

            <p>
                Jumlah Mata Pelajaran
                <br>
                <data>
                    <?php
                    $result = mysqli_query($conn, "SELECT COUNT(*) as total FROM mapel");
                    $row = mysqli_fetch_assoc($result);
                    echo $row['total'];
                    ?>
                </data>
            </p>

            <!-- Jumlah Siswa -->
            <p>
                Jumlah Siswa
                <br>
                <data>
                    <?php
                    $result = mysqli_query($conn, "SELECT COUNT(*) as total FROM siswa");
                    $row = mysqli_fetch_assoc($result);
                    echo $row['total'] . " Orang";
                    ?>
                </data>
            </p>


            <!-- Jumlah Kelas -->
            <p>
                Jumlah Kelas
                <br>
                <data>
                    <?php

                    $result = @mysqli_query($conn, "SELECT COUNT(*) as total FROM kelas");
                    if ($result) {
                        $row = mysqli_fetch_assoc($result);
                        echo $row['total'] . " Ruangan";
                    } else {
                        echo "0 Ruangan";
                    }
                    ?>
                </data>
            </p>


            <p>
                Jumlah Guru
                <br>
                <data>
                    <?php
                    $result = @mysqli_query($conn, "SELECT COUNT(*) as total FROM guru");
                    if ($result) {
                        $row = mysqli_fetch_assoc($result);
                        echo $row['total'] . " Orang";
                    } else {
                        echo "0 Orang";
                    }
                    ?>
                </data>
            </p>


            <p>
                Jumlah Ekstrakurikuler
                <br>
                <data>
                    <?php
                    $result = @mysqli_query($conn, "SELECT COUNT(*) as total FROM ekstrakurikuler");
                    if ($result) {
                        $row = mysqli_fetch_assoc($result);
                        echo $row['total'] . " Kegiatan";
                    } else {
                        echo "0 Kegiatan";
                    }
                    ?>
                </data>
            </p>

        </div>
    </section>

    <section class="pengumuman-section" id="pengumuman">
        <h2 class="pengumuman-judul">Pengumuman</h2>
        <div class="pengumuman-grid">
            <?php
            $result_peng = mysqli_query($conn, "SELECT id, judul, isi, tanggal, penulis FROM pengumuman ORDER BY tanggal DESC, id DESC LIMIT 5");
            if ($result_peng && mysqli_num_rows($result_peng) > 0):
                while ($p = mysqli_fetch_assoc($result_peng)):
                    $pid = (int)$p['id'];
                    $q_img = mysqli_query($conn, "SELECT nama_file FROM pengumuman_gambar WHERE pengumuman_id = $pid ORDER BY urutan ASC");
                    $gambar_list = [];
                    while ($img = mysqli_fetch_assoc($q_img)) {
                        $gambar_list[] = $img['nama_file'];
                    }
                    $p['gambar_list'] = $gambar_list;
                    $isi_pendek = strlen($p['isi']) > 100 ? substr($p['isi'], 0, 100) . '...' : $p['isi'];
                    $jml_gbr = count($gambar_list);
            ?>
                    <div class="pengumuman-card">
                        <div class="pengumuman-carousel" data-slide="0">
                            <div class="pengumuman-carousel-track">
                                <?php if ($jml_gbr > 0): ?>
                                    <?php foreach ($gambar_list as $gbr): ?>
                                        <div class="pengumuman-carousel-slide">
                                            <img src="assets/uploads/pengumuman/<?= htmlspecialchars($gbr) ?>" alt="<?= htmlspecialchars($p['judul']) ?>">
                                        </div>
                                    <?php endforeach; ?>
                                <?php else: ?>
                                    <div class="pengumuman-carousel-slide">
                                        <div class="pengumuman-carousel-placeholder">Tidak ada gambar</div>
                                    </div>
                                <?php endif; ?>
                            </div>
                            <?php if ($jml_gbr > 1): ?>
                                <div class="pengumuman-carousel-dots">
                                    <?php for ($d = 0; $d < $jml_gbr; $d++): ?>
                                        <span class="<?= $d === 0 ? 'active' : '' ?>" data-index="<?= $d ?>"></span>
                                    <?php endfor; ?>
                                </div>
                            <?php endif; ?>
                        </div>
                        <div class="pengumuman-body">
                            <div class="pengumuman-header">
                                <h3><?= htmlspecialchars($p['judul']) ?></h3>
                                <span class="pengumuman-tanggal"><?= htmlspecialchars($p['tanggal']) ?></span>
                            </div>
                            <p class="pengumuman-isi"><?= nl2br(htmlspecialchars($isi_pendek)) ?></p>
                            <button class="pengumuman-lihat" onclick='openPengumuman(<?= json_encode($p) ?>)'>Lihat selengkapnya</button>
                        </div>
                    </div>
                <?php
                endwhile;
            else:
                ?>
                <div class="pengumuman-kosong">Belum ada pengumuman</div>
            <?php endif; ?>
        </div>
    </section>
</main>

<div class="pengumuman-modal-overlay" id="pengumumanModal">
    <div class="pengumuman-modal">
        <button class="pengumuman-modal-close" onclick="closePengumuman()">&times;</button>
        <div id="pengumumanModalContent"></div>
    </div>
</div>


<?php
mysqli_close($conn);


include 'navigation/footer.php';
?>