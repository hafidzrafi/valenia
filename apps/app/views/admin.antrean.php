<?php
/** @var list<array<string, mixed>> $boards */
/** @var null|string $flash */
$active = 'antrian';
$token = $token ?? '';
$role = $role ?? 'admin';
$headerTitle = 'Antrean Poliklinik';
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

                <p class="mb-4 text-sm text-white/80">
                    Tekan "Panggil berikutnya" untuk memulai layanan. Papan diperbarui otomatis setiap 5 detik.
                </p>

                <div id="queue-boards"
                     hx-get="/admin/antrean"
                     hx-trigger="every 5s"
                     hx-swap="innerHTML"
                     class="mt-6">
                    <?php include __DIR__.'/partials/queue-boards.php'; ?>
                </div>

                <p class="mt-6 text-sm text-white/80">
                    <a href="/admin" class="underline">&larr; Kembali ke dashboard</a>
                </p>
            </main>
        </div>

        <?php include __DIR__.'/../components/admin-footer.php'; ?>
    </div>
</div>
