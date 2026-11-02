<?php
$role = $role ?? 'admin';
$adminNama = $adminNama ?? 'Petugas '.ucfirst($role);
$adminUnit = $adminUnit ?? 'Poliklinik Polinema';
$dashboardTitle = $headerTitle ?? ('admin' === $role ? 'Dashboard Admin' : 'Dashboard Perawat');
$dashboardSubtitle = $headerSubtitle ?? 'Layanan Poliklinik Politeknik Negeri Malang';
?>
<header class="relative pt-[30px] font-sans">
    <h1 class="font-display text-[63px] font-bold leading-[75px] tracking-[0.5px] text-[#379a89]"><?php echo e($dashboardTitle); ?></h1>
    <p class="text-[22px] font-medium leading-[30px] text-[#379a89]"><?php echo e($dashboardSubtitle); ?></p>

    <div class="mt-4 flex h-[41px] w-[137px] items-center gap-[7px] rounded-full bg-white pl-[6px] sm:absolute sm:right-0 sm:top-[27px] sm:mt-0">
        <div class="flex size-[30px] shrink-0 items-center justify-center rounded-full bg-teal-500">
            <i class="ph ph-user text-[18px] text-white"></i>
        </div>
        <div class="leading-tight">
            <p class="text-[11px] font-semibold text-teal-600"><?php echo e($adminNama); ?></p>
            <p class="text-[9px] text-teal-600"><?php echo e($adminUnit); ?></p>
        </div>
    </div>
</header>
