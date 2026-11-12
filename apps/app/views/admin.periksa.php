<?php
/** @var array<string, mixed> $visit */
/** @var string $title */
$role = $role ?? 'admin';
$active = 'input-hasil';
$headerTitle = 'Input Hasil Penanganan';
$headerSubtitle = 'Daftar antrian '.e((string) $visit['service_name']).' hari ini';
$adminNama = 'Petugas '.ucfirst($role);
$adminUnit = 'Poliklinik Polinema';

$doctor = $visit['doctor_on_duty'] ?? null;
$doctorName = is_array($doctor) ? (string) ($doctor['full_name'] ?? '') : '';

$statusLabel = [
    'menunggu' => 'Menunggu',
    'dilayani' => 'Dilayani',
    'selesai' => 'Selesai',
    'batal' => 'Dibatalkan',
    'expired' => 'Kedaluwarsa',
][(string) $visit['status']] ?? (string) $visit['status'];
?>
<div class="flex min-h-screen flex-col lg:flex-row" x-data="{ confirmOpen: false, confirmed: false }">
    <?php include __DIR__.'/../components/admin-sidebar.php'; ?>

    <div class="flex min-h-screen min-w-0 flex-1 flex-col"
         style="background: radial-gradient(circle at 85% 15%, rgba(160,215,205,0.7) 0%, transparent 50%), linear-gradient(135deg, #a9dcb0 0%, #88cebb 50%, #3fa896 100%);">

        <div class="mx-auto flex w-full max-w-[1080px] flex-1 flex-col px-8 pb-10">
            <?php include __DIR__.'/../components/admin-header.php'; ?>

            <main class="mt-[21px] flex flex-col gap-8 lg:flex-row lg:items-start">
                <?php include __DIR__.'/partials/admin-data-pasien.php'; ?>
                <?php include __DIR__.'/partials/admin-pemeriksaan-form.php'; ?>
            </main>

            <p class="mt-6 text-sm text-white/80">
                <a href="/admin" class="underline">&larr; Kembali ke dashboard</a>
            </p>
        </div>

        <div x-show="confirmOpen" x-cloak
            class="fixed inset-0 z-50 flex items-center justify-center bg-slate-900/50 p-4"
            @keydown.escape.window="confirmOpen = false">
            <div class="w-full max-w-[420px] rounded-[36px] bg-white px-8 py-7 text-center shadow-2xl"
                @click.outside="confirmOpen = false">
                <h3 class="font-display text-[28px] font-bold text-slate-900">Simpan Hasil?</h3>
                <p class="mt-3 text-sm text-slate-600">
                    Hasil penanganan akan masuk ke riwayat kunjungan pasien dan status
                    <?php echo e((string) ($visit['queue_code'] ?? '')); ?> menjadi Selesai.
                </p>
                <div class="mt-6 flex items-center justify-center gap-3">
                    <button type="button" @click="confirmOpen = false"
                        class="inline-flex h-[40px] items-center rounded-full border border-slate-300 px-8 text-sm font-medium text-slate-600 transition-colors hover:bg-slate-100">
                        Batal
                    </button>
                    <button type="button" @click="confirmed = true; document.getElementById('pemeriksaan-form').requestSubmit()"
                        class="h-[40px] rounded-full bg-teal-500 px-8 text-sm font-semibold text-white shadow transition-colors hover:bg-teal-600">
                        Simpan
                    </button>
                </div>
            </div>
        </div>

        <?php include __DIR__.'/../components/admin-footer.php'; ?>
    </div>
</div>
