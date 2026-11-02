<?php
/** @var array<string, mixed> $visit */
/** @var null|array<string, mixed> $exam */
$role = $role ?? 'admin';
$active = 'antrian';
$headerTitle = 'Detail Kunjungan';
$headerSubtitle = 'Hasil penanganan tercatat di bawah ini';
$adminNama = 'Petugas '.ucfirst($role);
$adminUnit = 'Poliklinik Polinema';

$exam = $exam ?? [];
?>
<div class="flex min-h-screen flex-col lg:flex-row">
    <?php include __DIR__.'/../components/admin-sidebar.php'; ?>

    <div class="flex min-h-screen min-w-0 flex-1 flex-col"
         style="background: radial-gradient(circle at 85% 15%, rgba(160,215,205,0.7) 0%, transparent 50%), linear-gradient(135deg, #a9dcb0 0%, #88cebb 50%, #3fa896 100%);">

        <div class="mx-auto flex w-full max-w-[1080px] flex-1 flex-col px-8 pb-10">
            <?php include __DIR__.'/../components/admin-header.php'; ?>

            <main class="mt-[21px] flex flex-col gap-8">
                <?php if (null !== ($flash ?? null) && '' !== $flash) { ?>
                    <p class="rounded-2xl bg-emerald-50 px-4 py-2 text-sm text-emerald-700">
                        <?php echo e($flash); ?>
                    </p>
                <?php } ?>

                <?php include __DIR__.'/partials/admin-data-pasien.php'; ?>

                <section class="w-full max-w-[560px] rounded-[60px] border-2 border-white/60 bg-white/80 px-8 py-7 shadow-[0_5px_18px_rgba(40,120,105,0.18)] backdrop-blur-md font-sans">
                    <div class="mb-5">
                        <p class="text-[11px] text-slate-500">Diagnosis / hasil pemeriksaan</p>
                        <p class="mt-1 font-semibold text-slate-900">
                            <?php echo e('' !== (string) ($exam['diagnosis'] ?? '') ? (string) $exam['diagnosis'] : '-'); ?>
                        </p>
                    </div>
                    <div class="mb-5">
                        <p class="text-[11px] text-slate-500">Tindakan / penanganan</p>
                        <p class="mt-1 font-semibold text-slate-900">
                            <?php echo e('' !== (string) ($exam['treatment'] ?? '') ? (string) $exam['treatment'] : '-'); ?>
                        </p>
                    </div>
                    <div class="mb-5">
                        <p class="text-[11px] text-slate-500">Catatan tambahan</p>
                        <p class="mt-1 font-semibold text-slate-900">
                            <?php echo e('' !== (string) ($exam['note'] ?? '') ? (string) $exam['note'] : '-'); ?>
                        </p>
                    </div>
                    <div class="flex justify-end">
                        <a href="/admin"
                            class="inline-flex h-[40px] items-center rounded-full bg-[#d9d9d9] px-8 text-sm font-medium text-[#666] transition-colors hover:bg-slate-300">
                            Tutup
                        </a>
                    </div>
                </section>
            </main>
        </div>

        <?php include __DIR__.'/../components/admin-footer.php'; ?>
    </div>
</div>
