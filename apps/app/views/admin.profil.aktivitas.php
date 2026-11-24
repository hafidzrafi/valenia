<?php
/** @var list<array{label: string, baris: list<array{ikon: string, judul: string, detail: string, waktu: string}>}> $kelompok */
$active = 'profil';
$token = $token ?? '';
$role = $role ?? 'admin';
$headerTitle = 'Profil Pengguna';
$adminNama = 'Petugas '.ucfirst($role);
$adminUnit = 'Poliklinik Polinema';
?>
<div class="flex min-h-screen flex-col lg:flex-row">
    <?php include __DIR__.'/../components/admin-sidebar.php'; ?>

    <div class="flex min-h-screen min-w-0 flex-1 flex-col"
         style="background: radial-gradient(circle at 85% 15%, rgba(160,215,205,0.7) 0%, transparent 50%), linear-gradient(135deg, #a9dcb0 0%, #88cebb 50%, #3fa896 100%);">

        <div class="mx-auto flex w-full max-w-[1080px] flex-1 flex-col px-8 pb-10">
            <?php include __DIR__.'/../components/admin-header.php'; ?>

            <main class="mt-[21px]">
                <section class="w-full max-w-[860px] rounded-2xl bg-white px-6 py-5 font-sans shadow-[0_5px_18px_rgba(40,120,105,0.18)]">
                    <h2 class="text-xl font-bold text-teal-700">Riwayat Aktivitas</h2>
                    <p class="mt-1 text-xs text-slate-500">Seluruh catatan aktivitas akun Anda di sistem.</p>

                    <?php if ([] === $kelompok) { ?>
                        <p class="mt-4 border-t border-slate-200 py-6 text-center text-sm text-slate-500">Belum ada aktivitas.</p>
                    <?php } ?>

                    <?php foreach ($kelompok as $grup) { ?>
                        <h3 class="mt-5 text-[11px] font-semibold uppercase tracking-wide text-slate-500"><?php echo e($grup['label']); ?></h3>
                        <ul class="mt-1">
                            <?php foreach ($grup['baris'] as $baris) { ?>
                                <?php include __DIR__.'/../components/admin-profil-aktivitas-baris.php'; ?>
                            <?php } ?>
                        </ul>
                    <?php } ?>
                </section>
            </main>
        </div>

        <?php include __DIR__.'/../components/admin-footer.php'; ?>
    </div>
</div>
