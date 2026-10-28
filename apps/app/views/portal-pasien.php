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

$heroImage = ('/images/portalpasien.webp');
$heroTitle = ("Portal Pasien");
$heroDesc = ("Pantau status antrean dan riwayat kunjungan Poliklinik VALENIA");
$showCta = false;
?>

<?php include __DIR__ . '/../components/header.php';  ?>
<div class="overflow-hidden">
    <?php include __DIR__ . '/../components/big-hero-card-landing.php'; ?>
</div>


<body>
    <div class="w-full h-130 flex items-center justify-center flex-row gap-8">
        <div
            class="w-90 h-100 rounded-[64px] bg-gradient-to-br from-white/80 to-white/60 backdrop-blur-[10px]"
            style="box-shadow: -10px 15px 10px 0 rgba(0,0,0,0.1);">
            <h1 class="text-[32px] pt-5 flex justify-center -mb-2.5 font-bold text-slate-900">Antrean Aktif</h1>
            <div class="flex flex-row justify-center items-center mt-10 gap-8">
                <div class="flex justify-start  bg-[url(/images/qr-placeholder.jpg)] bg-cover bg-center smooth-corners-md rounded-2xl h-34 w-34"></div>

                <div class="flex justify-end  gap-4">
                    <div class="flex flex-col -translate-y-2 gap-1">
                        <p class="text-[32px] font-black text-right text-slate-900"><?php echo ($no_antrean) ?></p>
                        <p class="text-sm  text-black text-right">Valenia Carlo</p>
                        <p class="text-sm  text-black text-right"><?php echo ($heroform) ?></p>
                        <p class="text-sm  text-black text-right">28 September 2026</p>
                        <p class="text-sm  text-black text-right">10:00 WIB</p>
                    </div>
                </div>
            </div>
            <div class="flex flex-col justify-center items-center">
                <p class="flex justify-center mt-8 text-[14px]">Tunjukkan QR Code saat periksa di poliklinik</p>
                <button type="button" class="w-[148px] h-[48px] mt-6 smooth-corners-md rounded-3xl bg-teal-500 flex flex-row gap-2 items-center justify-center hover:bg-teal-700">
                    <p class="text-base font-semibold text-white">Unduh</p>
                    <i class="ph ph-download-simple text-white text-[18px]"></i>
                </button>
            </div>
        </div>


        <div
            class="w-130 h-100 rounded-[64px] smooth-corners-md bg-gradient-to-br from-white/80 to-white/60 backdrop-blur-[10px] flex flex-col justify-center items-center"
            style="box-shadow: -10px 15px 10px 0 rgba(0,0,0,0.1);">
            <div class="flex flex-row w-full">

                <div>
                    <div class=" w-full h-5 flex justify-start ml-15">
                        <h1 class="text-[24px]  flex w-50 font-medium text-slate-900">Valenia Carlo</h1>
                    </div>

                </div>

                <div class=" w-full h-10 flex justify-end mr-15">
                    <div class="border rounded-full w-30 h-30 flex "></div>
                </div>

            </div>

            <div class=" w-100 translate-y-1  ">
                <div class="border-t  w-55 h-20">
                    <p class="text-sm  text-black pt-2">Mahasiswa</p>
                    <p class="text-sm  text-black">D4-TI</p>
                    <div class="scale-90 pt-2 flex -translate-x-3"><?php
                            $status_tag = ("positive");
                            $label_tag = ("Aktif");
                            include __DIR__ . '/../components/tag.php' ?>
                            </div>

                </div>
            </div>


            <div class=" w-100 translate-y-18  ">
                <div class="border-t  w-100 h-20">
                    <p class="  text-black font-medium pt-2">Progres Antrean Aktif</p>
                </div>
            </div>







            <div class=" flex flex-row gap-10 items-center justify-center h-15 w-100 mt-10">
                <div class="flex justify-center items-center">
                    <?php
                    $status_step = 'done';
                    $step_text = '1';
                    include __DIR__ . '/../components/step.php' ?>
                </div>
                <span class=" w-[40px] h-[2px] bg-gray-500"></span>

                <div class="flex justify-center items-center">
                    <?php
                    $status_step = 'done';
                    $step_text = '2';
                    include __DIR__ . '/../components/step.php' ?>



                </div>
                <span class=" w-[40px] h-[2px] bg-gray-500"></span>

                <div class=" flex justify-center items-center">
                    <?php
                    $status_step = 'current';
                    $step_text = '3';
                    include __DIR__ . '/../components/step.php' ?>

                </div>

                <span class=" w-[40px] h-[2px] bg-gray-500"></span>

                <div class=" flex justify-center items-center">
                    <?php
                    $status_step = 'wait';
                    $step_text = '4';
                    include __DIR__ . '/../components/step.php' ?>

                </div>

            </div>
            <div class=" flex flex-row gap-10 items-center justify-center h-4 w-110">
                <p class=" flex-grow  text-xs font-medium text-center  text-black">
                    Terdaftar
                </p>
                <p class=" flex-grow  text-xs font-medium text-center -translate-x-2  text-black">
                    Menunggu
                </p>
                <p class=" flex-grow  text-xs font-medium text-center -translate-x-2.5  text-black">
                    Dilayani
                </p>
                <p class=" flex-grow  text-xs font-medium text-center -translate-x-1  text-black">
                    Selesai
                </p>
            </div>

        </div>

    </div>

    <div class="flex flex-col relative w-full h-100 mt-5 mx-auto  smooth-corners-lg rounded-tl-[90px] rounded-tr-[90px] bg-slate-900/70 items-center pt-20">

    </div>
</body>

<?php include __DIR__ . '/../components/footer.php' ?>

<?php
$content = ob_get_clean();
include __DIR__ . '/../views/layout.php';
?>