<?php

$heroImage = '/images/syarat.jpg';
$heroTitle = 'Syarat dan Ketentuan Berobat';
$heroDesc = 'persyaratan pelayanan kesehatan di Poliklinik VALENIA';
$showCta = false;
$page = 'nonlanding';
?>
<div class="relative bg-cover bg-center bg-no-repeat" style="background-image: url('/images/background.png');">

    <?php include __DIR__.'/../components/cursor.php'; ?>
    <?php include __DIR__.'/../components/header.php'; ?>
    <?php include __DIR__.'/../components/big-hero-card-landing.php'; ?>


    <!-- ke-1 -->

    <div class="flex flex-row justify-center gap-10 w-full pt-20 pl-35 pr-35 h-200 ">
        <div class="sticky top-35  flex flex-col gap-5 w-110 h-80  rounded-3xl p-8 bg-white">

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


    <!-- ke-3 -->

    <div class="flex flex-row justify-center gap-10 w-full pl-35 pr-35 pb-40 h-180 ">
        <div class="sticky top-35  flex flex-col gap-5 w-110 h-80  rounded-3xl p-8 bg-white">

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
            <img src="/images/wait-in-line.svg" alt="" class="w-100 h-100 m-18 bg-slate-400 rounded-2xl">

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