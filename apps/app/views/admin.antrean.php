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
<div class="flex min-h-screen flex-col lg:flex-row" x-data="{ confirmService: null, confirmCode: '' }">
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

                <div x-show="confirmService !== null" x-cloak
                    class="fixed inset-0 z-50 flex items-center justify-center bg-slate-900/50 p-4"
                    @keydown.escape.window="confirmService = null">
                    <div class="w-full max-w-[420px] rounded-[36px] bg-white px-8 py-7 text-center shadow-2xl"
                        @click.outside="confirmService = null">
                        <h3 class="font-display text-[28px] font-bold text-slate-900">Panggil Pasien</h3>
                        <div class="mx-auto mt-4 max-w-[260px] rounded-2xl bg-teal-50 px-4 py-3">
                            <p class="text-3xl font-black text-slate-900" x-text="confirmCode"></p>
                        </div>
                        <p class="mt-3 text-sm text-slate-600">
                            Nomor antrean akan diumumkan dan status pasien berubah menjadi Dilayani.
                        </p>
                        <div class="mt-6 flex items-center justify-center gap-3">
                            <button type="button" @click="confirmService = null"
                                class="inline-flex h-[40px] items-center rounded-full border border-slate-300 px-8 text-sm font-medium text-slate-600 transition-colors hover:bg-slate-100">
                                Batal
                            </button>
                            <button type="button"
                                @click="document.getElementById('panggil-' + confirmService).submit()"
                                class="h-[40px] rounded-full bg-teal-500 px-8 text-sm font-semibold text-white shadow transition-colors hover:bg-teal-600">
                                Panggil
                            </button>
                        </div>
                    </div>
                </div>

                <p class="mt-6 text-sm text-white/80">
                    <a href="/admin" class="underline">&larr; Kembali ke dashboard</a>
                </p>
            </main>
        </div>

        <?php include __DIR__.'/../components/admin-footer.php'; ?>
    </div>
</div>
