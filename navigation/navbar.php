<nav>
    <div class="logo">
        <!-- Menggunakan variabel base_url agar path selalu benar -->
        <img src="<?php echo $base_url; ?>/assets/img/logo-sekolah.png" alt="Logo Sekolah SMAK St. Stefanus Ketang">
        <p>SMAK St. Stefanus Ketang</p>
    </div>

    <button class="hamburger" id="hamburger" aria-label="Toggle menu">
        <span></span>
        <span></span>
        <span></span>
    </button>
    <ul id="nav-menu" role="navigation">
        <li><a href="<?php echo $base_url; ?>/index.php">Beranda</a></li>
        <li><a href="<?php echo $base_url; ?>/about.php">Tentang</a></li>
        <li><a href="<?php echo $base_url; ?>/galeri.php">Galeri</a></li>
        <li><a href="<?php echo $base_url; ?>/news.php">Berita</a></li>
        <li><a href="<?php echo $base_url; ?>/contact.php">Kontak</a></li>
    </ul>
</nav>