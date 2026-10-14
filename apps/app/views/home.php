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
include __DIR__ . '/../components/big-hero-card-landing.php'
?>

<div class="w-full h-127 relative overflow-hidden bg-teal-500">
    <div
        class="flex flex-col justify-start items-center  absolute  top-53.25 gap-7.75">
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

<div class="relative top-20 w-full max-w-[58rem] mx-auto p-6 md:p-10 overflow-hidden flex flex-col md:flex-row items-center justify-between gap-6">


    <div class="flex-1 text-left z-10">
        <h2 class="text-[32px] font-bold text-white mb-3">
            Berobat Cuma 3 Langkah
        </h2>
        <p class="text-[18px] w-[90%]  text-white/80 leading-relaxed">
            Daftar online, pantau antrean real-time, dan periksa tanpa perlu nunggu lama
        </p>

        <div class="flex justify-start items-center h-[159px] gap-5 mt-10">

            <!-- Antrean Online -->
            <div
                class="flex flex-col items-start w-[158px] flex-shrink-0 gap-2 px-1.5 py-6 rounded-[47px] bg-white transition-all transform hover:scale-105"
                style="box-shadow: 0 1px 2px rgba(0,0,0,0.3), 0 1px 4px 1px rgba(0,0,0,0.15);">
                <div class="flex flex-col items-center self-stretch gap-5">
                    <svg xmlns="http://www.w3.org/2000/svg" width="32" height="32"
                        fill="#000" viewBox="0 0 256 256">
                        <path d="M232,104a8,8,0,0,0,8-8V64a16,16,0,0,0-16-16H32A16,16,0,0,0,16,64V96a8,8,0,0,0,8,8,24,24,0,0,1,0,48,8,8,0,0,0-8,8v32a16,16,0,0,0,16,16H224a16,16,0,0,0,16-16V160a8,8,0,0,0-8-8,24,24,0,0,1,0-48ZM32,167.2a40,40,0,0,0,0-78.4V64H88V192H32Zm192,0V192H104V64H224V88.8a40,40,0,0,0,0,78.4Z" />
                    </svg>

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
                class="flex flex-col items-start w-[158px] flex-shrink-0 gap-2 px-1.5 py-6 rounded-[47px] bg-white transition-all transform hover:scale-105"
                style="box-shadow: 0 1px 2px rgba(0,0,0,0.3), 0 1px 4px 1px rgba(0,0,0,0.15);">
                <div class="flex flex-col items-center self-stretch gap-5">
                    <svg xmlns="http://www.w3.org/2000/svg" width="32" height="32"
                        fill="#000" viewBox="0 0 256 256">
                        <path d="M232,136.66A104.12,104.12,0,1,1,119.34,24,8,8,0,0,1,120.66,40,88.12,88.12,0,1,0,216,135.34,8,8,0,0,1,232,136.66ZM120,72v56a8,8,0,0,0,8,8h56a8,8,0,0,0,0-16H136V72a8,8,0,0,0-16,0Zm40-24a12,12,0,1,0-12-12A12,12,0,0,0,160,48Zm36,24a12,12,0,1,0-12-12A12,12,0,0,0,196,72Zm24,36a12,12,0,1,0-12-12A12,12,0,0,0,220,108Z" />
                    </svg>

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
                class="flex flex-col items-start w-[158px] flex-shrink-0 gap-2 px-1.5 py-6 rounded-[47px] bg-white transition-all transform hover:scale-105"
                style="box-shadow: 0 1px 2px rgba(0,0,0,0.3), 0 1px 4px 1px rgba(0,0,0,0.15);">
                <div class="flex flex-col items-center self-stretch gap-5">
                    <svg xmlns="http://www.w3.org/2000/svg" width="32" height="32"
                        fill="#000" viewBox="0 0 256 256">
                        <path d="M208,32H184V24a8,8,0,0,0-16,0v8H88V24a8,8,0,0,0-16,0v8H48A16,16,0,0,0,32,48V208a16,16,0,0,0,16,16H208a16,16,0,0,0,16-16V48A16,16,0,0,0,208,32ZM72,48v8a8,8,0,0,0,16,0V48h80v8a8,8,0,0,0,16,0V48h24V80H48V48ZM208,208H48V96H208V208Z" />
                    </svg>

                    <div class="flex flex-col items-start self-stretch gap-2">
                        <p class="w-full text-[15px] font-medium text-center text-black">
                            Riwayat
                        </p>
                        <p class="w-full text-xs text-center text-black">
                            Lihat riwayat kunjungan dan medis
                        </p>
                    </div>
                </div>
            </div>
        </div>



    </div>


    <div class="relative w-72 shrink-0 z-10">
        <img src="/images/shade.svg" class="w-full h-auto object-cover" alt="mascot">
    </div>

</div>