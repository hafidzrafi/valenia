<div class="fixed top-0 left-1/2 -translate-x-1/2 z-50 w-[80%] mt-6 h-20 px-8 py-4 bg-gradient-to-br from-white/90 to-white/60 rounded-[64px] shadow-[6px_7px_7px_0px_rgba(0,0,0,0.25)] outline outline-4 outline-offset-[-4px] outline-white/50 backdrop-blur-xl flex justify-between items-center smooth-corners-sm">
    <div class="flex items-center gap-3">
        <div class="size-16 relative overflow-hidden  flex items-center justify-center">
            <img src="/images/valenia-logo.svg" alt="Logo Perusahaan" class="w-full h-full object-contain" />
        </div>
        <span class="text-teal-500 text-2xl font-light font-stretch:expanded font-['SF_Pro_Display'] tracking-wider">
            VALENIA
        </span>
    </div>
    <div class="flex items-center gap-12">
        <nav class="flex items-center gap-12 text-black text-base font-['SF_Pro_Display']">
            <a href="#" class="hover:text-teal-500 transition-colors mx-10">Beranda</a>

            <!-- Dropdown Layanan -->
            <div class="relative group mx-10">
                <a href="#" class="hover:text-teal-500 transition-colors flex items-center gap-1">
                    Layanan
                    <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" fill="currentColor" viewBox="0 0 256 256" class="transition-transform duration-300 group-hover:rotate-180">
                        <path d="M213.66,101.66l-80,80a8,8,0,0,1-11.32,0l-80-80A8,8,0,0,1,53.66,90.34L128,164.69l74.34-74.35a8,8,0,0,1,11.32,11.32Z"></path>
                    </svg>
                </a>
                <div class="absolute left-1/2 -translate-x-1/2 pt-2 opacity-0 invisible group-hover:opacity-100 group-hover:visible transition-all duration-300 z-50">
                    <div class="w-52 bg-white backdrop-blur-md rounded-2xl shadow-xl border border-white/20 py-2 overflow-hidden">

                        <form action="/form" method="get">
                            <input type="hidden" name="heroform" value="Poli Umum">
                            <button type="submit" class="block w-full px-4 py-2 text-left rounded-3xl hover:bg-teal-500 hover:text-white transition-colors active:bg-teal-700">
                                Poli Umum
                            </button>
                        </form>

                        <form action="/form" method="get">
                            <input type="hidden" name="heroform" value="Poli Gigi">
                            <button type="submit" class="block w-full px-4 py-2 text-left rounded-3xl hover:bg-teal-500 hover:text-white transition-colors active:bg-teal-700">
                                Poli Gigi
                            </button>
                        </form>

                        <form action="/form" method="get">
                            <input type="hidden" name="heroform" value="Konseling">
                            <button type="submit" class="block w-full px-4 py-2 text-left rounded-3xl hover:bg-teal-500 hover:text-white transition-colors active:bg-teal-700">
                                Konseling dan Psikologi
                            </button>
                        </form>

                        <a href="#" class="block px-4 py-2 rounded-3xl hover:bg-teal-500 hover:text-white transition-colors active:bg-teal-700">Cek Riwayat</a>

                    </div>
                </div>
            </div>

            <!-- Dropdown Template -->
            <div class="relative group mx-10">
                <a href="#" class="hover:text-teal-500 transition-colors flex items-center gap-1">
                    Informasi
                    <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" fill="currentColor" viewBox="0 0 256 256" class="transition-transform duration-300 group-hover:rotate-180">
                        <path d="M213.66,101.66l-80,80a8,8,0,0,1-11.32,0l-80-80A8,8,0,0,1,53.66,90.34L128,164.69l74.34-74.35a8,8,0,0,1,11.32,11.32Z"></path>
                    </svg>
                </a>
                <div class="absolute left-1/2 -translate-x-1/2 pt-2 opacity-0 invisible group-hover:opacity-100 group-hover:visible transition-all duration-300 z-50">
                    <div class="w-48 bg-white backdrop-blur-md rounded-2xl shadow-xl border border-white/20 py-2 overflow-hidden">
                        <a href="#" class="block px-4 py-2 rounded-3xl hover:bg-teal-500 hover:text-white transition-colors  active:bg-teal-700">Ketentuan Berobat</a>
                        <a href="#" class="block px-4 py-2 rounded-3xl hover:bg-teal-500 hover:text-white transition-colors  active:bg-teal-700">Jadwal Dokter</a>
                        <a href="#" class="block px-4 py-2 rounded-3xl hover:bg-teal-500 hover:text-white transition-colors  active:bg-teal-700">Tentang Poliklinik</a>
                    </div>
                </div>
            </div>

        </nav>

        <!-- Tombol Kontak -->
        <a href="#" class="smooth-corners-md rounded-3xl px-5 py-3 bg-teal-500 hover:bg-teal-600 text-white text-base font-['SF_Pro_Display']  transition-colors flex items-center gap-2">
            Kontak
            <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" fill="currentColor" viewBox="0 0 256 256">
                <path d="M222.37,158.46l-47.11-21.11-.13-.06a16,16,0,0,0-15.17,1.4,8.12,8.12,0,0,0-.75.56L134.87,160c-15.42-7.49-31.34-23.29-38.83-38.51l20.78-24.71c.2-.25.39-.5.57-.77a16,16,0,0,0,1.32-15.06l0-.12L97.54,33.64a16,16,0,0,0-16.62-9.52A56.26,56.26,0,0,0,32,80c0,79.4,64.6,144,144,144a56.26,56.26,0,0,0,55.88-48.92A16,16,0,0,0,222.37,158.46ZM176,208A128.14,128.14,0,0,1,48,80,40.2,40.2,0,0,1,82.87,40a.61.61,0,0,0,0,.12l21,47L83.2,111.86a6.13,6.13,0,0,0-.57.77,16,16,0,0,0-1,15.7c9.06,18.53,27.73,37.06,46.46,46.11a16,16,0,0,0,15.75-1.14,8.44,8.44,0,0,0,.74-.56L168.89,152l47,21.05h0s.08,0,.11,0A40.21,40.21,0,0,1,176,208Z"></path>
            </svg>
        </a>
    </div>
</div>