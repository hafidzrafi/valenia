<?php

$heroImage = '/images/syarat.jpg';
$heroTitle = 'Syarat dan Ketentuan Berobat';
$heroDesc = 'persyaratan pelayanan kesehatan di Poliklinik VALENIA';
$showCta = false;
$page = 'nonlanding';
?>
<div class="relative bg-cover bg-center bg-no-repeat" style="background-image: url('/images/background.png');">
    <div class="absolute inset-y-0 inset-x-20 bg-teal-100/80" aria-hidden="true"></div>
    <?php include __DIR__.'/../components/header.php'; ?>
    <?php include __DIR__.'/../components/cursor.php'; ?>
    <?php include __DIR__.'/../components/big-hero-card-landing.php'; ?>


    <!-- ke-1 -->

    <svg viewBox="0 0 200 200" class="absolute w-50  left-20  opacity-30">
        <path fill="#14B8A6" d="M30.3,-36.5C42,-26.3,56.1,-19.4,61,-8.3C65.9,2.7,61.5,17.9,54.4,32.4C47.2,47,37.2,60.9,22.3,70.4C7.3,79.8,-12.7,84.7,-24,76.1C-35.3,67.5,-37.9,45.5,-45.3,28.3C-52.6,11.1,-64.7,-1.2,-67.2,-16.2C-69.8,-31.2,-62.8,-48.9,-50,-58.9C-37.2,-68.9,-18.6,-71.2,-4.7,-65.7C9.3,-60.1,18.6,-46.7,30.3,-36.5Z" transform="translate(100 100)" />
    </svg>


    <svg viewBox="0 0 200 200" class="absolute w-100 translate-y-20 right-25   opacity-30">
        <path fill="#14B8A6" d="M36.3,-55C44.1,-44.3,45.5,-29.8,49,-16.5C52.6,-3.2,58.4,8.8,56.6,19.6C54.8,30.4,45.4,40,34.7,48.4C24,56.9,12,64.1,1.4,62.2C-9.3,60.4,-18.6,49.4,-25.3,39.7C-32.1,30,-36.3,21.6,-44.1,11.1C-51.9,0.7,-63.3,-11.8,-66.1,-27.1C-68.9,-42.4,-63,-60.5,-50.5,-69.7C-38.1,-78.8,-19,-79.1,-2.4,-75.8C14.2,-72.5,28.5,-65.6,36.3,-55Z" transform="translate(100 100)" />
    </svg>

    <i class="absolute ph ph-first-aid text-7xl right-30 mt-5  text-teal-500/30 rotate-10 "></i>
    <i class="absolute ph ph-pill text-6xl -translate-x-110 translate-y-25 right-0  text-teal-500/30  "></i>
    <i class="absolute ph ph-stethoscope text-5xl -translate-x-50 translate-y-110 right-0 -rotate-10  text-teal-500/30  "></i>
    <div class="relative flex flex-row justify-center gap-10 w-full pt-20 pl-35 pr-35 h-200 ">
        <div class="sticky top-35  flex flex-col gap-5 w-110 h-80  rounded-3xl p-8 bg-white">
            <i class="absolute ph ph-flower  text-5xl right-5 top-5  text-teal-500/30 "></i>

            <div class="flex flex-row gap-4 ">
                <div class="w-10 h-10 rounded-full bg-teal-500 text-white flex items-center justify-center">
                    <h4 class="font-medium text-lg">01</h4>
                </div>

                <h2 class="text-teal-700 text-lg flex items-center justify-center font-semibold ">Langkah</h2>
            </div>

            <h1 class="text-shadow-slate-900 font-bold  text-3xl">Persyaratan Data dan <br> Pendaftaran</h1>
            <p class="w-[50vh]">Pastikan identitas akademik anda aktif dan data yang diisi sesuai dengan dokumen resmi.</p>
            <?php
            $status_tag = 'positive';
$label_tag = 'Wajib';

