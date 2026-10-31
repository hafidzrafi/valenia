<?php
$adminNama = $adminNama ?? 'Petugas Admin';
$adminUnit = $adminUnit ?? 'Poliklinik Polinema';
?>
<header class="relative pt-[30px] font-['Poppins']">
    <h1 class="font-['Oswald'] text-[63px] font-bold leading-[75px] tracking-[0.5px] text-[#379a89]">Dashboard Admin</h1>
    <p class="text-[22px] font-medium leading-[30px] text-[#379a89]">Layanan Poliklinik Politeknik Negeri Malang</p>

    <div class="absolute right-0 top-[27px] flex h-[41px] w-[137px] items-center gap-[7px] rounded-full bg-white pl-[6px]">
        <div class="flex size-[30px] shrink-0 items-center justify-center rounded-full bg-teal-500">
            <i class="ph ph-user text-[18px] text-white"></i>
        </div>
        <div class="leading-tight">
            <p class="text-[11px] font-semibold text-teal-600"><?php echo e($adminNama); ?></p>
            <p class="text-[9px] text-teal-600"><?php echo e($adminUnit); ?></p>
        </div>
    </div>
</header>