<?php

/**
 * Home page (VALENIA-29) — standalone full page served at "/".
 *
 * IMPORTANT: this is a complete <html> document, not a content partial. Render
 * it via render(app_path('app/views/home.php'), [...]) from the route. Do NOT use
 * view('home'), which would wrap it in layout.php and emit two <html> documents.
 */
$heroform = $_SESSION['heroform'] ?? 'Default';
$no_antrean = $no_antrean ?? 'P-11';

include __DIR__.'/../components/background-img.php';

$heroImage = '/images/portalpasien.webp';
$heroTitle = 'Portal Pasien';
$heroDesc = 'Pantau status antrean dan riwayat kunjungan Poliklinik VALENIA';
$showCta = false;
?>
<?php include __DIR__.'/../components/cursor.php'; ?>
<?php include __DIR__.'/../components/header.php'; ?>
<div class="overflow-hidden">
    <?php include __DIR__.'/../components/big-hero-card-landing.php'; ?>
</div>


<body>
    <div class="w-full h-130 flex items-center justify-center flex-row gap-8">
        <div
            class="w-90 h-100 smooth-corners-md rounded-4xl bg-gradient-to-br from-white/80 to-white/60 backdrop-blur-[10px] border-2 border-gray-400"
            style="box-shadow: 10px 15px 10px 0 rgba(0,0,0,0.1);">
            <h1 class="text-[32px] pt-5 flex justify-center -mb-2.5 font-bold text-slate-900">Antrean Aktif</h1>
            <div class="flex flex-row justify-center items-center mt-10 gap-8">
                <div class="flex justify-start  bg-[url(/images/qr-placeholder.jpg)] bg-cover bg-center smooth-corners-md rounded-2xl h-34 w-34"></div>

                <div class="flex justify-end  gap-4">
                    <div class="flex flex-col -translate-y-2 gap-1">
                        <p class="text-[32px] font-black text-right text-slate-900"><?php echo $no_antrean; ?></p>
                        <p class="text-sm  text-black text-right">Valenia Carlo</p>
                        <p class="text-sm  text-black text-right"><?php echo $heroform; ?></p>
                        <p class="text-sm  text-black text-right">28 September 2026</p>
                        <p class="text-sm  text-black text-right">10:00 WIB</p>
                    </div>
                </div>
            </div>
            <div class="flex flex-col justify-center items-center">
                <p class="flex justify-center mt-8 text-[14px]">Tunjukkan QR Code saat periksa di poliklinik</p>
                <button type="button" class="w-[148px] h-[48px] mt-6 smooth-corners-md rounded-3xl bg-teal-500 flex flex-row gap-2 items-center justify-center hover:bg-teal-700">
                    <p class="text-base font-medium text-white">Unduh</p>
                    <i class="ph ph-download-simple text-white text-[18px]"></i>
                </button>
            </div>
        </div>


        <div
            class="w-130 h-100 rounded-4xl smooth-corners-md bg-gradient-to-br from-white/80 to-white/60 backdrop-blur-[10px] flex flex-col justify-center items-center border-2 border-gray-400"
            style="box-shadow: 10px 15px 10px 0 rgba(0,0,0,0.1);">
            <div class="flex flex-row w-full">

                <div>
                    <div class=" w-full h-5 flex justify-start ml-12">
                        <h1 class="text-[24px]  flex w-50 font-medium text-slate-900">Valenia Carlo</h1>
                    </div>

                </div>

                <div class=" w-full h-10 flex justify-end mr-12">
                    <div class="border rounded-full w-30 h-30 flex "></div>
                </div>

            </div>

            <div class=" w-104 translate-y-1  ">
                <div class="border-t  w-65 h-20">
                    <p class="text-sm  text-black pt-2">Mahasiswa</p>
                    <p class="text-sm  text-black">D4-TI</p>
                    <div class="scale-90 pt-2 flex -translate-x-3"><?php
                                                                    $status_tag = 'positive';
$label_tag = 'Aktif';

include __DIR__.'/../components/tag.php'; ?>
                    </div>

                </div>
            </div>


            <div class=" w-104 translate-y-18  ">
                <div class="border-t  w-100 h-20">
                    <p class="  text-black font-medium pt-2">Progres Antrean Aktif</p>
                </div>
            </div>







            <div class=" flex flex-row gap-10 items-center justify-center h-15 w-100 mt-10">
                <div class="flex justify-center items-center">
                    <?php
                    $status_step = 'done';
