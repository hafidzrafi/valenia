<?php
/**
 * Tabel rekap per minggu + tombol Unduh Laporan (VALENIA-42). Read-only.
 *
 * @var list<array{label: string, umum: int, gigi: int, konseling: int}> $minggu
 * @var int $totalUmum
 * @var int $totalGigi
 * @var int $totalKonseling
 * @var int $totalSemua
 */
?>
<section class="rounded-[40px] border-2 border-white/60 bg-white/70 p-6 font-sans shadow-[0_5px_18px_rgba(40,120,105,0.18)] backdrop-blur-md">
    <div class="mb-3 flex justify-end">
        <button type="button" @click="unduhOpen = true"
            class="inline-flex h-8 items-center gap-2 rounded-full bg-teal-500 px-4 text-xs font-medium text-white transition-colors hover:bg-teal-600">
            Unduh Laporan
            <i class="ph ph-download-simple text-[14px]"></i>
        </button>
    </div>

    <div class="overflow-x-auto">
        <table class="w-full min-w-[560px] text-left text-sm text-slate-800">
            <thead>
                <tr class="border-b border-slate-800 text-slate-600">
                    <th class="px-4 py-3 font-medium">Periode</th>
                    <th class="px-4 py-3 font-medium">Poli Umum</th>
                    <th class="px-4 py-3 font-medium">Poli Gigi</th>
                    <th class="px-4 py-3 font-medium">Konseling</th>
                    <th class="px-4 py-3 font-medium">Total</th>
                </tr>
            </thead>
            <tbody>
                <?php if ([] === $minggu) { ?>
                    <tr>
                        <td colspan="5" class="px-4 py-6 text-center text-slate-500">Belum ada data laporan.</td>
                    </tr>
                <?php } ?>
                <?php foreach ($minggu as $baris) { ?>
                    <tr class="border-b border-slate-800">
                        <td class="px-4 py-3"><?php echo e($baris['label']); ?></td>
                        <td class="px-4 py-3"><?php echo (int) $baris['umum']; ?></td>
                        <td class="px-4 py-3"><?php echo (int) $baris['gigi']; ?></td>
                        <td class="px-4 py-3"><?php echo (int) $baris['konseling']; ?></td>
                        <td class="px-4 py-3"><?php echo (int) ($baris['umum'] + $baris['gigi'] + $baris['konseling']); ?></td>
                    </tr>
                <?php } ?>
                <tr class="font-semibold">
                    <td class="px-4 py-3">Total</td>
                    <td class="px-4 py-3"><?php echo $totalUmum; ?></td>
                    <td class="px-4 py-3"><?php echo $totalGigi; ?></td>
                    <td class="px-4 py-3"><?php echo $totalKonseling; ?></td>
                    <td class="px-4 py-3"><?php echo $totalSemua; ?></td>
                </tr>
            </tbody>
        </table>
    </div>
</section>