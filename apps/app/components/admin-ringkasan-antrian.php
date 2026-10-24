<?php

/**
 * Kartu Ringkasan Antrian (VALENIA-34). Hanya menampilkan data (read-only).
 * Angka dihitung dari $antreanAktif, jadi tidak ada tombol ubah angka.
 *
 * @var array<int, array{poli: string}> $antreanAktif
 */
$antreanAktif = $antreanAktif ?? [];
$perPoli = array_count_values(array_column($antreanAktif, 'poli'));

$ringkasan = [
    ['label' => 'Total pasien', 'jumlah' => count($antreanAktif),       'icon' => 'ph-users'],
    ['label' => 'Poli Umum',    'jumlah' => $perPoli['Umum'] ?? 0,      'icon' => 'ph-stethoscope'],
    ['label' => 'Poli Gigi',    'jumlah' => $perPoli['Gigi'] ?? 0,      'icon' => 'ph-tooth'],
    ['label' => 'Konseling',    'jumlah' => $perPoli['Konseling'] ?? 0, 'icon' => 'ph-chats-circle'],
];
?>
<section class="smooth-corners-lg rounded-[32px] border border-white/70 bg-white/50 p-5 shadow-lg backdrop-blur-md">
    <h2 class="mb-4 text-center text-lg font-bold text-teal-700">Ringkasan Antrian</h2>
    <div class="grid grid-cols-2 gap-3">
        <?php foreach ($ringkasan as $item): ?>
            <div class="smooth-corners-md flex items-center gap-3 rounded-2xl bg-white/80 px-3 py-3 shadow-sm">
                <i class="ph <?= e($item['icon']) ?> text-3xl text-teal-600"></i>
                <div class="leading-tight">
                    <p class="text-xs text-slate-500"><?= e($item['label']) ?></p>
                    <p class="text-xl font-bold text-slate-800"><?= (int) $item['jumlah'] ?></p>
                </div>
            </div>
        <?php endforeach; ?>
    </div>
</section>