$step_text = '1';

include __DIR__.'/../components/step.php'; ?>
                </div>
                <span class=" w-[42px] h-[2px] bg-gray-500"></span>

                <div class="flex justify-center items-center">
                    <?php
$status_step = 'done';
$step_text = '2';

include __DIR__.'/../components/step.php'; ?>



                </div>
                <span class=" w-[42px] h-[2px] bg-gray-500"></span>

                <div class=" flex justify-center items-center">
                    <?php
$status_step = 'current';
$step_text = '3';

include __DIR__.'/../components/step.php'; ?>

                </div>

                <span class=" w-[42px] h-[2px] bg-gray-500"></span>

                <div class=" flex justify-center items-center">
                    <?php
$status_step = 'wait';
$step_text = '4';

include __DIR__.'/../components/step.php'; ?>

                </div>

            </div>
            <div class=" flex flex-row gap-10 items-center justify-center h-4 w-112">
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

    <div class="flex flex-col relative w-full h-260 mt-5 mx-auto  smooth-corners-lg rounded-tl-[90px] rounded-tr-[90px] bg-slate-900/70 items-center pt-15">
        <h1 class="text-4xl  flex justify-center font-bold text-white mt-5">Riwayat Kunjungan</h1>

        <p class="text-7xl font-black mt-30 text-white"><?php echo $no_antrean; ?></p>
        <p class="  text-white mt-6 ">Nomor yang dilayani saat ini</p>


        <div class="grid grid-cols-1 gap-6 md:grid-cols-3 max-w-5xl mx-auto mt-40">
            <div class="flex flex-col gap-3 space-y-2 col-span-2">
                <div class="grid grid-cols-1 gap-2 place-items-center rounded-3xl bg-gradient-to-br from-white/80 to-white/60 backdrop-blur-[20px] border border-gray-300/50 p-6 shadow-sm min-h-[324px]">

                    <div class="grid grid-cols-4 items-center px-8 h-15 w-130 border border-gray-500 rounded-3xl bg-white hover:bg-teal-100/80 hover:border-teal-500 active:bg-teal-200/80">
                        <p class="text-lg font-medium text-black">Poli Umum</p>
                        <p class="text-sm font-medium text-black text-center">28 Sep 2026</p>
                        <p class="text-sm font-medium text-black text-center">10:00:00 WIB</p>
                        <div class="flex justify-end scale-90">
                            <?php
        $status_tag = 'negative';
$label_tag = 'Ditangani';

include __DIR__.'/../components/tag.php'; ?>
                        </div>
                    </div>

                    <div class="grid grid-cols-4 items-center px-8 h-15 w-130 border border-gray-500 rounded-3xl bg-white hover:bg-teal-100/80 hover:border-teal-500 active:bg-teal-200/80">
                        <p class="text-lg font-medium text-black">Konseling</p>
                        <p class="text-sm font-medium text-black text-center">28 Sep 2026</p>
                        <p class="text-sm font-medium text-black text-center">10:00:00 WIB</p>
                        <div class="flex justify-end scale-90">
                            <?php
$status_tag = 'positive';
$label_tag = 'Selesai';

include __DIR__.'/../components/tag.php'; ?>
                        </div>
                    </div>

                    <div class="grid grid-cols-4 items-center px-8 h-15 w-130 border border-gray-500 rounded-3xl bg-white hover:bg-teal-100/80 hover:border-teal-500 active:bg-teal-200/80">
                        <p class="text-lg font-medium text-black">Poli Gigi</p>
                        <p class="text-sm font-medium text-black text-center">28 Sep 2026</p>
                        <p class="text-sm font-medium text-black text-center">10:00:00 WIB</p>
                        <div class="flex justify-end scale-90">
                            <?php
$status_tag = 'disabled';
$label_tag = 'Dibatalkan';

include __DIR__.'/../components/tag.php'; ?>
                        </div>
                    </div>

                    <div class="grid grid-cols-4 items-center px-8 h-15 w-130 border border-gray-500 rounded-3xl bg-white hover:bg-teal-100/80 hover:border-teal-500 active:bg-teal-200/80">
                        <p class="text-lg font-medium text-black">Poli Umum</p>
                        <p class="text-sm font-medium text-black text-center">28 Sep 2026</p>
                        <p class="text-sm font-medium text-black text-center">10:00:00 WIB</p>
                        <div class="flex justify-end scale-90">
                            <?php
