<?php

/**
 * 
 * @var array $location
 * @var array $call_centers
 * 
 * 
 * 
 *
 * 
 */
$heroImage = '/images/kontak.jpg';
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
            class="-translate-y-25 w-205 min-h-120 h-auto rounded-4xl bg-gradient-to-br from-white/80 to-white/60 backdrop-blur-[10px] overflow-hidden"
            style="box-shadow: -10px 15px 10px 0 rgba(0,0,0,0.1);">

            <div
                class="relative h-[400px] w-full"
                x-data
                @mouseenter="$dispatch('cursor-hide')"
                @mouseleave="$dispatch('cursor-show')">
                <iframe
                    src="<?= htmlspecialchars($location['embed_url']) ?>"
                    class="h-full w-full border-0"
                    allowfullscreen
                    loading="lazy"
                    referrerpolicy="no-referrer-when-downgrade"></iframe>
            </div>


            <div class="grid grid-cols-1 gap-6 px-8 py-6  md:grid-cols-3">
                <?php foreach ($call_centers as $cc): ?>
                    <div class="flex items-center justify-center gap-4">
                        <i class="ph ph-phone-call text-4xl text-teal-700"></i>
                        <div>
                            <p class="text-sm font-medium text-teal-700"><?= htmlspecialchars($cc['label'], ENT_QUOTES, 'UTF-8') ?></p>
                            <p class="text-xs tracking-wider text-slate-500"><?= htmlspecialchars($cc['number'], ENT_QUOTES, 'UTF-8') ?></p>
                        </div>
                    </div>
                <?php endforeach; ?>
            </div>
        </div>
    </div>
</div>



<?php include __DIR__ . '/../components/footer.php'; ?>