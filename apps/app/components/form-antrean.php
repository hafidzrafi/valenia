<div
    class="w-115 h-130 rounded-[64px] bg-gradient-to-br from-white/80 to-white/60 backdrop-blur-[10px]"
    style="box-shadow: -10px 15px 10px 0 rgba(0,0,0,0.1);">
    <h1 class="text-[32px] pt-5 flex justify-center -mb-2.5 font-bold text-slate-900">Form Pendaftaran</h1>
    <form class="flex flex-col gap-6 items-center mt-10" method="POST" action="/ambil-tiket">
        <input type="hidden" name="_token" value="<?php echo e(csrf_token()); ?>">
        <div class="flex flex-row gap-6 justify-center">
            <div class="flex flex-col justify-start items-start flex-grow-0 flex-shrink-0 w-[180px] relative gap-1"
                x-data="{ nimNip: '', touched: false, isValid: true }">
                <label for="nim_nip" class="flex-grow-0 flex-shrink-0 text-sm font-medium text-left text-black">NIM / NIP</label>
                <div class="flex justify-start items-center self-stretch flex-grow-0 flex-shrink-0 relative overflow-hidden gap-2 px-3 py-2 smooth-corners-lg rounded-2xl bg-white border-[1.5px]"
                    :class="{
                                'border-gray-500': !touched,
                                'border-teal-500': touched && isValid,
                                'border-red-500': touched && !isValid
                                }">
                    <input type="text" inputmode="numeric" pattern="[0-9]*" id="nim_nip" name="nim_nip" required placeholder="Masukkan NIM / NIP... "
                        class="w-full text-xs font-medium text-left bg-transparent border-none focus:outline-none"
                        :class="{
                                    'text-gray-500': true,
                                    'text-red-500': touched && !isValid
                                    }"
                        x-model="nimNip"
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
                x-data="{ tanggalLahir: '', touched: false }">
                <label for="tanggal_lahir" class="text-sm font-medium text-left text-black">Tanggal Lahir</label>
                <div class="flex justify-start items-center self-stretch flex-grow-0 flex-shrink-0 relative overflow-hidden gap-2 px-3 py-2 smooth-corners-lg rounded-2xl bg-white border-[1.5px]"
                    :class="{
                                    'border-gray-500': !touched,
                                    'border-teal-500': touched && tanggalLahir != '',
                                    'border-red-500': touched && tanggalLahir === ''                          
                                }">
                    <input type="date" id="tanggal_lahir" name="tanggal_lahir " required
                        class="w-full text-xs font-medium text-left text-[#757575] bg-transparent border-none focus:outline-none"
                        :class="{
                                     'text-gray-500': true,
                                        'text-red-500': touched && tanggalLahir === ''
                                }"
                        x-model="tanggalLahir"
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
            x-data="{ keluhan: '', touched: false }">
            <label for="keluhan" class="flex-grow-0 flex-shrink-0 text-sm font-medium text-left text-black">Keluhan</label>
            <div class="flex flex-col w-full">
                <textarea id="keluhan" name="keluhan" required placeholder="Masukkan keluhan..."
                    class="flex justify-start h-24 self-stretch flex-grow-0 flex-shrink-0 relative overflow-hidden gap-2 px-3 py-2 smooth-corners-lg rounded-2xl bg-white border-[1.5px] resize-none text-xs font-medium text-left text-[#757575] focus:outline-none"
                    :class="{
                                    'border-gray-500': !touched,
                                    'border-teal-500': touched && keluhan != '',
                                    'border-red-500': touched && keluhan === '',
                                    'text-gray-500': true,
                                        'text-red-500': touched && keluhan === ''
                                }"
                    x-model="keluhan"

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