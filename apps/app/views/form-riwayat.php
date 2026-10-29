<?php

/**
 * Home page (VALENIA-29) — standalone full page served at "/".
 *
 * IMPORTANT: this is a complete <html> document, not a content partial. Render
 * it via render(app_path('app/views/home.php'), [...]) from the route. Do NOT use
 * view('home'), which would wrap it in layout.php and emit two <html> documents.
 *
 */



$isform = true;

include __DIR__ . '/../components/background-img.php';

?>

<?php include __DIR__ . '/../components/cursor.php'; ?>




<div class="min-h-screen flex flex-col justify-center items-center">



    <div
        class="w-85 h-120 rounded-4xl bg-gradient-to-br from-white/80 to-white/60 backdrop-blur-[10px] mt-14"
        style="box-shadow: -10px 15px 10px 0 rgba(0,0,0,0.1);">

        <h1 class="text-3xl pt-5 flex justify-center -mb-2.5 font-bold text-slate-900">Portal Pasien</h1>
        <p class="text-sm text-center mt-4">Masukkan NIM/NIP dan tanggal lahir <br> untuk cek riwayat</p>

        <form class="flex flex-col gap-2 items-center mt-10" method="POST" action="/portal-pasien"
            onsubmit="localStorage.removeItem('nim_nip'); localStorage.removeItem('tanggal_lahir');">

            <input type="hidden" name="_token" value="<?= e(csrf_token()) ?>">

            <div class="flex flex-col justify-start items-start flex-grow-0 flex-shrink-0 w-[250px] relative gap-1"
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

            <div class="flex flex-col justify-start items-start w-[250px] relative gap-1"
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

            <div class="flex justify-end w-[250px]">
                <a href="#" class="text-teal-500 text-sm -mt-4 font-medium hover:text-teal-700 hover:underline">Bantuan?</a>
            </div>

            <div class="flex flex-col items-center gap-3 mt-8">
                <button type="submit" class="w-[188px] h-[52px] smooth-corners-md rounded-3xl bg-teal-500 flex flex-row gap-2 items-center justify-center hover:bg-teal-600 transition-colors shadow-md">
                    <p class="text-base font-semibold text-white">Cari</p>
                    <i class="ph ph-magnifying-glass text-white text-[18px]"></i>
                </button>

                <a href="/" class="mt-2 font-medium text-gray-500 hover:text-teal-500 hover:underline"
                    onclick="localStorage.removeItem('nim_nip'); localStorage.removeItem('tanggal_lahir');">
                    Batal
                </a>
            </div>
        </form>
    </div>


    <div>
        <?php include __DIR__ . '/../components/subfooter.php'; ?>
    </div>

</div>