include __DIR__.'/../components/tag.php';
?>
        </div>

        <div class="relative flex flex-col items-center">
            <img src="/images/login.svg" alt="" class="w-90 h-90  m-14 ">

            <div class="flex items-center p-6 gap-6 rounded-4xl bg-gradient-to-br from-white/80 to-white/50 backdrop-blur-md border border-white/20 transition-all duration-200 hover:scale-105">
                <div class="grid grid-cols-1 gap-4 w-full">
                    <div class="flex flex-row items-center gap-6">
                        <div class="w-4 h-4 bg-emerald-500 rounded-full"></div>
                        <p class="text-base font-medium ">Memiliki NIM / NIP aktif yang terdaftar di sistem civitas akademika,</p>
                    </div>
                    <div class="flex flex-row items-center gap-6">
                        <div class="w-4 h-4 bg-emerald-500 rounded-full"></div>
                        <p class="text-base font-medium ">Mengisi data pendaftaran online (NIM/NIP, Tanggal Lahir, & Keluhan Utama),</p>
                    </div>
                    <div class="flex flex-row items-center gap-6">
                        <div class="w-4 h-4 bg-emerald-500 rounded-full"></div>
                        <p class="text-base font-medium ">Memastikan data yang diinput sesuai dengan identitas resmi.</p>
                    </div>
                </div>
            </div>
        </div>

    </div>


    <svg viewBox="0 0 200 200" class="absolute w-110 translate-x-5 translate-y-5 left-30   opacity-30">
        <path fill="#14B8A6" d="M39.6,-45.4C54.4,-34.8,71.6,-25.2,78.4,-10.3C85.2,4.6,81.7,24.9,69.6,35.1C57.6,45.3,37.1,45.4,19.7,49.9C2.3,54.3,-11.9,63.1,-20.7,58.9C-29.4,54.8,-32.7,37.8,-38.5,24C-44.4,10.2,-52.9,-0.3,-57.4,-16.3C-62,-32.4,-62.7,-53.9,-52.4,-65.3C-42.2,-76.7,-21.1,-78,-4.3,-72.8C12.4,-67.6,24.9,-56,39.6,-45.4Z" transform="translate(100 100)" />
    </svg>

    <i class="absolute ph ph-tooth text-7xl left-120 translate-y-15  text-teal-500/30 rotate-5 "></i>
    <i class="absolute ph ph-pill text-5xl left-135 translate-y-30  text-teal-500/30  "></i>
    <i class="absolute ph ph-first-aid-kit text-6xl left-50 translate-y-80  text-teal-500/30 -rotate-10  "></i>

    <!-- ke-2 -->

    <div class="flex flex-row justify-center gap-10 w-full pl-35 pr-35 h-180">

        <div class="relative flex flex-col items-center">
            <img src="/images/checked.svg" alt="" class="w-80 h-80 m-14 p-4">

            <div class="flex items-center p-6 gap-6 rounded-4xl bg-gradient-to-br from-white/80 to-white/50 backdrop-blur-md border border-white/20 transition-all duration-200 hover:scale-105">
                <div class="grid grid-cols-1 gap-4 w-full">
                    <div class="flex flex-row items-center gap-6">
                        <div class="w-4 h-4 bg-emerald-500 rounded-full"></div>
                        <p class="text-base font-medium ">Tiket resmi diterbitkan setelah menyelesaikan konfirmasi pendaftaran,</p>
                    </div>
                    <div class="flex flex-row items-center gap-6">
                        <div class="w-4 h-4 bg-emerald-500 rounded-full"></div>
                        <p class="text-base font-medium ">Wajib mengunduh atau menyimpan bukti Tiket & QR Code Antrean,</p>
                    </div>
                    <div class="flex flex-row items-center gap-6">
                        <div class="w-4 h-4 bg-amber-500 rounded-full"></div>
                        <p class="text-base font-medium ">Batas pembatalan tiket Maks. 1 jam sebelum melakukan kunjungan.</p>
                    </div>
                </div>
            </div>
        </div>

        <div class="sticky top-35  flex flex-col gap-5 w-110 h-80  rounded-3xl p-8 bg-white">
            <i class="absolute ph ph-queue  text-5xl right-5 top-5  text-teal-500/30 "></i>
            <div class="flex flex-row gap-4 ">
                <div class="w-10 h-10 rounded-full bg-teal-500 text-white flex items-center justify-center">
                    <h4 class="font-medium text-lg">02</h4>
                </div>

                <h2 class="text-teal-700 text-lg flex items-center justify-center font-semibold ">Langkah</h2>
            </div>

            <h1 class="text-shadow-slate-900 font-bold  text-3xl">Ketentuan Antrean dan <br> Tiket Digital</h1>
            <p class="w-[50vh]">Tiket menjadi bukti kunjungan Anda. Simpan QR Code dan perhatikan batas pembatalan.</p>
            <?php
$status_tag = 'positive';
$label_tag = 'Wajib';

