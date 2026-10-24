<?php

/**
 * Tabel Daftar Antrian Aktif (VALENIA-34). Read-only.
 * Lencana status memakai components/tag.php.
 *
 * @var array<int, array{no: string, nama: string, nim: string, poli: string, status: string}> $antreanAktif
 */
$antreanAktif = $antreanAktif ?? [];

// Teks status -> varian lencana di tag.php (positive hijau, negative oranye, disabled abu)
$varianStatus = [
    'Selesai'  => 'positive',
    'Dilayani' => 'negative',
    'Menunggu' => 'disabled',
];
?>
<section>
    <h2 class="mb-3 text-sm font-bold text-teal-700">Daftar Antrian Aktif</h2>
    <div class="smooth-corners-lg overflow-x-auto rounded-[32px] border border-white/70 bg-white/80 p-5 shadow-lg backdrop-blur-md">
        <table class="w-full min-w-[40rem] text-left text-sm">
            <thead>
                <tr class="text-slate-500">
                    <th class="px-4 py-2 font-medium">No</th>
                    <th class="px-4 py-2 font-medium">Nama</th>
                    <th class="px-4 py-2 font-medium">NIM/NIP</th>
                    <th class="px-4 py-2 font-medium">Poli</th>
                    <th class="px-4 py-2 text-center font-medium">Status</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-slate-200">
                <?php if ($antreanAktif === []): ?>
                    <tr>
                        <td colspan="5" class="px-4 py-6 text-center text-slate-500">Belum ada antrean aktif.</td>
                    </tr>
                <?php endif; ?>
                <?php foreach ($antreanAktif as $row): ?>
                    <?php
                    $status = $varianStatus[$row['status']] ?? 'disabled';
                    $label = $row['status'];
                    ?>
                    <tr class="text-slate-800">
                        <td class="px-4 py-3 font-medium"><?= e($row['no']) ?></td>
                        <td class="px-4 py-3"><?= e($row['nama']) ?></td>
                        <td class="px-4 py-3"><?= e($row['nim']) ?></td>
                        <td class="px-4 py-3"><?= e($row['poli']) ?></td>
                        <td class="px-4 py-3">
                            <div class="flex justify-center">
                                <?php include __DIR__ . '/tag.php'; ?>
                            </div>
                        </td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>
</section>