$status_tag = 'positive';
$label_tag = 'Selesai';

include __DIR__.'/../components/tag.php'; ?>
                        </div>
                    </div>

                </div>

                <div class="rounded-3xl bg-gradient-to-br from-white/80 to-white/60 backdrop-blur-[20px] border border-gray-300/50 p-4 shadow-sm flex flex-row items-center justify-center space-x-2">

                    <input type="text" id="filter" name="filter" placeholder="Cari..."
                        class="w-50 text-xs  text-left bg-transparent p-2 smooth-corners-lg rounded-2xl mr-8  border-[1.5px] border-gray-500 focus:outline-none focus:border-teal-500 focus:bg-teal-100/50 hover:bg-teal-100/50 ">

                    <div
                        x-data="{ 
                            currentPage: 1, 
                            totalPages: 4,
                            goToPage(page) {
                                if(page < 1 || page > this.totalPages) return;
                                this.currentPage = page;
                                // fetch data
                                console.log('Fetching data for page:', this.currentPage);
                            }
                        }"
                        class="flex items-center space-x-2 text-sm font-medium">
                        <!-- Previous button -->
                        <button
                            @click="goToPage(currentPage - 1)"
                            :disabled="currentPage === 1"
                            class="w-9 h-9 flex items-center justify-center rounded-[8px] border border-gray-500 hover:bg-teal-100 active:bg-teal-400/50 active:border-teal-500 transition-colors disabled:opacity-40 disabled:pointer-events-none">
                            <i class="ph ph-caret-left text-[18px]"></i>
                        </button>

                        <!-- number -->
                        <template x-for="i in totalPages" :key="i">
                            <button
                                @click="goToPage(i)"
                                :class="currentPage === i ? 'bg-teal-400/50 text-white border-teal-500' : 'border-gray-500 hover:bg-teal-100'"
                                class="w-9 h-9 flex items-center justify-center rounded-[8px] border transition-colors"
                                x-text="i">
                            </button>
                        </template>

                        <!-- Next button -->
                        <button
                            @click="goToPage(currentPage + 1)"
                            :disabled="currentPage === totalPages"
                            class="w-9 h-9 flex items-center justify-center rounded-[8px] border border-gray-500 hover:bg-teal-100 active:bg-teal-400/50 active:border-teal-500 transition-colors disabled:opacity-40 disabled:pointer-events-none">
                            <i class="ph ph-caret-right text-[18px]"></i>
                        </button>
                    </div>

                </div>
            </div>





            <div class="flex flex-col items-center rounded-3xl bg-gradient-to-br from-white/80 to-white/60 backdrop-blur-[20px] border border-gray-300/50 p-4 shadow-md min-h-[400px] min-w-70">
                <h1 class="text-2xl flex justify-center  font-bold text-slate-900">Poli Umum</h1>

                <div class="w-50 h-55 flex justify-center items-center border-b-2 border-gray-500">
                    <div class="flex    bg-[url(/images/qr-placeholder.jpg)] bg-cover bg-center smooth-corners-md rounded-2xl h-40 w-40 mb-3"></div>
                </div>
                <div class="absolute scale-110 translate-y-59  ">
                    <?php
                    $status_tag = 'negative';
$label_tag = 'Ditangani';

include __DIR__.'/../components/tag.php'; ?>
                </div>

                <p class="text-sm mt-6  text-black text-center">28 September 2026</p>
                <p class="text-sm  text-black text-center">10:00 WIB</p>

                <div class="flex flex-row items-center justify-center gap-4">

                    <button type="button" class="w-[105px] h-[40px] mt-6 smooth-corners-md rounded-3xl border-2 border-amber-500 bg-white/80 text-amber-500 flex items-center justify-center hover:bg-amber-500 hover:text-white">
                        <p class="font-medium ">Batal</p>

                    </button>

                    <button type="button" class="w-[105px] h-[40px] mt-6 smooth-corners-md rounded-3xl bg-teal-500 flex flex-row gap-2 items-center justify-center hover:bg-teal-700">
                        <p class="font-medium text-white">Unduh</p>
                        <i class="ph ph-download-simple text-white text-[18px]"></i>
                    </button>

                </div>



            </div>
        </div>

    </div>


</body>

<?php include __DIR__.'/../components/footer.php'; ?>