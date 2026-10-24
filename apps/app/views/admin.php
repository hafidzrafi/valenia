<?php

/**
 * Dashboard admin (VALENIA-34).
 *
 * Dirender lewat view('admin') dari routes/admin.php, jadi sudah dibungkus
 * layout.php. JANGAN tambahkan <html>, <body>, atau ob_start() di sini.
 *
 * @var string $user
 * @var string $role
 * @var string $token
 */

// Data sementara (dummy) sampai integrasi database selesai.
// Repo ini publik, jadi jangan isi dengan nama atau NIM asli.
$antreanAktif = [
    ['no' => 'A-01', 'nama' => 'Pasien Contoh 1', 'nim' => '0000000001', 'poli' => 'Umum',      'status' => 'Selesai'],
    ['no' => 'A-02', 'nama' => 'Pasien Contoh 2', 'nim' => '0000000002', 'poli' => 'Umum',      'status' => 'Menunggu'],
    ['no' => 'A-03', 'nama' => 'Pasien Contoh 3', 'nim' => '0000000003', 'poli' => 'Konseling', 'status' => 'Dilayani'],
    ['no' => 'A-04', 'nama' => 'Pasien Contoh 4', 'nim' => '0000000004', 'poli' => 'Konseling', 'status' => 'Selesai'],
];

$kehadiranMedis = [
    ['poli' => 'Poli Umum', 'jam' => '08:00:00 WIB', 'hadir' => true],
    ['poli' => 'Poli Gigi', 'jam' => '08:00:00 WIB', 'hadir' => true],
    ['poli' => 'Konseling', 'jam' => '10:00:00 WIB', 'hadir' => true],
];

$active = 'dashboard';
?>
<div class="flex min-h-screen">
    <?php include __DIR__ . '/../components/admin-sidebar.php'; ?>

    <div class="flex min-w-0 flex-1 flex-col bg-linear-to-br from-teal-50 via-emerald-100 to-teal-300">
        <div class="flex-1 space-y-6 p-8">
            <header class="flex items-start justify-between gap-4">
                <div>
                    <h1 class="text-4xl font-bold text-teal-600">Dashboard Admin</h1>
                    <p class="mt-1 font-medium text-teal-600">Layanan Poliklinik Politeknik Negeri Malang</p>
                </div>
                <div class="smooth-corners-md flex items-center gap-3 rounded-2xl border border-white/70 bg-white/70 px-4 py-2 shadow">
                    <i class="ph ph-user-circle text-3xl text-teal-600"></i>
                    <div class="leading-tight">
                        <p class="text-sm font-semibold text-slate-800">Petugas <?= e(ucfirst($role)) ?></p>
                        <p class="text-xs text-slate-500"><?= e($user) ?></p>
                    </div>
                </div>
            </header>

            <div class="grid gap-6 lg:grid-cols-[2fr_3fr]">
                <?php include __DIR__ . '/../components/admin-ringkasan-antrian.php'; ?>
                <?php include __DIR__ . '/../components/admin-kehadiran-medis.php'; ?>
            </div>

            <?php include __DIR__ . '/../components/admin-tabel-antrean.php'; ?>
        </div>

        <footer class="flex h-14 items-center bg-teal-500 px-8 text-sm text-white">
            <p class="flex items-center">Copyright <i class="ph ph-copyright px-1 text-lg"></i> 2026.PBL-Kelompok1</p>
        </footer>
    </div>
</div>