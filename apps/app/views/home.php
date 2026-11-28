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
$heroImage = '/images/landingpage.jpg';
$heroTitle = 'Layanan Poliklinik';
$heroDesc = 'Politeknik Negeri Malang';
$ctaPrimaryText = $ctaPrimaryText ?? 'Mulai sekarang';
$ctaPrimaryLink = $ctaPrimaryLink ?? '#layanan-heading';
$ctaSecondaryText = $ctaSecondaryText ?? 'Pelajari lebih lanjut';
$ctaSecondaryLink = $ctaSecondaryLink ?? '/tentang-poliklinik';
$error = $error ?? null;

ob_start();
?>

<body>
    <div x-data class="relative bg-cover bg-center bg-no-repeat" style="background-image: url('/images/background.png');">
        <div class="absolute inset-y-0 inset-x-0 bg-teal-100/60" aria-hidden="true"></div>

        <?php include __DIR__.'/../components/header.php'; ?>
        <?php include __DIR__.'/../components/cursor.php'; ?>
        <?php if (null !== $error && '' !== $error) { ?>
            <div class="relative z-20 mx-auto mt-4 w-fit max-w-[90%] rounded-2xl bg-red-50 px-5 py-3 text-sm font-medium text-red-700 shadow-lg"
                role="alert">
                <?php echo e((string) $error); ?>
            </div>
        <?php } ?>
        <?php include __DIR__.'/../components/big-hero-card-landing.php'; ?>

        <svg viewBox="0 0 200 200" class="absolute w-50 opacity-30 m-10 left-30">
            <path fill="#14B8A6" d="M43.5,-78.1C54.7,-68.8,61.1,-53.9,64.1,-39.9C67,-26,66.6,-13,68.9,1.3C71.2,15.7,76.3,31.3,71.6,42.3C66.9,53.3,52.5,59.6,38.9,65C25.3,70.4,12.7,75,0.6,74C-11.5,73.1,-23.1,66.5,-32.3,58.5C-41.4,50.6,-48.3,41.2,-57.3,31.2C-66.2,21.2,-77.3,10.6,-78.5,-0.7C-79.8,-12,-71.1,-24,-62.5,-34.7C-54,-45.4,-45.5,-54.8,-35.1,-64.6C-24.7,-74.3,-12.3,-84.5,1.9,-87.7C16.1,-91,32.2,-87.4,43.5,-78.1Z" transform="translate(100 100)" />
        </svg>

        <svg viewBox="0 0 200 200" class="absolute w-80 opacity-30 right-15 mt-5">
            <path fill="#14B8A6" d="M33.1,-57.4C44.5,-50.7,56.3,-45.1,62.3,-35.7C68.2,-26.3,68.2,-13.1,68.2,0C68.2,13.2,68.3,26.3,65,40.3C61.7,54.3,55.1,69.1,43.7,74.3C32.3,79.5,16.2,75.1,2.8,70.2C-10.5,65.3,-21,59.9,-31.3,54C-41.6,48.2,-51.8,42,-57.9,32.9C-64,23.8,-66.1,11.9,-67.8,-0.9C-69.4,-13.8,-70.6,-27.6,-66.4,-40.2C-62.3,-52.7,-52.9,-64,-40.9,-70.3C-29,-76.6,-14.5,-78,-1.8,-74.8C10.9,-71.7,21.7,-64.1,33.1,-57.4Z" transform="translate(100 100)" />
        </svg>

        <div class="w-full h-135 relative overflow-hidden bg-teal-700/70">
            <div class="pointer-events-none absolute inset-0 text-teal-700/50" aria-hidden="true">

                <i class="ph ph-dna absolute top-[12%] left-[6%] text-7xl -rotate-12"></i>
                <i class="ph ph-stethoscope absolute top-[42%] left-[3%] text-6xl rotate-6"></i>
                <i class="ph ph-hospital absolute bottom-[8%] left-[8%] text-7xl -rotate-6"></i>
                <i class="ph ph-pill absolute bottom-[30%] left-[19%] text-5xl rotate-45"></i>

                <i class="ph ph-first-aid absolute top-[10%] right-[8%] text-9xl rotate-12"></i>
                <i class="ph ph-tooth absolute top-[48%] right-[4%] text-6xl -rotate-12"></i>
                <i class="ph ph-heartbeat absolute bottom-[10%] right-[10%] text-7xl rotate-6"></i>
                <i class="ph ph-chats-circle absolute bottom-[34%] right-[20%] text-5xl -rotate-6"></i>

                <i class="ph ph-plus absolute top-[28%] left-[26%] text-3xl"></i>
                <i class="ph ph-plus absolute top-[22%] right-[28%] text-4xl"></i>
                <i class="ph ph-thermometer absolute top-[8%] left-[34%] text-5xl rotate-12"></i>
                <i class="ph ph-syringe absolute bottom-[6%] right-[34%] text-5xl -rotate-45"></i>
            </div>
            <div
                x-data="{ shown: true }"
                x-init="
        const observer = new IntersectionObserver(([entry]) => {

            if (entry.isIntersecting && entry.boundingClientRect.top > 50) {
                shown = true;
            } else {
                if (entry.boundingClientRect.top > 0) {
                    shown = false;
                }
            }
        }, { threshold: 0.1 });

        observer.observe($el);
    "
                :class="shown ? 'opacity-100 translate-y-0' : 'opacity-0 translate-y-12'"
                class="flex flex-col justify-start items-center absolute top-50 gap-7.75 opacity-100 translate-y-0 transition-all duration-1000 ease-out">
                <p
                    class="self-stretch text-8xl font-bold text-center font-display-expanded tracking-wider text-white">
                    VALENIA
                </p>
                <p
                    class="grow-0 shrink-0 w-[60%] h-[78px] text-xl text-center text-white">
                    Verifikasi Antrean dan Layanan Navigasi Interaktif Aplikasi Poliklinik Politeknik Negeri
                    Malang. Memberikan kemudahan akses layanan kesehatan yang cepat, terintegrasi, dan efisien
                    bagi seluruh civitas akademika.
                </p>
            </div>
        </div>

        <svg viewBox="0 0 200 200" class="absolute w-40 opacity-25">
            <path fill="#14B8A6" d="M40.4,-74C48.1,-65.5,47.3,-46.2,51.9,-32C56.6,-17.9,66.7,-8.9,67,0.2C67.3,9.3,57.8,18.6,49.5,26.4C41.2,34.2,34.2,40.6,26.1,50.6C18.1,60.5,9,74.1,-2.1,77.7C-13.3,81.4,-26.5,75.1,-40.5,68.6C-54.5,62.1,-69.2,55.3,-72.7,43.8C-76.2,32.3,-68.5,16.2,-66.3,1.2C-64.2,-13.7,-67.7,-27.4,-62.7,-36.3C-57.7,-45.2,-44.3,-49.4,-32.5,-55.5C-20.6,-61.6,-10.3,-69.6,3,-74.8C16.3,-80,32.7,-82.4,40.4,-74Z" transform="translate(100 100)" />
        </svg>


        <svg viewBox="0 0 200 200" class="absolute translate-y-20 right-30 w-110 opacity-25">
            <path fill="#14B8A6" d="M45.9,-76.7C59.2,-71.9,69.5,-59,77.3,-44.8C85.1,-30.7,90.5,-15.3,90.3,-0.1C90.1,15.1,84.2,30.1,75.6,43C67.1,55.9,55.8,66.5,42.8,73.9C29.7,81.2,14.9,85.3,0.8,83.9C-13.2,82.5,-26.5,75.7,-37.5,67.1C-48.4,58.5,-57.1,48.3,-67,36.8C-76.9,25.3,-88,12.7,-90.7,-1.6C-93.4,-15.8,-87.7,-31.5,-78.7,-44.5C-69.7,-57.5,-57.3,-67.8,-43.7,-72.4C-30,-77.1,-15,-76.2,0.7,-77.3C16.3,-78.4,32.6,-81.6,45.9,-76.7Z" transform="translate(100 100)" />
        </svg>


        <div
            class="absolute w-84 shrink-0 z-10 right-60 -translate-y-20 transition-all transform hover:scale-105">
            <img src="/images/shade.webp" class="w-full h-auto" alt="mascot">
        </div>

        <div class="relative  w-full max-w-[85%] mx-auto  p-20 overflow-hidden flex flex-col md:flex-row items-center justify-between gap-15 mb-6">
            

            <div class="text-left   rounded-4xl">
                <h2 class="text-4xl font-bold tracking-tight text-teal-700 mb-3">
                    Berobat Cuma 3 Langkah
                </h2>
                <p class="text-lg   text-slate-900 leading-relaxed">
                    Daftar online, pantau antrean real-time, dan periksa tanpa <br> perlu nunggu lama
                </p>

                <div class="flex justify-start items-center h-41 gap-5 mt-10">

                    <!-- Antrean Online -->
                    <div
                        class="flex flex-col items-start w-40 flex-shrink-0 gap-2 px-1.5 py-4 smooth-corners-lg rounded-3xl bg-white transition-all transform hover:scale-105"
                        style="box-shadow: 0 1px 2px rgba(0,0,0,0.3), 0 1px 4px 1px rgba(0,0,0,0.15);">
                        <div class="flex flex-col items-center self-stretch gap-5">

                            <i class="ph ph-ticket text-500 text-[40px]"></i>
                            <div class="flex flex-col items-start self-stretch gap-2">
                                <p class="w-full text-[15px] font-medium text-center text-black">
                                    Antrean Online
                                </p>
                                <p class="w-full text-xs text-center text-black">
                                    Daftar pemeriksaan hari ini secara daring
                                </p>
                            </div>
                        </div>
                    </div>

                    <!-- Cek Antrean -->
                    <div
                        class="flex flex-col items-start w-40 flex-shrink-0 gap-2 px-1.5 py-4  rounded-3xl bg-white transition-all transform hover:scale-105"
                        style="box-shadow: 0 1px 2px rgba(0,0,0,0.3), 0 1px 4px 1px rgba(0,0,0,0.15);">
                        <div class="flex flex-col items-center self-stretch gap-5">
                            <i class="ph ph-clock-countdown text-500 text-[40px]"></i>

                            <div class="flex flex-col items-start self-stretch gap-2">
                                <p class="w-full text-[15px] font-medium text-center text-black">
                                    Cek Antrean
                                </p>
                                <p class="w-full text-xs text-center text-black">
                                    Pantau status nomor antrean Anda
                                </p>
                            </div>
                        </div>
                    </div>

                    <!-- Riwayat -->
                    <div
                        class="flex flex-col items-start w-40 flex-shrink-0 gap-2 px-1.5 py-4  rounded-3xl bg-white transition-all transform hover:scale-105"
                        style="box-shadow: 0 1px 2px rgba(0,0,0,0.3), 0 1px 4px 1px rgba(0,0,0,0.15);">
                        <div class="flex flex-col items-center self-stretch gap-5">
                            <i class="ph ph-calendar-blank text-500 text-[40px]"></i>

                            <div class="flex flex-col items-start self-stretch gap-2">
                                <p class="w-full text-[15px] font-medium text-center text-black">
                                    Riwayat
                                </p>
                                <p class="w-full text-xs text-center h-8 text-black">
                                    Lihat riwayat kunjungan
                                </p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>





        <div class="relative w-full max-w-[85%] mx-auto  flex-shrink-0 pl-20 mb-6">
            <h2 class="text-4xl font-bold tracking-tight text-teal-700 mb-3">
                Antrean Real Time
            </h2>
            <p class="text-lg   text-slate-900 leading-relaxed">
                Data diperbarui langsung dari loket Poliklinik
            </p>
        </div>

        <div class="relative flex justify-center  pb-4 scale-85">
            <svg viewBox="0 0 200 200" class="absolute w-90 left-[0%] top-[45%] opacity-25">
                <path fill="#14B8A6" d="M30.4,-50.6C43.3,-45.1,60.6,-45,65.3,-37.5C70,-29.9,62.3,-15,57.7,-2.6C53.1,9.7,51.7,19.3,50.5,33.1C49.3,46.8,48.4,64.5,40,70.7C31.7,77,15.8,71.7,3.5,65.7C-8.9,59.7,-17.8,53,-26.4,46.9C-35,40.8,-43.3,35.3,-54.1,27.6C-65,19.8,-78.4,9.9,-77.1,0.8C-75.8,-8.4,-59.7,-16.8,-49.1,-24.9C-38.4,-32.9,-33.1,-40.5,-25.8,-49.2C-18.5,-57.9,-9.3,-67.7,-0.3,-67.2C8.7,-66.7,17.4,-56,30.4,-50.6Z" transform="translate(100 100)" />
            </svg>
            <?php include __DIR__.'/../components/live-antrean.php'; ?>
        </div>



        <section aria-labelledby="layanan-heading" class="mt-32 w-full relative flex flex-col items-center px-4 pb-24">

            <div class="pointer-events-none absolute inset-0 text-teal-700/50" aria-hidden="true">


                <i class="ph ph-stethoscope absolute top-[7%] left-[20%] text-6xl -rotate-8"></i>
                <i class="ph ph-first-aid absolute top-[6%] right-[20%] text-7xl rotate-12"></i>

                <i class="ph ph-plus absolute top-[21%] left-[10%] text-3xl"></i>
                <i class="ph ph-plus absolute top-[21%] right-[10%] text-4xl"></i>
            </div>



            <div class="w-full text-center">
                <h2 id="layanan-heading" class="mb-4 text-6xl font-black font-display-expanded text-center text-teal-700">
                    Layanan Kesehatan
                </h2>
                <p class=" text-lg text-slate-900 opacity-90">
                    Pilih layanan sesuai keluhan
                </p>
            </div>
            <div class="mt-10 w-full">
                <svg viewBox="0 0 200 200" class="absolute w-80 top-[55%] right-[5%] opacity-25">
                    <path fill="#14B8A6" d="M37.1,-60.3C49.3,-57.2,61.2,-49.7,64,-39C66.8,-28.2,60.4,-14.1,59.5,-0.5C58.5,13,63.1,26,62.3,40.4C61.6,54.7,55.6,70.3,44.4,78.8C33.1,87.2,16.5,88.4,2.2,84.6C-12.2,80.8,-24.3,72,-35.2,63.3C-46.1,54.7,-55.7,46.2,-64.9,35.6C-74.2,25.1,-83.1,12.6,-82.2,0.5C-81.3,-11.5,-70.5,-23,-61,-33C-51.4,-43.1,-43.1,-51.6,-33.1,-56C-23.2,-60.4,-11.6,-60.7,0.4,-61.4C12.4,-62.2,24.9,-63.4,37.1,-60.3Z" transform="translate(100 100)" />
                </svg>
                <?php include __DIR__.'/../components/3-card.php'; ?>

            </div>
        </section>

        <section class="flex flex-row relative w-full py-10 mt-10 mx-auto gap-24  bg-slate-900/70 items-center justify-center">
            <!-- Card Section -->
            <div>
                <div class="card w-100 h-85 bg-[url(/images/poliklinikhome.webp)] bg-cover bg-center rounded-2xl">
                </div>
            </div>

            <!-- About Section -->
            <div class="flex flex-col gap-4 items-end">
                <h2 id="tentang-heading" class="w-full text-8xl font-black  text-right text-white">
                    Tentang Kami
                </h2>
                <p class="text-base text-right text-white pb-40 max-w-xl">
                    Poliklinik Politeknik Negeri Malang adalah unit layanan kesehatan di lingkungan kampus yang menjaga kesehatan seluruh civitas akademika. Kami menyediakan layanan pemeriksaan, pengobatan, dan konsultasi yang profesional, terjangkau, dan mengutamakan kenyamanan pasien.
                </p>
            </div>

        </section>
    </div>
</body>

<?php include __DIR__.'/../components/footer.php'; ?>

<?php
$content = ob_get_clean();

include __DIR__.'/../views/layout.php';
?>