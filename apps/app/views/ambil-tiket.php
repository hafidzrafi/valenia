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
?>


<body>
    <div class="mt-13 right-110   absolute">
        <nav class="w-85 h-10 gap-2.5 pl-2.5 flex items-center absolute smooth-corners-lg rounded-full bg-gradient-to-br from-white/90 to-white/60 border-2 border-white/50 backdrop-blur-[36.6px]" style="box-shadow: 3px 6px 5px 0 rgba(0,0,0,0.1);">
            <ol class="flex items-center space-x-2">
                <li>
                    <a href="/" class="text-gray-500 hover:text-teal-500 hover:underline">Beranda</a>
                </li>
                <li>/</li>
                <li>
                    <a href="#"
                        x-data
                        @click.prevent="window.history.back()"
                        class="text-gray-500 hover:text-teal-500 hover:underline">
                        Form Pendaftaran
                    </a>
                </li>
                <li>/</li>
                <li>
                    <span class="text-black font-medium">Antrean </span>
                </li>
            </ol>
        </nav>
    </div>


    <div class="min-h-screen flex flex-col">
        <div class="grow flex items-center justify-center">
            <div class="w-280 h-88  flex flex-col smooth-corners-lg rounded-[64px] bg-white">
                <h1 class="text-[47px] pt-5 pr-15 -mb-2.5 flex justify-end font-bold text-slate-900"> AMBIL ANTREAN </h1>
                <span class="mx-auto translate-x-78 mt-1 w-[375px] h-[5px] mb-3 flex justify-end items-center right-0 bg-slate-900"></span>
                <p class="pr-15.5 flex justify-end text-right  text-[16px] text-black">Periksa kembali data pendaftaran, klik tombol <br>
                    Ambil Antrean untuk mengonfirmasi dan <br>
                    menerbitkan bukti tiket resmi
                </p>
                <p class="pr-15.5 mt-22 flex justify-end text-right  text-[16px] text-black flex items-center gap-2">
                    <span class="text-amber-500">Peringatan:</span> Nomor antrean yang sudah
                </p>
                <p class="pr-15.5 flex justify-end text-right  text-[16px] text-black flex items-center gap-2">
                    diambil tidak dapat dibatalkan
                </p>

                <div
                    class="flex flex-col translate-x-140 translate-y-13 absolute justify-start items-center flex-grow-0 flex-shrink-0 h-[276px] w-[66px] gap-17">

                    <p class=" flex-grow text-xs font-medium text-center text-black">
                        Isi Form
                    </p>
                    <p class="flex-grow  text-xs font-medium text-center text-teal-500">
                        Ambil no Antrean
                    </p>
                    <p class=" flex-grow  text-xs font-medium text-center -translate-y-1 text-black">
                        Datang ke Poliklinik
                    </p>

                </div>


                <div class="absolute flex flex-col gap-8 translate-x-127 translate-y-15  justify-center items-center">

                    <div class="flex justify-center items-center">
                        <?php
                        $status_step = 'done';

                        include __DIR__ . '/../components/step.php' ?>
                    </div>
                    <span class=" w-[3px] h-[50px] bg-gray-500"></span>

                    <div class="flex justify-center items-center">
                        <?php
                        $status_step = 'current';
                        $step_text = '2';
                        include __DIR__ . '/../components/step.php' ?>



                    </div>
                    <span class=" w-[3px] h-[50px] bg-gray-500"></span>

                    <div class=" flex justify-center items-center">
                        <?php
                        $status_step = 'wait';
                        $step_text = '3';
                        include __DIR__ . '/../components/step.php' ?>

                    </div>

                </div>



            </div>

            <div class="absolute left-25">
                <div
                    class="w-115 h-130 rounded-[64px] bg-gradient-to-br from-white/80 to-white/60 backdrop-blur-[10px]"
                    style="box-shadow: 10px 15px 10px 0 rgba(0,0,0,0.1);">
                    <h1 class="text-[32px] pt-5 flex justify-center -mb-2.5 font-bold text-slate-900">Konfirmasi Pendaftaran</h1>
                    <p class=" text-[64px] mt-10 font-black text-center text-slate-900"><?= e($no_antrean) ?> </p>

                    <div class="flex justify-center mt-10 items-center gap-4">
                        <div class="flex flex-col gap-3 h-[83px] w-[166px]">
                            <p class="text-sm font-medium text-black h-5.5">Nama</p>
                            <p class="text-sm font-medium text-black h-5.5">Layanan</p>
                            <p class="text-sm font-medium text-black h-5.5">Estimasi dipanggil</p>
                        </div>

                        <div class="flex flex-col gap-3 h-[83px] w-4">
                            <p class="text-sm font-medium text-black h-5.5">:</p>
                            <p class="text-sm font-medium text-black h-5.5">:</p>
                            <p class="text-sm font-medium text-black h-5.5">:</p>
                        </div>

                        <div class="flex flex-col gap-3 h-[78px] w-[110px]">
                            <p class="text-sm font-medium text-black text-right">Valenia Carlo</p>
                            <p class="text-sm font-medium text-black text-right"><?= e($heroform) ?> </p>
                            <p class="text-sm font-medium text-black text-right">10:00 WIB</p>
                        </div>
                    </div>

                    <form class="flex flex-col gap-6 items-center mt-10" method="POST" action="/muncul-qr"
                        onsubmit="localStorage.removeItem('nim_nip'); localStorage.removeItem('tanggal_lahir'); localStorage.removeItem('keluhan');">
                        <input type="hidden" name="_token" value="<?= e(csrf_token()) ?>">
                        <div class="flex flex-row gap-6 justify-center">
                        </div>
                        <div class="flex flex-row gap-6 justify-center items-center scale-90">
                            <button type="button" onclick="window.history.back()" class="w-[188px] h-[59px] smooth-corners-md rounded-3xl border-2 text-amber-500 border-amber-500 flex flex-row gap-2 items-center justify-center hover:bg-amber-500 hover:text-white">
                                <p class="text-base font-semibold ">Batal</p>
                            </button>
                            <button type="submit" class="w-[188px] h-[59px] smooth-corners-md rounded-3xl bg-teal-500 flex flex-row gap-2 items-center justify-center hover:bg-teal-700">
                                <p class="text-base font-semibold text-white">Kirim</p>
                                <i class="ph ph-download-simple text-white text-[18px]"></i>
                            </button>
                        </div>
                    </form>
                </div>
            </div>

        </div>

        <div>
            <?php include __DIR__ . '/../components/subfooter.php'; ?>
        </div>
    </div>

</body>


<?php
$content = ob_get_clean();
include __DIR__ . '/../views/layout.php';
?>