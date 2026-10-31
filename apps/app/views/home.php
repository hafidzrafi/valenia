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
$heroDesc = ("Politeknik Negeri Malang");
if (!isset($_SESSION['isguest'])) {
    $_SESSION['isguest'] = false; 
}


include __DIR__ . '/../components/background-img.php';
ob_start();
?>

<?php include __DIR__ . '/../components/cursor.php';  ?>
<?php include __DIR__ . '/../components/header.php';  ?>
<?php include __DIR__ . '/../components/big-hero-card-landing.php'; ?>

<body>


    <div class="w-full h-127 relative overflow-hidden bg-teal-500/50">
        <div
            x-data="{ shown: false }"
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
            class="flex flex-col justify-start items-center  absolute  top-51.25 gap-7.75 transition-all duration-1000 ease-out">
            <p
                class="self-stretch text-[64px] font-bold text-center font-stretch:expanded tracking-wider text-white">
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

    <div class="relative top-20 w-full max-w-[64rem] mx-auto p-6 md:p-10 overflow-hidden flex flex-col md:flex-row items-center justify-between gap-15">


        <div class="w-80 h-95 flex-1 text-left   rounded-4xl pl-10 pt-5">
            <h2 class="text-[32px] font-bold text-white mb-3">
                Berobat Cuma 3 Langkah
            </h2>
            <p class="text-[18px] w-[90%]  text-white/80 leading-relaxed">
                Daftar online, pantau antrean real-time, dan periksa tanpa perlu nunggu lama
            </p>

            <div class="flex justify-start items-center h-[159px] gap-5 mt-10">

                <!-- Antrean Online -->
                <div
                    class="flex flex-col items-start w-[158px] flex-shrink-0 gap-2 px-1.5 py-6 smooth-corners-lg rounded-[32px] bg-white transition-all transform hover:scale-105"
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
                    class="flex flex-col items-start w-[158px] flex-shrink-0 gap-2 px-1.5 py-6 smooth-corners-lg rounded-[32px] bg-white transition-all transform hover:scale-105"
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
                    class="flex flex-col items-start w-[158px] flex-shrink-0 gap-2 px-1.5 py-6 smooth-corners-lg rounded-[32px] bg-white transition-all transform hover:scale-105"
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

        <div
            x-data="{
    showVideo: false,
    trigger() {
      this.showVideo = true;
      
      // Forces Alpine to wait until the video DOM wrapper is ready
      this.$nextTick(() => {
        const video = this.$refs.reactionVideo;
        if (video) {
          video.currentTime = 0;
          video.playbackRate = 4;
          video.play().catch(err => console.log('Video playback interrupted:', err));
        }
      });

      setTimeout(() => { this.showVideo = false }, 1000);
    }
  }"
            @click="trigger"
            class="relative w-72 shrink-0 z-10 transition-all transform hover:scale-105 active:scale-95 cursor-pointer">

            <img src="/images/shade.webp" class="w-full h-auto object-cover" alt="mascot">

            <div
                x-show="showVideo"
                x-transition.opacity
                class="absolute inset-0 flex items-center justify-center bg-transparent pointer-events-none">

                <video
                    x-ref="reactionVideo"
                    class="w-full h-auto object-contain"
                    playsinline>
                    <source src="/images/hearts.webm" type="video/webm">
                </video>
            </div>
        </div>
    </div>


    <div class="mt-50 w-full h-127 relative flex flex-col flex-shrink-0 items-center">
        <h2 class="grow-0 shrink-0 w-[40%] absolute  text-[64px]  text-center font-bold text-white">
            Pantau Antrean Real Time
        </h2>
        <div class="transform absolute scale-65 mt-50">
            <?php include __DIR__ . '/../components/live-antrean.php' ?>
        </div>

    </div>


    <div class="mt-32 top-32 w-full h-32 relative flex flex-col items-center">
        <div class="absolute w-full text-center">
            <h2 class="mb-4 text-4xl font-bold text-white bg-gradient-to-r from-teal-500 to-[#10B981] inline-block px-6 py-3 smooth-corners-md rounded-[36px] shadow-xl animate-[bounce_3s_infinite]">
                Pilih Layanan Kesehatan
            </h2>
            <p class=" text-lg text-white opacity-90">
                Pilih layanan sesuai keluhan
            </p>
        </div>
        <div class="absolute scale-120 -mt-10   ">
            <?php include __DIR__ . '/../components/3-card.php' ?>
        </div>
    </div>

    <div class="flex flex-col relative w-full mt-140 mx-auto  smooth-corners-lg rounded-tl-[90px] rounded-tr-[90px] bg-slate-900/70 items-center pt-20">
        <div class="flex justify-start items-center relative gap-[200px]">
            <!-- Card Section -->
            <div class="cards perspective-normal">
                <div class="card w-70 h-62.5 -translate-y-20 translate-x-5 bg-[url(/images/placeholder.jpg)] bg-cover bg-center border-2 border-[#555555] smooth-corners-lg rounded-[32px] relative transform-style preserve-3d will-change-transform transition-transform duration-500 hover:translateZ-[10px] hover:rotate-x-[20deg] hover:rotate-y-[20deg] hover:outline-2 hover:outline-teal-500">
                </div>
            </div>

            <!-- About Section -->
            <div class="flex flex-col items-end w-[580px] h-full space-y-4">
                <h2 class="w-full text-8xl font-black text-right text-white">
                    Tentang Kami
                </h2>
                <p class="w-full text-lg text-right text-white pb-40">
                    Poli dimakan Valenia raja Ipsum Loh yah, Poli dimakan Valenia raja Ipsum Loh yah, Poli dimakan Valenia raja Ipsum Loh yah,
                </p>
            </div>
        </div>
    </div>
</body>

<?php include __DIR__ . '/../components/footer.php' ?>

<?php
$content = ob_get_clean();
include __DIR__ . '/../views/layout.php';
?>