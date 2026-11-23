<?php

/** @var string $token */
/** @var null|string $error */
?>
<div class="flex min-h-screen flex-col" style="background: url('/images/background.png') center / cover no-repeat;">

    <div class="flex flex-1 items-center justify-center px-4 py-10">
        <div class="w-[340px] rounded-[64px] border border-white/40
                    bg-gradient-to-br from-white/80 to-white/60 px-8 pb-10 pt-9 backdrop-blur-[10px]"
            style="box-shadow: -10px 15px 10px 0 rgba(0,0,0,0.1);">

            <h1 class="text-center font-display text-[32px] font-bold leading-tight text-slate-700">
                Ganti kata sandi
            </h1>
            <p class="mt-2 text-center text-sm text-slate-700">Akun ini wajib mengganti kata sandi sebelum melanjutkan</p>

            <?php if (!empty($error)) { ?>
                <p class="mt-4 rounded-2xl bg-red-50 px-4 py-2 text-center text-sm text-red-600">
                    <?php echo e($error); ?>
                </p>
            <?php } ?>

            <form method="POST" action="/ganti-sandi" class="mt-7 flex flex-col gap-4">
                <input type="hidden" name="_token" value="<?php echo e($token); ?>">
                <div class="flex flex-col gap-1.5">
                    <label class="text-[11px] font-semibold uppercase tracking-wide text-slate-800" for="kata_sandi_saat_ini">Kata sandi saat ini</label>
                    <input id="kata_sandi_saat_ini" type="password" name="kata_sandi_saat_ini" required
                        autocomplete="current-password" placeholder="xxxx-xxxx-xxxx"
                        class="w-full rounded-full border-[1.5px] border-slate-500 bg-transparent px-4 py-2.5
                               text-sm text-slate-700 placeholder:text-slate-400
                               focus:border-teal-500 focus:outline-none">
                </div>

                <div class="flex flex-col gap-1.5">
                    <label class="text-[11px] font-semibold uppercase tracking-wide text-slate-800" for="kata_sandi_baru">Kata sandi baru (min. 8 karakter)</label>
                    <input id="kata_sandi_baru" type="password" name="kata_sandi_baru" required
                        autocomplete="new-password" placeholder="xxxx-xxxx-xxxx"
                        class="w-full rounded-full border-[1.5px] border-slate-500 bg-transparent px-4 py-2.5
                               text-sm text-slate-700 placeholder:text-slate-400
                               focus:border-teal-500 focus:outline-none">
                </div>

                <div class="flex flex-col gap-1.5">
                    <label class="text-[11px] font-semibold uppercase tracking-wide text-slate-800" for="konfirmasi_kata_sandi">Konfirmasi kata sandi baru</label>
                    <input id="konfirmasi_kata_sandi" type="password" name="konfirmasi_kata_sandi" required
                        autocomplete="new-password" placeholder="xxxx-xxxx-xxxx"
                        class="w-full rounded-full border-[1.5px] border-slate-500 bg-transparent px-4 py-2.5
                               text-sm text-slate-700 placeholder:text-slate-400
                               focus:border-teal-500 focus:outline-none">
                </div>


                <div class="mt-4 flex justify-center">
                    <button type="submit"
                        class="h-[52px] w-[188px] rounded-3xl bg-teal-500 font-semibold text-white shadow-md
                               transition-colors hover:bg-teal-600">
                        Simpan
                    </button>
                </div>
            </form>
        </div>
    </div>

    <div>
        <?php include __DIR__ . '/../components/subfooter.php'; ?>
    </div>




                <?php /*
    <h1 class="text-xl font-bold">Ganti Kata Sandi</h1>
    <p class="mt-1 text-slate-600 text-sm">Akun ini wajib mengganti kata sandi sebelum melanjutkan.</p>


    <form method="POST" action="/ganti-sandi" class="mt-4 space-y-3 max-w-sm">
        <input type="hidden" name="_token" value="<?php echo e($token); ?>">
        <div>
            <label class="block text-sm" for="kata_sandi_saat_ini">Kata sandi saat ini</label>
            <input id="kata_sandi_saat_ini" type="password" name="kata_sandi_saat_ini" required
                autocomplete="current-password"
                class="mt-1 w-full border border-slate-300 rounded px-2 py-1">
        </div>
        <div>
            <label class="block text-sm" for="kata_sandi_baru">Kata sandi baru (min. 8 karakter)</label>
            <input id="kata_sandi_baru" type="password" name="kata_sandi_baru" required
                autocomplete="new-password"
                class="mt-1 w-full border border-slate-300 rounded px-2 py-1">
        </div>
        <div>
            <label class="block text-sm" for="konfirmasi_kata_sandi">Konfirmasi kata sandi baru</label>
            <input id="konfirmasi_kata_sandi" type="password" name="konfirmasi_kata_sandi" required
                autocomplete="new-password"
                class="mt-1 w-full border border-slate-300 rounded px-2 py-1">
        </div>
        <button type="submit" class="bg-slate-900 text-white rounded px-3 py-1">Simpan</button>
    </form>
    */?>
</div>