include __DIR__.'/../components/tag.php';
?>
        </div>

    </div>


    <i class="absolute ph ph-clock text-7xl right-35 translate-y-8  text-teal-500/30 "></i>
    <i class="absolute ph ph-ticket text-5xl right-148 translate-y-15  text-teal-500/30 -rotate-12 "></i>
    <!-- ke-3 -->

    <svg viewBox="0 0 200 200" class="absolute w-180 -translate-y-50 right-25   opacity-30">
        <path fill="#14B8A6" d="M29.6,-26.7C43.1,-23.9,62.2,-19.1,70.5,-7.2C78.8,4.7,76.3,23.6,65,32.2C53.7,40.8,33.6,39,18.6,39.4C3.6,39.8,-6.4,42.4,-17.7,41.5C-29.1,40.5,-41.7,35.9,-43.3,27.7C-44.8,19.4,-35.2,7.5,-31.4,-4.1C-27.7,-15.7,-29.8,-26.8,-25.5,-31.3C-21.2,-35.8,-10.6,-33.6,-1.3,-32.1C8,-30.5,16,-29.6,29.6,-26.7Z" transform="translate(100 100)" />
    </svg>

    <svg viewBox="0 0 200 200" class="absolute w-60 bottom-10 left-20    opacity-30">
        <path  fill="#14B8A6" d="M33.2,-45.5C43.4,-38.2,52.4,-29.1,59.6,-17C66.8,-4.9,72.2,10.2,70,24.4C67.7,38.7,57.7,52.1,44.7,63.8C31.8,75.5,15.9,85.4,1.8,82.9C-12.3,80.5,-24.6,65.6,-38.3,54.2C-52.1,42.8,-67.3,34.8,-71.9,22.9C-76.5,10.9,-70.4,-5,-60.7,-15C-50.9,-25,-37.5,-29.1,-26.8,-36.2C-16.1,-43.4,-8,-53.6,1.7,-56C11.5,-58.4,22.9,-52.9,33.2,-45.5Z" transform="translate(100 100)" />
    </svg>

    <i class="absolute ph ph-first-aid-kit text-7xl bottom-10 right-30  text-teal-500/30 rotate-12 "></i>
    <i class="absolute ph ph-heartbeat text-5xl bottom-8 right-53  text-teal-500/30 -rotate-5"></i>

    <div class="relative flex flex-row justify-center gap-10 w-full pl-35 pr-35 pb-40 h-180 ">
        <div class="sticky top-35  flex flex-col gap-5 w-110 h-80  rounded-3xl p-8 bg-white">
            <i class="absolute ph ph-flower-lotus  text-5xl right-5 top-5  text-teal-500/30 "></i>
            <div class="flex flex-row gap-4 ">
                <div class="w-10 h-10 rounded-full bg-teal-500 text-white flex items-center justify-center">
                    <h4 class="font-medium text-lg">03</h4>
                </div>

                <h2 class="text-teal-700 text-lg flex items-center justify-center font-semibold ">Langkah</h2>
            </div>

            <h1 class="text-shadow-slate-900 font-bold  text-3xl">Prosedur Kedatangan <br>dan Jam Operasional</h1>
            <p class="w-[50vh]">Datang lebih awal agar proses pemeriksaan berjalan lancar sesuai urutan antrean.</p>
            <?php
$status_tag = 'positive';
$label_tag = 'Wajib';

include __DIR__.'/../components/tag.php';
?>
        </div>

        <div class="relative flex flex-col">
            <img src="/images/wait-in-line.svg" alt="" class="w-100 h-100 m-18 rounded-2xl">

            <div class="flex items-center p-6 gap-6 rounded-4xl bg-gradient-to-br from-white/80 to-white/50 backdrop-blur-md border border-white/20 transition-all duration-200 hover:scale-105">
                <div class="grid grid-cols-1 gap-4 w-full">
                    <div class="flex flex-row items-center gap-6">
                        <div class="w-4 h-4 bg-emerald-500 rounded-full"></div>
                        <p class="text-base font-medium ">Datang 30 menit sebelum estimasi,</p>
                    </div>
                    <div class="flex flex-row items-center gap-6">
                        <div class="w-4 h-4 bg-emerald-500 rounded-full"></div>
                        <p class="text-base font-medium ">Tunjukkan QR Code saat periksa,</p>
                    </div>
                    <div class="flex flex-row items-center gap-6">
                        <div class="w-4 h-4 bg-emerald-500 rounded-full"></div>
                        <p class="text-base font-medium ">Ikuti alur progres panggilan.</p>
                    </div>
                </div>
            </div>
        </div>


    </div>

    






</div>

<?php include __DIR__.'/../components/footer.php'; ?>