<?php

/**
 * Home page (VALENIA-29) — standalone full page served at "/".
 * @var array $location
 * IMPORTANT: this is a complete <html> document, not a content partial. Render
 * it via render(app_path('app/views/home.php'), [...]) from the route. Do NOT use
 * view('home'), which would wrap it in layout.php and emit two <html> documents.
 *
 * 
 */
$heroImage = '/images/landingpage.jpg';
$heroTitle = 'Kontak Poliklinik';
$heroDesc = 'Layanan Poliklinik Politeknik Negeri Malang';
$page = 'nonlanding';
$showCta = false;

?>

<?php include __DIR__ . '/../components/cursor.php'; ?>
<?php include __DIR__ . '/../components/header.php'; ?>
<?php include __DIR__ . '/../components/big-hero-card-landing.php'; ?>


<div class="relative bg-cover bg-center bg-no-repeat" style="background-image: url('/images/background.png');">

    <div class="flex items-center justify-center">
        <div
            class="-translate-y-25 w-205 min-h-130 h-auto rounded-4xl bg-gradient-to-br from-white/80 to-white/60 backdrop-blur-[10px] overflow-hidden"
            style="box-shadow: -10px 15px 10px 0 rgba(0,0,0,0.1);">

            <iframe
                src="<?= $location['embed_url'] ?>"
                class="w-full h-full min-h-[400px] border-0"
                allowfullscreen=""
                loading="lazy"
                referrerpolicy="no-referrer-when-downgrade">
            </iframe>

        </div>
    </div>


    <div class="w-full -h-5">

    </div>

</div>



<?php include __DIR__ . '/../components/footer.php'; ?>