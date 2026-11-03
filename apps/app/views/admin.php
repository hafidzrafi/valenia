<?php
/** @var list<array<string, mixed>> $visits */
/** @var array{total: int, by_service: array<string, int>, by_status: array<string, int>} $summary */
/** @var list<array<string, mixed>> $attendance */
/** @var null|string $flash */
$active = 'dashboard';
$token = $token ?? '';

$role = $role ?? 'admin';
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
                <?php if (null !== ($flash ?? null) && '' !== $flash) { ?>
                    <p class="mb-4 rounded-2xl bg-emerald-50 px-4 py-2 text-sm text-emerald-700">
                        <?php echo e($flash); ?>
                    </p>
                <?php } ?>

                <div class="mt-2">
                    <?php include __DIR__.'/../components/admin-ringkasan-antrian.php'; ?>
                </div>

                <div class="mt-8 flex flex-col gap-8 lg:flex-row lg:items-start">
                    <?php include __DIR__.'/../components/admin-sedang-ditangani.php'; ?>
                    <?php include __DIR__.'/../components/admin-status-tugas.php'; ?>
                </div>

                <div class="mt-8">
                    <?php include __DIR__.'/../components/admin-tabel-antrean.php'; ?>
                </div>

                <div class="mt-8">
                    <?php include __DIR__.'/../components/admin-kehadiran-medis.php'; ?>
                </div>

                <?php include __DIR__.'/../components/admin-aktivitas-antrian.php'; ?>
            </main>
        </div>

        <?php include __DIR__.'/../components/admin-footer.php'; ?>
    </div>
</div>
