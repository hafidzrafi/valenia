<div class="flex flex-col relative w-full mx-auto h-100 bg-teal-500/90">

    <div class="flex flex-col absolute items-start gap-3 scale-115 p-10 pl-15">
        <div class="flex items-center gap-3">
            <div class="size-16 relative overflow-hidden flex items-center justify-center">
                <img src="/images/valenia-logo.svg" alt="Logo Perusahaan" class="w-full h-full object-contain brightness-0 invert" />
            </div>
            <span class="text-white text-2xl font-light font-stretch:expanded font-display">
                VALENIA
            </span>
        </div>
        <div class="flex flex-col grow-0 shrink-0  h-[32px]">
            <hr class="border-t-2 border-white w-full  -translate-y-2" />
            <p class="text-white">Verifikasi Antrean dan Layanan Navigasi Interaktif <br>
                Aplikasi Poliklinik Politeknik Negeri Malang</p>

        </div>
    </div>


    <div class="absolute top-8 right-8 flex flex-row gap-12 text-white">

        <div class="flex flex-col gap-4 w-[171px]">
            <h3 class="text-xl font-semibold text-white">Profil</h3>
            <ul class="flex flex-col gap-2 text-base font-medium opacity-80">
                <li><a href="#" class="hover:underline">Raja Lorem Ipsum</a></li>
                <li><a href="#" class="hover:underline">Raja Lorem Ipsum</a></li>
                <li><a href="#" class="hover:underline">Raja Lorem Ipsum</a></li>
                <li><a href="#" class="hover:underline">Raja Lorem Ipsum</a></li>
            </ul>
        </div>

        <div class="flex flex-col gap-4 w-[171px]">
            <h3 class="text-xl font-semibold text-white">Profil</h3>
            <ul class="flex flex-col gap-2 text-base font-medium opacity-80">
                <li><a href="#" class="hover:underline">Raja Lorem Ipsum</a></li>
                <li><a href="#" class="hover:underline">Raja Lorem Ipsum</a></li>
                <li><a href="#" class="hover:underline">Raja Lorem Ipsum</a></li>
                <li><a href="#" class="hover:underline">Raja Lorem Ipsum</a></li>
            </ul>
        </div>

    </div>

    <div class="flex justify-center items-center mt-auto w-full h-18 mx-auto bg-teal-700">
        <div class="flex justify-center items-center">
            <p class="text-center text-white">Copyright</p>
            <i class="ph ph-copyright text-white pl-1 pr-1 text-[20px]"></i>
            <p class="text-center text-white">2026.PBL-Kelompok1</p>
        </div>
    </div>

    <!-- kode radit -->
    <?php /* $u = current_user(); ?>
        <?php if ($u === null): ?>
            <a href="/login" class="smooth-corners-md rounded-3xl px-5 py-3 bg-teal-500 hover:bg-teal-600 text-white text-base font-display transition-colors">Masuk</a>
        <?php else: ?>
            <?php if (($u['role'] ?? '') === 'admin'): ?>
                <a href="/admin" class="smooth-corners-md rounded-3xl px-5 py-3 bg-teal-500 hover:bg-teal-600 text-white text-base font-display transition-colors">Admin</a>
            <?php endif; ?>
            <span class="text-white text-base font-display"><?= e($u['username']) ?></span>
            <form method="POST" action="/logout" class="inline">
                <input type="hidden" name="_token" value="<?= e(csrf_token()) ?>">
                <button type="submit" class="smooth-corners-md rounded-3xl px-5 py-3 bg-slate-900 hover:bg-slate-700 text-white text-base font-display transition-colors">Keluar</button>
            </form>
        <?php endif;  */ ?>