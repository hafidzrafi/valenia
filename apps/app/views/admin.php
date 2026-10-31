<?php
$antreanAktif = [
    ['no' => 'A-01', 'nama' => 'Raditya M.', 'nim' => '254107020102', 'poli' => 'Umum',      'status' => 'Selesai'],
    ['no' => 'A-02', 'nama' => 'Galuh p.',   'nim' => '254107020127', 'poli' => 'Umum',      'status' => 'Menunggu'],
    ['no' => 'A-03', 'nama' => 'Findi f.',   'nim' => '254107020016', 'poli' => 'Konseling', 'status' => 'Ditangani'],
    ['no' => 'A-04', 'nama' => 'M. hafidz',  'nim' => '254107020084', 'poli' => 'Konseling', 'status' => 'Selesai'],
];

$kehadiranMedis = [
    ['poli' => 'Poli Umum', 'jam' => '10:00:00 WIB', 'hadir' => true],
    ['poli' => 'Poli Gigi', 'jam' => '-',            'hadir' => false],
    ['poli' => 'Konseling', 'jam' => '10:00:00 WIB', 'hadir' => true],
];

$active = 'dashboard';
$token  = function_exists('csrf_token') ? csrf_token() : '';

$role      = $role ?? 'admin';
$adminNama = 'Petugas ' . ucfirst($role);
$adminUnit = 'Poliklinik Polinema';
?>
<div class="flex min-h-screen flex-col lg:flex-row">
    <?php include __DIR__ . '/../components/admin-sidebar.php'; ?>

    <div class="flex min-h-screen min-w-0 flex-1 flex-col"
         style="background: radial-gradient(circle at 85% 15%, rgba(160,215,205,0.7) 0%, transparent 50%), linear-gradient(135deg, #a9dcb0 0%, #88cebb 50%, #3fa896 100%);">

        <div class="mx-auto flex w-full max-w-[1080px] flex-1 flex-col px-8 pb-10">
            <?php include __DIR__ . '/../components/admin-header.php'; ?>

            <main class="mt-[21px]">
                <div class="flex flex-wrap items-end justify-between gap-x-8 gap-y-6">
                    <?php include __DIR__ . '/../components/admin-ringkasan-antrian.php'; ?>
                    <?php include __DIR__ . '/../components/admin-kehadiran-medis.php'; ?>
                </div>

                <div class="mt-8">
                    <?php include __DIR__ . '/../components/admin-tabel-antrean.php'; ?>
                </div>
            </main>
        </div>

        <?php include __DIR__ . '/../components/admin-footer.php'; ?>
    </div>
</div>