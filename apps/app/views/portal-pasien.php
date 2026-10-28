<?php

/**
 * Home page (VALENIA-29) — standalone full page served at "/".
 *
 * IMPORTANT: this is a complete <html> document, not a content partial. Render
 * it via render(app_path('app/views/home.php'), [...]) from the route. Do NOT use
 * view('home'), which would wrap it in layout.php and emit two <html> documents.
 *
 */

$heroform = $_SESSION['heroform'] ?? "Default";
$no_antrean = $no_antrean ?? 'P-11';
include __DIR__ . '/../components/background-img.php';
ob_start();

$heroImage = ('/images/portalpasien.webp');
$heroTitle = ("Portal Pasien");
$heroDesc = ("Pantau status antrean dan riwayat kunjungan Poliklinik VALENIA");
$showCta = false;
?>

<?php include __DIR__ . '/../components/header.php';  ?>
<?php include __DIR__ . '/../components/big-hero-card-landing.php'; ?>

<body>
    <div class="w-full h-256">

    </div>
</body>

<?php
$content = ob_get_clean();
include __DIR__ . '/../views/layout.php';
?>