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
                    <a href="/" class="text-gray-500 hover:text-teal-500 hover:underline"
                        onclick="localStorage.removeItem('nim_nip'); localStorage.removeItem('tanggal_lahir'); localStorage.removeItem('keluhan');">Beranda</a>
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
                <p class="pl-15.5 mt-17 text-[16px] text-black flex items-center gap-2">
                    Jam Operasional Layanan <i class="ph ph-clock text-black text-[18px]"></i>
                </p>
                <?php if ($heroform == "POLI UMUM" || $heroform == "POLI GIGI"): ?>
                    <p class="pl-15.5 text-[16px] text-black">
                        Senin Kamis: 08.00-16.00 WIB <br>
                        Jumat: 08.00 16.30-WIB
                    </p>
                <?php elseif ($heroform == "KONSELING"): ?>
                    <p class="pl-15.5 mt-3 text-[16px] text-black">
                        Selasa & Jumat 09:00-13:00 WIB
                    </p>

                <?php endif ?>

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

            <div class="absolute right-25">
                <div
                    class="w-115 h-130 rounded-[64px] bg-gradient-to-br from-white/80 to-white/60 backdrop-blur-[10px]"
                    style="box-shadow: -10px 15px 10px 0 rgba(0,0,0,0.1);">

                    <h1 class="text-[32px] pt-5 flex justify-center -mb-2.5 font-bold text-slate-900">Form Pendaftaran</h1>

                    <form class="flex flex-col gap-6 items-center mt-10" method="POST" action="/ambil-tiket">

                        <input type="hidden" name="_token" value="<?= e(csrf_token()) ?>">
                        <div class="flex flex-row gap-6 justify-center">
                            <div class="flex flex-col justify-start items-start flex-grow-0 flex-shrink-0 w-[180px] relative gap-1"
                                x-data="{ 
                                nimNip: localStorage.getItem('nim_nip') || '', 
                                touched: false, 
                                isValid: true 
                                }">
                                <label for="nim_nip" class="flex-grow-0 flex-shrink-0 text-sm font-medium text-left text-black">NIM / NIP</label>
                                <div class="flex justify-start items-center self-stretch flex-grow-0 flex-shrink-0 relative overflow-hidden gap-2"
                                    :class="{
                                     'border-gray-500': !touched,
                                     'border-red-500': touched && !isValid
                                    }">
                                    <input type="text" inputmode="numeric" pattern="[0-9]*" id="nim_nip" name="nim_nip" required placeholder="Masukkan NIM / NIP..."
                                        class="w-full text-xs font-medium text-left bg-transparent p-2 smooth-corners-lg rounded-2xl  border-[1.5px] focus:outline-none focus:border-teal-500 focus:text-gray-500"
                                        :class="{
                                    'text-gray-500': true,
                                    'text-red-500': touched && !isValid
                                    }"
                                        x-model="nimNip"
                                        @input="localStorage.setItem('nim_nip', nimNip)"
                                        @blur="touched = true; isValid = nimNip != '' && /^\d*$/.test(nimNip)">
                                </div>

                                <div class="h-4">
                                    <span class="text-xs text-red-500"
                                        x-show="touched && (nimNip === '' || !/^\d*$/.test(nimNip))"
                                        x-transition>
                                        <span x-show="nimNip === ''">Wajib diisi</span>
                                        <span x-show="nimNip !== '' && !/^\d*$/.test(nimNip)">Hanya bisa diisi angka</span>
                                    </span>
                                </div>
                            </div>

                            <div class="flex flex-col justify-start items-start w-[180px] relative gap-1"
                                x-data="{ tanggalLahir: localStorage.getItem('tanggal_lahir') || '', touched: false }">
                                <label for="tanggal_lahir" class="text-sm font-medium text-left text-black">Tanggal Lahir</label>
                                <div class="flex justify-start items-center self-stretch flex-grow-0 flex-shrink-0 relative overflow-hidden gap-2"
                                    :class="{
                                    'border-gray-500': !touched,
                                    'border-red-500': touched && tanggalLahir === ''                          
                                    }">

                                    <input type="date" id="tanggal_lahir" name="tanggal_lahir" required
                                        class="w-full text-xs font-medium text-left bg-transparent p-2 smooth-corners-lg rounded-2xl border-gray-500 text-gray-500  border-[1.5px] focus:outline-none focus:border-teal-500 focus:text-gray-500"
                                        :class="{
                                        'text-red-500': touched && tanggalLahir === ''
                                        }"
                                        x-model="tanggalLahir"
                                        @input="localStorage.setItem('tanggal_lahir', tanggalLahir)"
                                        @blur="touched = true">

                                </div>
                                <div class="h-4">
                                    <span class="text-xs text-red-500"
                                        x-show="touched && tanggalLahir === ''"
                                        x-transition>Wajib diisi</span>
                                </div>
                            </div>
                        </div>

                        <div class="flex flex-col justify-start items-start flex-grow-0 flex-shrink-0 w-[384px] relative gap-1"
                            x-data="{ keluhan: localStorage.getItem('keluhan') || '', touched: false }">
                            <label for="keluhan" class="flex-grow-0 flex-shrink-0 text-sm font-medium text-left text-black">Keluhan</label>
                            <div class="flex flex-col w-full">
                                <textarea id="keluhan" name="keluhan" required placeholder="Masukkan keluhan..."
                                    class="flex justify-start h-24 self-stretch flex-grow-0 flex-shrink-0 relative overflow-hidden gap-2 px-3 py-2 smooth-corners-lg rounded-2xl bg-white border-[1.5px] resize-none text-xs font-medium text-left text-[#757575] focus:outline-none focus:border-teal-500 focus:text-gray-500"
                                    :class="{
                                'border-gray-500': !touched,
                                'border-red-500': touched && keluhan === '',
                                'text-red-500': touched && keluhan === ''
                                }"
                                    x-model="keluhan"
                                    @input="localStorage.setItem('keluhan', keluhan)"
                                    @blur="touched = true"></textarea>
                                <div class="h-4">
                                    <span class="text-xs text-red-500"
                                        x-show="touched && keluhan === ''"
                                        x-transition>Wajib diisi</span>
                                </div>
                                <div class="flex justify-end mt-1">
                                    <a href="#" class="text-teal-500 text-[14px] font-medium hover:text-teal-800 hover:underline">Bantuan?</a>
                                </div>
                            </div>
                        </div>
                        <button type="submit" class="w-[188px] h-[59px] mt-5 mx-auto smooth-corners-md rounded-3xl bg-teal-500 flex flex-row gap-2 items-center justify-center hover:bg-teal-600 transition-colors">
                            <p class="text-base font-semibold text-white">Kirim</p>
                            <i class="ph ph-paper-plane-tilt text-white text-[18px]"></i>
                        </button>
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