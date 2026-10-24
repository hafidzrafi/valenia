<?php

/**
 * Home page (VALENIA-29) — standalone full page served at "/".
 *
 * IMPORTANT: this is a complete <html> document, not a content partial. Render
 * it via render(app_path('app/views/home.php'), [...]) from the route. Do NOT use
 * view('home'), which would wrap it in layout.php and emit two <html> documents.
 *
 */


$heroform = $heroform ?? ($_SESSION['heroform'] ?? 'Default');

include __DIR__ . '/../components/background-img.php';
ob_start();
?>


<body>
    <div class="m-13 ml-25 absolute">
        <nav class="w-70 h-10 gap-2.5 pl-2.5 flex items-center absolute smooth-corners-lg rounded-full bg-gradient-to-br from-white/90 to-white/60 border-2 border-white/50 backdrop-blur-[36.6px]" style="box-shadow: 3px 6px 5px 0 rgba(0,0,0,0.1);">
            <ol class="flex items-center space-x-2">
                <li>
                    <a href="/" class="text-gray-500 hover:text-teal-500 hover:underline">Beranda</a>
                </li>
                <li>/</li>
                <li>
                    <span class="text-black font-medium">Form Pendaftaran </span>
                </li>
            </ol>
        </nav>
    </div>


    <div class="min-h-screen flex flex-col">
        <div class="grow flex items-center justify-center">
            <div class="w-280 h-88  flex flex-col smooth-corners-lg rounded-[64px] bg-white">
                <h1 class="text-[64px] pt-5 pl-15 -mb-2.5 font-bold text-slate-900"> <?= e($heroform) ?> </h1>
                <span class="ml-15.5 w-[375px] h-[5px] mb-3 bg-slate-900"></span>
                <p class="pl-15.5 text-[16px] text-black">Masukkan NIM atau NIP, tanggal lahir dan keluhan <br>
                    yang dialami pada form berikut</p>
                <p class="pl-15.5 mt-10 text-[16px] text-black flex items-center gap-2">
                    Jam Operasional Layanan <i class="ph ph-clock text-black text-[18px]"></i>
                </p>

                <div
                    class="flex flex-col translate-x-120 translate-y-13 absolute justify-start items-center flex-grow-0 flex-shrink-0 h-[276px] w-[66px] gap-17">

                    <p class=" flex-grow text-xs font-medium text-center text-teal-500">
                        Isi Form
                    </p>
                    <p class="flex-grow  text-xs font-medium text-center text-black">
                        Ambil no Antrean
                    </p>
                    <p class=" flex-grow  text-xs font-medium text-center -translate-y-1 text-black">
                        Datang ke Poliklinik
                    </p>

                </div>


                <div class="absolute flex flex-col gap-8 translate-x-145 translate-y-15  justify-center items-center">

                    <div class="flex justify-center items-center">
                        <?php
                        $status_step = 'current';
                        $step_text = '1';
                        include __DIR__ . '/../components/step.php' ?>
                    </div>
                    <span class=" w-[3px] h-[50px] bg-gray-500"></span>

                    <div class="flex justify-center items-center">
                        <?php
                        $status_step = 'wait';
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

            <div class="absolute right-25" >
                <?php include __DIR__ . '/../components/form-antrean.php' ?>
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