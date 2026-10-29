<?php
$active = $active ?? 'dashboard';
$token = $token ?? '';

$menu = [
    ['key' => 'dashboard',     'label' => 'Dashboard',           'icon' => 'ph-squares-four',   'href' => '/admin'],
    ['key' => 'antrian',       'label' => 'Antrian',             'icon' => 'ph-users',          'href' => '#'],
    ['key' => 'data-master',   'label' => 'Data Master',         'icon' => 'ph-database',       'href' => '#'],
    ['key' => 'rekap-bulanan', 'label' => 'Rekap Bulanan',       'icon' => 'ph-chart-bar',      'href' => '#'],
    ['key' => 'jadwal-medis',  'label' => 'Jadwal Tenaga Medis', 'icon' => 'ph-calendar-dots',  'href' => '#'],
    ['key' => 'profil',        'label' => 'Profil',              'icon' => 'ph-user-circle',    'href' => '#'],
];
?>
<aside class="sticky top-0 z-10 flex h-screen w-64 shrink-0 flex-col justify-between bg-white px-[11px] pb-4 font-['Poppins']">
    <div>
        <!-- Logo -->
        <div class="mb-[17px] flex items-center gap-4 pl-3 pt-6">
            <img src="/images/valenia-logo.svg" alt="VALENIA" class="h-[41px] w-[57px] object-contain"
                onerror="this.onerror=null;this.src='/images/logo.svg';">
            <span class="text-[26px] font-normal tracking-wide text-teal-500">VALENIA</span>
        </div>

        <!-- Menu -->
        <nav class="flex flex-col gap-2.5">
            <?php foreach ($menu as $item): ?>
                <?php $isActive = $item['key'] === $active; ?>
                <a href="<?= e($item['href']) ?>"
                    class="flex h-[50px] items-center gap-[15px] px-3 text-base text-black transition-colors <?= $isActive ? 'bg-teal-500 text-white' : 'hover:bg-teal-50 hover:text-teal-600' ?>"
                    style="border-radius: 12px;"
                    <?= $isActive ? 'aria-current="page"' : '' ?>>
                    <i class="ph <?= e($item['icon']) ?> text-[28px]"></i>
                    <span><?= e($item['label']) ?></span>
                </a>
            <?php endforeach; ?>
        </nav>
    </div>

    <!-- Keluar -->
    <form method="POST" action="/logout">
        <input type="hidden" name="_token" value="<?= e($token) ?>">
        <button type="submit"
            class="flex h-[52px] w-full items-center gap-3.5 bg-[#fbd5d5] px-3 text-base text-[#e0383e] transition-colors hover:bg-[#f8c2c2]"
            style="border-radius: 10px;">
            <i class="ph ph-sign-out text-[28px]"></i>
            <span>Keluar</span>
        </button>
    </form>
</aside>