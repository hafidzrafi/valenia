<?php
/** @var string $token */
/** @var null|string $error */
/** @var null|string $username */
?>
<div class="flex min-h-screen flex-col" style="background: url('/images/background.png') center / cover no-repeat;">

    <div class="flex w-full justify-center pt-8">
        <?php include __DIR__.'/../components/login-nav.php'; ?>
    </div>

    <div class="flex flex-1 items-center justify-center px-4 py-10">
        <div class="w-[340px] rounded-[64px] border border-white/40
                    bg-gradient-to-br from-white/80 to-white/60 px-8 pb-10 pt-9 backdrop-blur-[10px]"
            style="box-shadow: -10px 15px 10px 0 rgba(0,0,0,0.1);">

            <h1 class="text-center font-display text-[32px] font-bold leading-tight text-slate-700">
                Login Petugas
            </h1>
            <p class="mt-2 text-center text-sm text-slate-700">Masuk untuk mengelola antrian</p>

            <?php if (!empty($error)) { ?>
                <p class="mt-4 rounded-2xl bg-red-50 px-4 py-2 text-center text-sm text-red-600">
                    <?php echo e($error); ?>
                </p>
            <?php } ?>

            <form method="POST" action="/login" class="mt-7 flex flex-col gap-4">
                <input type="hidden" name="_token" value="<?php echo e($token); ?>">

                <div class="flex flex-col gap-1.5">
                    <label for="username" class="text-[11px] font-semibold uppercase tracking-wide text-slate-800">
                        Email / NIP
                    </label>
                    <input id="username" type="text" name="username" required autocomplete="username"
                        value="<?php echo e($username ?? ''); ?>"
                        placeholder="xxxx - xxxx - xxxx"
                        class="w-full rounded-full border-[1.5px] border-slate-500 bg-transparent px-4 py-2.5
                               text-sm text-slate-700 placeholder:text-slate-400
                               focus:border-teal-500 focus:outline-none">
                </div>

                <div class="flex flex-col gap-1.5">
                    <label for="password" class="text-[11px] font-semibold uppercase tracking-wide text-slate-800">
                        Kata Sandi
                    </label>
                    <input id="password" type="password" name="password" required autocomplete="current-password"
                        placeholder="Ubah kata sandi"
                        class="w-full rounded-full border-[1.5px] border-slate-500 bg-transparent px-4 py-2.5
                               text-sm text-slate-700 placeholder:text-slate-400
                               focus:border-teal-500 focus:outline-none">
                </div>

                <div class="mt-4 flex justify-center">
                    <button type="submit"
                        class="h-[52px] w-[188px] rounded-3xl bg-teal-500 font-semibold text-white shadow-md
                               transition-colors hover:bg-teal-600">
                        Masuk
                    </button>
                </div>

                <p class="mt-3 text-center text-sm font-medium text-teal-500">
                    Lupa Kata Sandi?
                </p>
                <?php /* No self service reset exists yet. Admins can force a reset from /admin/pengguna; a public reset flow is a separate ticket. */ ?>
            </form>
        </div>
    </div>

    <div>
        <?php include __DIR__.'/../components/subfooter.php'; ?>
    </div>
</div>
