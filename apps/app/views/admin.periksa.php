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
][(string) $visit['status']] ?? (string) $visit['status'];
?>
<div class="flex min-h-screen flex-col lg:flex-row">
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

        <?php include __DIR__.'/../components/admin-footer.php'; ?>
    </div>
</div>
