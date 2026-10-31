<?php
$heroTitle = $heroTitle ?? "Lorem adalah sahabat Pak Ipsum";
$heroDesc  = $heroDesc ?? "Deskripsi default website.";
$heroImage = $heroImage ?? "../assets/img/default-img.jpg";
$ctaPrimaryText = $ctaPrimaryText ?? "Mulai Sekarang";
$ctaPrimaryLink = $ctaPrimaryLink ?? "#";
$ctaSecondaryText = $ctaSecondaryText ?? "Pelajari Lebih Lanjut";
$ctaSecondaryLink = $ctaSecondaryLink ?? "#";
$showCta = $showCta ?? true;
$page = $page ?? "";
?>

<?php if ($page == "nonlanding"): ?>
    <section class="relative  md:h-[80vh] w-full overflow-hidden flex items-center justify-center border-b-3 border-gray-400 ">
        <!-- Background Image -->
        <img src="<?= $heroImage; ?>" alt="Hero Image" class="absolute inset-0 w-full h-full object-cover">

        <!-- Overlay Gelap  -->
        <div class="absolute inset-0 bg-black/50"></div>

        <!-- Konten Teks -->
        <div class="relative z-10 container mx-auto px-6 text-center text-white font-['SF_Pro_Display']">
            <h1 class="text-[96px] md:text-6xl lg:text-7xl font-extrabold leading-tight mb-6">
                <?= $heroTitle; ?>
            </h1>
            <p class="text-lg md:text-2xl max-w-3xl mx-auto mb-10 opacity-90">
                <?= $heroDesc; ?>
            </p>

            <!-- Tombol CTA  -->
            <?php if ($showCta): ?>
                <div class="flex flex-col sm:flex-row gap-4 justify-center">
                    <?php if ($ctaPrimaryText): ?>
                        <a href="<?= $ctaPrimaryLink; ?>" class="bg-teal-500 smooth-corners-lg  hover:bg-teal-700 text-white px-8 py-4 rounded-3xl font-bold text-lg transition-all transform hover:scale-105">
                            <?= $ctaPrimaryText; ?>
                        </a>
                    <?php endif; ?>

                    <?php if ($ctaSecondaryText): ?>
                        <a href="<?= $ctaSecondaryLink; ?>" class="bg-white/10 smooth-corners-lg hover:bg-white/20 backdrop-blur-md text-white border border-white/30 px-8 py-4 rounded-3xl font-bold text-lg transition-all">
                            <?= $ctaSecondaryText; ?>
                        </a>
                    <?php endif; ?>
                </div>
            <?php endif; ?>
        </div>
    </section>


<?php else: ?>

    <section class="relative  md:h-screen w-full overflow-hidden flex items-center justify-center border-b-2 border-gray-400 ">
        <!-- Background Image -->
        <img src="<?= $heroImage; ?>" alt="Hero Image" class="absolute inset-0 w-full h-full object-cover">

        <!-- Overlay Gelap  -->
        <div class="absolute inset-0 bg-black/50"></div>

        <!-- Konten Teks -->
        <div class="relative z-10 container mx-auto px-6 text-center text-white font-['SF_Pro_Display']">
            <h1 class="text-[96px] md:text-6xl lg:text-7xl font-extrabold leading-tight mb-6">
                <?= $heroTitle; ?>
            </h1>
            <p class="text-lg md:text-2xl max-w-3xl mx-auto mb-10 opacity-90">
                <?= $heroDesc; ?>
            </p>

            <!-- Tombol CTA  -->
            <?php if ($showCta): ?>
                <div class="flex flex-col sm:flex-row gap-4 justify-center">
                    <?php if ($ctaPrimaryText): ?>
                        <a href="<?= $ctaPrimaryLink; ?>" class="bg-teal-500 smooth-corners-lg  hover:bg-teal-700 text-white px-8 py-4 rounded-3xl font-bold text-lg transition-all transform hover:scale-105">
                            <?= $ctaPrimaryText; ?>
                        </a>
                    <?php endif; ?>

                    <?php if ($ctaSecondaryText): ?>
                        <a href="<?= $ctaSecondaryLink; ?>" class="bg-white/10 smooth-corners-lg hover:bg-white/20 backdrop-blur-md text-white border border-white/30 px-8 py-4 rounded-3xl font-bold text-lg transition-all">
                            <?= $ctaSecondaryText; ?>
                        </a>
                    <?php endif; ?>
                </div>
            <?php endif; ?>
        </div>
    </section>
<?php endif ?>