<?php
/**
 * Empat kartu ringkasan laporan (VALENIA-42). Read-only.
 *
 * @var int $totalSemua
 * @var int $totalUmum
 * @var int $totalGigi
 * @var int $totalKonseling
 */
$ringkasan = [
    ['label' => 'Total antrian', 'jumlah' => $totalSemua, 'icon' => 'ph-users-three'],
    ['label' => 'Poli Umum', 'jumlah' => $totalUmum, 'icon' => 'ph-stethoscope'],
    ['label' => 'Poli Gigi', 'jumlah' => $totalGigi, 'icon' => 'ph-tooth'],
    ['label' => 'Konseling', 'jumlah' => $totalKonseling, 'icon' => 'ph-brain'],
];
?>
<section class="grid grid-cols-2 gap-6 lg:grid-cols-4" aria-label="Ringkasan laporan">
    <?php foreach ($ringkasan as $item) { ?>
        <div class="flex h-[78px] items-center gap-3 rounded-[28px] bg-white/90 px-4 font-sans shadow-[0_3px_10px_rgba(40,120,105,0.15)]">
            <span class="flex size-11 shrink-0 items-center justify-center rounded-full bg-teal-100 text-teal-700">
                <i class="ph <?php echo e($item['icon']); ?> text-[26px]"></i>
            </span>
            <div class="leading-tight">
                <p class="text-xs text-slate-600"><?php echo e($item['label']); ?></p>
                <p class="text-2xl font-bold text-slate-900"><?php echo (int) $item['jumlah']; ?></p>
            </div>
        </div>
    <?php } ?>
</section>