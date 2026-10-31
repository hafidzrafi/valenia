<?php
$antreanAktif = $antreanAktif ?? [];
$perPoli = array_count_values(array_column($antreanAktif, 'poli'));

$ringkasan = [
    ['label' => 'Total antrian', 'jumlah' => count($antreanAktif), 'icon' => 'ph-users'],
    ['label' => 'Poli Umum', 'jumlah' => $perPoli['Umum'] ?? 0, 'icon' => 'ph-stethoscope'],
    ['label' => 'Poli Gigi', 'jumlah' => $perPoli['Gigi'] ?? 0, 'icon' => 'ph-tooth'],
    ['label' => 'Konseling', 'jumlah' => $perPoli['Konseling'] ?? 0, 'icon' => 'ph-brain'],
];
?>
<section class="smooth-corners-lg h-[292px] w-[292px] shrink-0 border-2 border-white/60 bg-white/40 px-6 pt-[17px] shadow-[0_5px_18px_rgba(40,120,105,0.18)] backdrop-blur-md font-['Poppins']"
    style="border-radius: 60px;">
    <h2 class="mb-[14px] text-center font-['Oswald'] text-[28px] font-semibold leading-[34px] text-[#379a89]">Ringkasan Antrian</h2>
    <div class="grid grid-cols-2 gap-4">
        <?php foreach ($ringkasan as $item) { ?>
            <div class="flex h-[90px] items-center gap-[7px] bg-[#effcf8] pl-4" style="border-radius: 24px;">
                <div class="flex size-[35px] shrink-0 items-center justify-center rounded-full bg-[#d9f5ec]">
                    <i class="ph <?php echo e($item['icon']); ?> text-[24px] text-slate-900"></i>
                </div>
                <div>
                    <p class="h-[24px] w-min text-[10px] font-normal leading-[12px] text-slate-600"><?php echo e($item['label']); ?></p>
                    <p class="mt-1 text-base font-semibold leading-none text-slate-900"><?php echo (int) $item['jumlah']; ?></p>
                </div>
            </div>
        <?php } ?>
    </div>
</section>