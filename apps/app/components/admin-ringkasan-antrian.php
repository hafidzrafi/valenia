<?php
/** @var array{total: int, by_service: array<string, int>, by_status: array<string, int>} $summary */
$byStatus = $summary['by_status'];

$ringkasan = [
    ['label' => 'Total antrian', 'jumlah' => $summary['total'], 'icon' => 'ph-users'],
    ['label' => 'Menunggu', 'jumlah' => $byStatus['menunggu'], 'icon' => 'ph-hourglass-medium'],
    ['label' => 'Ditangani', 'jumlah' => $byStatus['dilayani'], 'icon' => 'ph-stethoscope'],
    ['label' => 'Selesai', 'jumlah' => $byStatus['selesai'], 'icon' => 'ph-check-circle'],
];
?>
<div class="flex flex-wrap gap-5 font-sans">
    <?php foreach ($ringkasan as $item) { ?>
        <div class="flex min-w-[228px] flex-1 items-center gap-4 rounded-full bg-white/90 py-5 pl-6 pr-10 shadow-[0_5px_18px_rgba(40,120,105,0.18)] backdrop-blur-md">
            <div class="flex size-[52px] shrink-0 items-center justify-center rounded-full bg-[#d9f5ec]">
                <i class="ph <?php echo e($item['icon']); ?> text-[30px] text-slate-900"></i>
            </div>
            <div>
                <p class="text-sm font-normal text-slate-600"><?php echo e($item['label']); ?></p>
                <p class="text-[28px] font-bold leading-tight text-slate-900"><?php echo (int) $item['jumlah']; ?></p>
            </div>
        </div>
    <?php } ?>
</div>
