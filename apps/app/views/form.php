<?php

/**
 * Home page (VALENIA-29) — standalone full page served at "/".
 *
 * IMPORTANT: this is a complete <html> document, not a content partial. Render
 * it via render(app_path('app/views/home.php'), [...]) from the route. Do NOT use
 * view('home'), which would wrap it in layout.php and emit two <html> documents.
 */
$heroform = $heroform ?? ($_SESSION['heroform'] ?? 'Default');
$isform = true;
$old = $old ?? [];
$error = $error ?? null;

ob_start();
?>

<?php include __DIR__ . '/../components/cursor.php'; ?>

<body>
    <div class="relative bg-cover bg-center bg-no-repeat" style="background-image: url('/images/background.png');">
        <div class="absolute inset-y-0 inset-x-0 bg-teal-100/60" aria-hidden="true"></div>

        <div class="m-13 ml-25 absolute z-10">
            <nav class="w-70 h-10 gap-2.5 pl-2.5 flex items-center absolute smooth-corners-lg rounded-full bg-gradient-to-br from-white/80 to-white/50 border-2 border-white/50 backdrop-blur-[36.6px]" style="box-shadow: 3px 6px 5px 0 rgba(0,0,0,0.1);">
                <ol class="flex items-center space-x-2">
                    <li>
                        <a href="/" class="text-gray-500 hover:text-teal-500 hover:underline"
                            onclick="localStorage.clear();">Beranda</a>
                    </li>
                    <li>/</li>
                    <li>
                        <span class="text-black font-medium">Form Pendaftaran </span>
                    </li>
                </ol>
            </nav>
        </div>


        <div class="relative min-h-screen flex flex-col">
            <div class="grow flex items-center justify-center">
                <div class="relative pointer-events-none w-280 h-88 p-10  flex flex-col smooth-corners-lg rounded-2xl bg-white">
                    <h1 class="text-6xl mb-2  font-bold tracking-tight text-slate-900"> <?php echo e($heroform); ?> </h1>
                    <p class=" text-base text-black mb-10">Masukkan NIM atau NIP, tanggal lahir dan keluhan <br>
                        yang dialami pada form berikut</p>

                    <p class="text-base text-black flex items-center gap-2 mb-4">
                        Jam Operasional Layanan <i class="ph ph-clock text-black text-[18px]"></i>
                    </p>
                    <?php if ('POLI UMUM' == $heroform || 'POLI GIGI' == $heroform) { ?>
                        <p class="text-base text-black">
                            Senin Kamis: 08.00-16.00 WIB <br>
                            Jumat: 08.00 16.30-WIB
                        </p>
                    <?php } elseif ('KONSELING' == $heroform) { ?>
                        <p class="text-base text-black">
                            Selasa & Jumat 09:00-13:00 WIB
                        </p>

                    <?php } ?>

                    <div
                        class="flex flex-col absolute left-1/2 -translate-x-25 top-[14%] justify-start items-center flex-grow-0 flex-shrink-0 h-[276px] w-[66px] gap-17">

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


                    <div class="absolute left-1/2 top-[16%] flex flex-col gap-8 justify-center items-center">

                        <div class="flex justify-center items-center">
                            <?php
                            $status_step = 'current';
                            $step_text = '1';

                            include __DIR__ . '/../components/step.php'; ?>
                        </div>
                        <span class=" w-[3px] h-[50px] bg-gray-500"></span>

                        <div class="flex justify-center items-center">
                            <?php
                            $status_step = 'wait';
                            $step_text = '2';

                            include __DIR__ . '/../components/step.php'; ?>



                        </div>
                        <span class=" w-[3px] h-[50px] bg-gray-500"></span>

                        <div class=" flex justify-center items-center">
                            <?php
                            $status_step = 'wait';
                            $step_text = '3';

                            include __DIR__ . '/../components/step.php'; ?>

                        </div>

                    </div>



                </div>

                <div class="absolute left-1/2 translate-x-30">
                    <div
                        class="w-115 min-h-130 h-auto pb-14 rounded-4xl bg-gradient-to-br from-white/90 to-white/70 backdrop-blur-[10px]"
                        style="box-shadow: -10px 15px 10px 0 rgba(0,0,0,0.1);">

                        <h1 class="text-[32px] pt-5 flex justify-center -mb-2.5 font-bold text-slate-900">Form Pendaftaran</h1>

                        <?php if (null !== $error) { ?>
                            <p class="mt-4 mx-10 rounded-2xl bg-red-50 px-4 py-2 text-sm text-red-600"><?php echo e($error); ?></p>
                        <?php } ?>

                        <form class="flex flex-col gap-4 items-center mt-6" method="POST" action="/ambil-tiket">

                            <?php echo csrf_field(); ?>
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

                                    <div>
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
                                            class="w-full text-xs font-medium text-left bg-transparent p-2 rounded-2xl  text-gray-500  border-[1.5px] focus:outline-none focus:border-teal-500 focus:text-gray-500"
                                            :class="{
                                        'text-red-500': touched && tanggalLahir === ''
                                        }"
                                            x-model="tanggalLahir"
                                            @input="localStorage.setItem('tanggal_lahir', tanggalLahir)"
                                            @blur="touched = true">

                                    </div>
                                    <div>
                                        <span class="text-xs text-red-500"
                                            x-show="touched && tanggalLahir === ''"
                                            x-transition>Wajib diisi</span>
                                    </div>
                                </div>
                            </div>

                            <div class="flex flex-col justify-start items-start flex-grow-0 flex-shrink-0 w-[384px] relative gap-1"
                                x-data="{ namalengkap: localStorage.getItem('nama_lengkap') || '', touched: false }">
                                <label for="nama_lengkap" class="text-sm font-medium text-left text-black">Nama Lengkap</label>
                                <div class="flex justify-start items-center self-stretch flex-grow-0 flex-shrink-0 relative overflow-hidden gap-2"
                                    :class="{
                                    'border-gray-500': !touched,
                                    'border-red-500': touched && namalengkap === ''                          
                                    }">
                                    <input type="text" id="nama_lengkap" name="nama_lengkap" required maxlength="120"
                                        value="<?php echo e($old['full_name'] ?? ''); ?>"
                                        placeholder="Masukkan nama lengkap..."
                                        class="w-full text-xs font-medium text-left bg-transparent p-2 rounded-2xl  text-gray-500  border-[1.5px] focus:outline-none focus:border-teal-500 focus:text-gray-500"
                                        :class="{
                                        'text-red-500': touched && namalengkap === ''
                                        }"
                                        x-model="namalengkap"
                                        @input="localStorage.setItem('nama_lengkap', namalengkap)"
                                        @blur="touched = true">

                                </div>
                                <div>
                                    <span class="text-xs text-red-500"
                                        x-show="touched && namalengkap === ''"
                                        x-transition>Wajib diisi</span>
                                </div>
                            </div>

                            <div class="flex flex-row gap-6 justify-center">
                                <div class="flex flex-col justify-start items-start w-[180px] relative gap-1"
                                    x-data="{ jeniskelamin: localStorage.getItem('jenis_kelamin') || '', touched: false }">
                                    <label for="jenis_kelamin" class="text-sm font-medium text-left text-black">Jenis Kelamin</label>
                                    <div class="flex justify-start items-center self-stretch flex-grow-0 flex-shrink-0 relative overflow-hidden gap-2"
                                        :class="{
                                    'border-gray-500': !touched,
                                    'border-red-500': touched && jeniskelamin === ''                          
                                    }">
                                        <select id="jenis_kelamin" name="jenis_kelamin" required
                                            class="w-full text-xs font-medium text-left bg-transparent p-2 smooth-corners-lg rounded-2xl border-[1.5px]  text-gray-500 focus:outline-none focus:border-teal-500 focus:text-gray-500"
                                            :class="{
                                        'text-red-500': touched && jeniskelamin === ''
                                        }"
                                            x-model="jeniskelamin"      
                                            @blur="touched = true">
                                            <option value="">Pilih...</option>
                                            <option value="L" <?php echo ($old['gender'] ?? '') === 'L' ? 'selected' : ''; ?>>Laki-laki</option>
                                            <option value="P" <?php echo ($old['gender'] ?? '') === 'P' ? 'selected' : ''; ?>>Perempuan</option>
                                        </select>
                                    </div>
                                    <div>
                                        <span class="text-xs text-red-500"
                                            x-show="touched && jeniskelamin === ''"
                                            x-transition>Wajib diisi</span>
                                    </div>
                                </div>

                                <div class="flex flex-col justify-start items-start w-[180px] relative gap-1"
                                    x-data="{ kontak: localStorage.getItem('kontak') || '', touched: false }">
                                    <label for="kontak" class="text-sm font-medium text-left text-black">Kontak (opsional)</label>
                                    <input type="text" id="kontak" name="kontak" maxlength="30"
                                        value="<?php echo e($old['contact'] ?? ''); ?>"
                                        placeholder="Nomor telepon..."
                                        class="w-full text-xs font-medium text-left bg-transparent p-2 smooth-corners-lg rounded-2xl border-[1.5px] border-gray-500 text-gray-500 focus:outline-none focus:border-teal-500 focus:text-gray-500"
                                        x-model="kontak"
                                        @input="localStorage.setItem('kontak', kontak)"
                                        @blur="touched = true">
                                </div>
                            </div>

                            <div class="flex flex-col justify-start items-start flex-grow-0 flex-shrink-0 w-[384px] relative gap-1"
                                x-data="{ keluhan: localStorage.getItem('keluhan') || '', touched: false }">
                                <label for="keluhan" class="flex-grow-0 flex-shrink-0 text-sm font-medium text-left text-black">Keluhan</label>
                                <div class="flex flex-col w-full">
                                    <textarea id="keluhan" name="keluhan" required placeholder="Masukkan keluhan..."
                                        class="flex justify-start h-20 self-stretch flex-grow-0 flex-shrink-0 relative overflow-hidden gap-2 px-3 py-2 smooth-corners-lg rounded-2xl bg-white border-[1.5px] resize-none text-xs font-medium text-left text-[#757575] focus:outline-none focus:border-teal-500 focus:text-gray-500"
                                        :class="{
                                'border-gray-500': !touched,
                                'border-red-500': touched && keluhan === '',
                                'text-red-500': touched && keluhan === ''
                                }"
                                        x-model="keluhan"
                                        @input="localStorage.setItem('keluhan', keluhan)"
                                        @blur="touched = true"><?php echo e($old['complaint'] ?? ''); ?></textarea>
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
                            <button type="submit" class="w-[160px] h-[48px] mt-2 mb-2 mx-auto smooth-corners-md rounded-3xl bg-teal-500 flex flex-row gap-2 items-center justify-center hover:bg-teal-600 transition-colors">
                                <p class="text-sm font-semibold text-white">Kirim</p>
                                <i class="ph ph-paper-plane-tilt text-white text-[16px]"></i>
                            </button>
                        </form>
                    </div>
                </div>

            </div>
        </div>
    </div>
</body>


<?php
$content = ob_get_clean();

include __DIR__ . '/../views/layout.php';
?>