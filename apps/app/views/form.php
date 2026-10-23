<?php

/**
 * Home page (VALENIA-29) — standalone full page served at "/".
 *
 * IMPORTANT: this is a complete <html> document, not a content partial. Render
 * it via render(app_path('app/views/home.php'), [...]) from the route. Do NOT use
 * view('home'), which would wrap it in layout.php and emit two <html> documents.
 *
 */
$heroform = $_GET['heroform'] ?? "Default";


include __DIR__ . '/../components/background-img.php';
ob_start();
?>


<body>
    <div class="m-13 ml-25 absolute">
        <nav aria-label="Breadcrumb" class="w-70 h-10 gap-2.5 pl-2.5 flex items-center absolute smooth-corners-lg rounded-full bg-gradient-to-br from-white/90 to-white/60 border-2 border-white/50 backdrop-blur-[36.6px]" style="box-shadow: 3px 6px 5px 0 rgba(0,0,0,0.1);">
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
                <h1 class="text-[64px] pt-5 pl-15 -mb-2.5 font-bold text-slate-900"> <?= htmlspecialchars($heroform) ?> </h1>
                <span class="ml-15.5 w-[375px] h-[5px] mb-3 bg-slate-900"></span>
                <p class="pl-15.5 text-[16px] text-black">Masukkan NIM atau NIP, tanggal lahir dan keluhan <br>
                    yang dialami pada form berikut</p>


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

            <div
                class="w-115 h-130 right-25 absolute  rounded-[64px] bg-gradient-to-br from-white/80 to-white/60 backdrop-blur-[10px]"
                style="box-shadow: -10px 15px 10px 0 rgba(0,0,0,0.1);">
                <h1 class="text-[32px] pt-5 pl-15 -mb-2.5 font-bold text-slate-900">NGETEST DOANG WOI</h1>
                <div class="flex flex-row gap-6 justify-center mt-10">

                    <div
                        class="flex flex-col justify-start items-start flex-grow-0 flex-shrink-0 w-[180px] relative gap-1">
                        <p class="flex-grow-0 flex-shrink-0 text-sm font-medium text-left text-black">NIM / NIP</p>
                        <div
                            class="flex justify-start items-center self-stretch flex-grow-0 flex-shrink-0 relative overflow-hidden gap-2 px-3 py-2 smooth-corners-lg rounded-2xl bg-white border-[1.5px] border-[#757575]">
                            <p class="flex items-center gap-2 flex-grow-0 flex-shrink-0 text-xs font-medium text-left text-[#757575]">
                                <i class="ph ph-user text-500 text-[16px]"></i> Type here...
                            </p>
                        </div>
                    </div>

                    <div
                        class="flex flex-col justify-start items-start flex-grow-0 flex-shrink-0 w-[180px] relative gap-1">
                        <p class="flex-grow-0 flex-shrink-0 text-sm font-medium text-left text-black">label</p>
                        <div
                            class="flex justify-start items-center self-stretch flex-grow-0 flex-shrink-0 relative overflow-hidden gap-2 px-3 py-2 smooth-corners-lg rounded-2xl  bg-white border-[1.5px] border-[#757575]">
                            <p class="flex-grow-0 flex-shrink-0 text-xs font-medium text-left text-[#757575]">
                                Type here...
                            </p>
                        </div>
                    </div>

                </div>

                <div class="flex justify-center mt-10">
                    <div
                        class="flex flex-col justify-start items-start flex-grow-0 flex-shrink-0 w-[384px] h-20 relative gap-1">
                        <p class="flex-grow-0 flex-shrink-0 text-sm font-medium text-left text-black">NIM / NIP</p>
                        <div
                            class="flex justify-start h-24 self-stretch flex-grow-0 flex-shrink-0 relative overflow-hidden gap-2 px-3 py-2 smooth-corners-lg rounded-2xl bg-white border-[1.5px] border-[#757575]">
                            <p class="flex-grow-0 flex-shrink-0 text-xs font-medium text-left text-[#757575] align-self-start">
                                Masukkan keluhan...
                            </p>
                        </div>
                    </div>
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