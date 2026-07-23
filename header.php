<?php
// Konfigurasi Base URL
// Biarkan kosong ('') jika project ada di root (misal: htdocs/).
// Jika nanti ditaruh di subfolder (misal: htdocs/sekolah/), ubah menjadi '/sekolah'
$base_url = '/porto';
?>
<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <!-- Judul halaman dinamis (bisa diubah di setiap file halaman) -->
    <title>SMAK St. Stefanus Ketang - <?php echo $page_title ?? 'Beranda'; ?></title>
    <meta name="description" content="Website resmi SMAK St. Stefanus Ketang.">

    <!-- Favicon dipindahkan ke sini (tempat yang seharusnya) -->
    <link rel="shortcut icon" href="<?php echo $base_url; ?>/assets/img/logo-sekolah.ico" type="image/x-icon">

    <!-- Pemanggilan CSS -->
    <link rel="stylesheet" href="<?php echo $base_url; ?>/style.css">
</head>

<body>