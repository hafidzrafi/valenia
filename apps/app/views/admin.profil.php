<?php
/** @var string $namaLengkap */
/** @var string $username */
/** @var string $email */
/** @var null|string $error */
/** @var null|string $flash */
$active = 'profil';
$token = $token ?? '';
$role = $role ?? 'admin';
$headerTitle = 'Profil Pengguna';
$adminNama = 'Petugas '.ucfirst($role);
$adminUnit = 'Poliklinik Polinema';

$error = $error ?? null;
$flash = $flash ?? null;
$sandiOpen = null !== $error && '' !== $error;
$nama = '' !== $namaLengkap ? $namaLengkap : $username;
$emailNip = '' !== $email ? $email : $username;
$peran = 'admin' === $role ? 'Admin' : 'Perawat';
?>
<div class="flex min-h-screen flex-col lg:flex-row">
    <?php include __DIR__.'/../components/admin-sidebar.php'; ?>

    <div class="flex min-h-screen min-w-0 flex-1 flex-col"
         style="background: radial-gradient(circle at 85% 15%, rgba(160,215,205,0.7) 0%, transparent 50%), linear-gradient(135deg, #a9dcb0 0%, #88cebb 50%, #3fa896 100%);">

        <div class="mx-auto flex w-full max-w-[1080px] flex-1 flex-col px-8 pb-10">
            <?php include __DIR__.'/../components/admin-header.php'; ?>

            <main class="mt-[21px]"
                  x-data="{ ubahOpen: false, sandiOpen: <?php echo $sandiOpen ? 'true' : 'false'; ?> }">
                <?php if (null !== $flash && '' !== $flash) { ?>
                    <div x-data="{ show: true }" x-init="setTimeout(() => show = false, 4000)" x-show="show" x-transition.opacity
                         class="fixed left-1/2 top-6 z-50 flex -translate-x-1/2 items-center gap-3 rounded-full bg-teal-500 px-6 py-3 font-sans text-white shadow-lg"
                         role="status">
                        <i class="ph ph-check text-[24px]"></i>
                        <p class="text-sm font-semibold"><?php echo e($flash); ?></p>
                    </div>
                <?php } ?>

                <div class="flex flex-col gap-6 lg:flex-row lg:items-start">
                    <section class="flex w-full shrink-0 flex-col items-center gap-1 rounded-2xl bg-white px-5 py-6 text-center font-sans shadow-[0_5px_18px_rgba(40,120,105,0.18)] lg:w-[230px]">
                        <span class="flex size-[52px] items-center justify-center rounded-full bg-teal-500 text-white">
                            <i class="ph ph-user text-[30px]"></i>
                        </span>
                        <p class="mt-2 text-sm font-semibold text-slate-800"><?php echo e($nama); ?></p>
                        <span class="rounded-full bg-teal-100 px-3 py-0.5 text-[10px] font-medium text-teal-700">Petugas <?php echo e($peran); ?></span>
                        <p class="text-[10px] text-slate-500">Poliklinik Politeknik Negeri Malang</p>
                    </section>

                    <div class="flex min-w-0 flex-1 flex-col gap-6">
                        <section class="rounded-2xl bg-white px-6 py-5 font-sans shadow-[0_5px_18px_rgba(40,120,105,0.18)]">
                            <h2 class="text-xl font-bold text-teal-700">Informasi Akun</h2>
                            <dl class="mt-3 grid gap-x-8 gap-y-4 sm:grid-cols-2">
                                <div>
                                    <dt class="text-[11px] text-slate-500">Nama</dt>
                                    <dd class="text-sm font-semibold text-slate-800"><?php echo e($nama); ?></dd>
                                </div>
                                <div>
                                    <dt class="text-[11px] text-slate-500">Email / NIP</dt>
                                    <dd class="text-sm font-semibold text-slate-800"><?php echo e($emailNip); ?></dd>
                                </div>
                                <div>
                                    <dt class="text-[11px] text-slate-500">Peran</dt>
                                    <dd class="text-sm font-semibold text-slate-800">Petugas <?php echo e($peran); ?></dd>
                                </div>
                                <div>
                                    <dt class="text-[11px] text-slate-500">Poliklinik</dt>
                                    <dd class="text-sm font-semibold text-slate-800">Poliklinik Polinema</dd>
                                </div>
                            </dl>
                            <button type="button" @click="ubahOpen = true"
                                class="mt-5 inline-flex h-8 items-center rounded-full bg-teal-500 px-5 text-xs font-semibold text-white transition-colors hover:bg-teal-600">
                                Ubah Profil
                            </button>
                        </section>

                        <section class="flex flex-wrap items-center justify-between gap-4 rounded-2xl bg-white px-6 py-5 font-sans shadow-[0_5px_18px_rgba(40,120,105,0.18)]">
                            <div>
                                <h2 class="text-xl font-bold text-teal-700">Keamanan</h2>
                                <p class="mt-1 text-xs text-slate-500">Ganti password secara berkala agar akun tetap aman.</p>
                            </div>
                            <button type="button" @click="sandiOpen = true"
                                class="inline-flex h-8 items-center rounded-full bg-teal-500 px-5 text-xs font-semibold text-white transition-colors hover:bg-teal-600">
                                Ganti Password
                            </button>
                        </section>
                    </div>
                </div>

                <!-- Dialog Ubah Profil (tampilan saja, belum ada fungsi simpan) -->
                <div x-show="ubahOpen" x-transition.opacity style="display: none;"
                     @click.self="ubahOpen = false" @keydown.escape.window="ubahOpen = false"
                     class="fixed inset-0 z-50 flex items-center justify-center bg-black/30 p-4"
                     role="dialog" aria-modal="true" aria-labelledby="judul-ubah-profil">
                    <div class="w-full max-w-[460px] rounded-[20px] bg-white p-6 font-sans shadow-xl">
                        <h2 id="judul-ubah-profil" class="text-2xl font-bold text-teal-700">Ubah Profil</h2>
                        <form class="mt-4 flex flex-col gap-4" @submit.prevent>
                            <div class="grid gap-3 sm:grid-cols-2">
                                <div class="flex flex-col gap-1">
                                    <label for="ubah-nama" class="text-xs font-semibold text-slate-800">Nama lengkap</label>
                                    <input id="ubah-nama" type="text" name="nama_lengkap" value="<?php echo e($nama); ?>"
                                        class="h-9 rounded-full border border-slate-300 bg-white px-4 text-xs text-slate-700 focus:border-teal-500 focus:outline-none">
                                </div>
                                <div class="flex flex-col gap-1">
                                    <label for="ubah-email" class="text-xs font-semibold text-slate-800">Email / NIP</label>
                                    <input id="ubah-email" type="text" name="email_nip" value="<?php echo e($emailNip); ?>"
                                        class="h-9 rounded-full border border-slate-300 bg-white px-4 text-xs text-slate-700 focus:border-teal-500 focus:outline-none">
                                </div>
                            </div>
                            <p class="text-[11px] text-slate-500">Peran dan poliklinik tidak bisa diubah di sini.</p>
                            <div class="flex justify-end gap-3">
                                <button type="button" @click="ubahOpen = false"
                                    class="h-9 rounded-full border border-slate-300 px-6 text-xs font-semibold text-slate-700 transition-colors hover:bg-slate-50">
                                    Batal
                                </button>
                                <button type="submit" disabled title="Segera hadir"
                                    class="h-9 rounded-full bg-teal-500 px-6 text-xs font-semibold text-white disabled:cursor-not-allowed disabled:opacity-60">
                                    Simpan Perubahan
                                </button>
                            </div>
                        </form>
                    </div>
                </div>

                <!-- Dialog Ganti Password -->
                <div x-show="sandiOpen" x-transition.opacity style="display: none;"
                     @click.self="sandiOpen = false" @keydown.escape.window="sandiOpen = false"
                     class="fixed inset-0 z-50 flex items-center justify-center bg-black/30 p-4"
                     role="dialog" aria-modal="true" aria-labelledby="judul-ganti-password">
                    <div class="w-full max-w-[420px] rounded-[20px] bg-white p-6 font-sans shadow-xl">
                        <h2 id="judul-ganti-password" class="text-2xl font-bold text-teal-700">Ganti Password</h2>

                        <?php if ($sandiOpen) { ?>
                            <p class="mt-3 rounded-xl bg-rose-50 px-3 py-2 text-xs text-rose-600"><?php echo e($error); ?></p>
                        <?php } ?>

                        <form method="POST" action="/admin/profil" class="mt-4 flex flex-col gap-3">
                            <input type="hidden" name="_token" value="<?php echo e($token); ?>">
                            <div class="flex flex-col gap-1">
                                <label for="kata_sandi_saat_ini" class="text-xs font-semibold text-slate-800">Password lama</label>
                                <input id="kata_sandi_saat_ini" type="password" name="kata_sandi_saat_ini" required
                                    autocomplete="current-password"
                                    class="h-9 rounded-full border border-slate-300 bg-white px-4 text-xs text-slate-700 focus:border-teal-500 focus:outline-none">
                            </div>
                            <div class="flex flex-col gap-1">
                                <label for="kata_sandi_baru" class="text-xs font-semibold text-slate-800">Password baru</label>
                                <input id="kata_sandi_baru" type="password" name="kata_sandi_baru" required minlength="8"
                                    autocomplete="new-password"
                                    class="h-9 rounded-full border border-slate-300 bg-white px-4 text-xs text-slate-700 focus:border-teal-500 focus:outline-none">
                            </div>
                            <div class="flex flex-col gap-1">
                                <label for="konfirmasi_kata_sandi" class="text-xs font-semibold text-slate-800">Konfirmasi password baru</label>
                                <input id="konfirmasi_kata_sandi" type="password" name="konfirmasi_kata_sandi" required minlength="8"
                                    autocomplete="new-password"
                                    class="h-9 rounded-full border border-slate-300 bg-white px-4 text-xs text-slate-700 focus:border-teal-500 focus:outline-none">
                            </div>
                            <p class="text-[11px] text-slate-500">Password minimal 8 karakter.</p>
                            <div class="mt-1 flex justify-end gap-3">
                                <button type="button" @click="sandiOpen = false"
                                    class="h-9 rounded-full border border-slate-300 px-6 text-xs font-semibold text-slate-700 transition-colors hover:bg-slate-50">
                                    Batal
                                </button>
                                <button type="submit"
                                    class="h-9 rounded-full bg-teal-500 px-6 text-xs font-semibold text-white transition-colors hover:bg-teal-600">
                                    Simpan Password
                                </button>
                            </div>
                        </form>
                    </div>
                </div>
            </main>
        </div>

        <?php include __DIR__.'/../components/admin-footer.php'; ?>
    </div>
</div>