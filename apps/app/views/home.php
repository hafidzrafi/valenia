<?php
/**
 * Home page (VALENIA-29) — standalone full page served at "/".
 *
 * IMPORTANT: this is a complete <html> document, not a content partial. Render
 * it via render(app_path('app/views/home.php'), [...]) from the route. Do NOT use
 * view('home'), which would wrap it in layout.php and emit two <html> documents.
 *
 * @var string $title
 */
$heroImage = ('/images/landingpage.jpg');
$heroTitle = ("Layanan Poliklinik");
$heroDesc = ("anjay YAWAWI LALILULELOOOOO~");
include __DIR__ . '/../components/header.php';
include __DIR__ . '/../components/Big-Hero-Card_LandingPageOnly.php'
?>


