<?php
$active = $active ?? 'dashboard';
$token = $token ?? '';
$role = $role ?? 'admin';

/**
 * Menu entries carry the roles that may see them. Entries without an href are
 * roadmap items: they stay visible, marked "Segera hadir", instead of pointing
 * at a fragment.
 */
$menu = [
    ['key' => 'dashboard', 'label' => 'Dashboard', 'icon' => 'ph-squares-four', 'href' => '/admin', 'roles' => ['admin', 'perawat']],
    ['key' => 'antrian', 'label' => 'Antrian', 'icon' => 'ph-users', 'href' => '/admin/antrean', 'roles' => ['admin', 'perawat']],
    ['key' => 'input-hasil', 'label' => 'Input Hasil', 'icon' => 'ph-clipboard-text', 'href' => '/admin/periksa', 'roles' => ['admin', 'perawat']],
    ['key' => 'jadwal-medis', 'label' => 'Jadwal Tenaga Medis', 'icon' => 'ph-calendar-dots', 'href' => null, 'roles' => ['admin', 'perawat']],
    ['key' => 'data-master', 'label' => 'Data Master', 'icon' => 'ph-database', 'href' => null, 'roles' => ['admin']],
    ['key' => 'rekap-bulanan', 'label' => 'Rekap Bulanan', 'icon' => 'ph-chart-bar', 'href' => null, 'roles' => ['admin']],
    ['key' => 'profil', 'label' => 'Profil', 'icon' => 'ph-user-circle', 'href' => null, 'roles' => ['admin']],
];

$kelola = [
    ['key' => 'akun', 'label' => 'Kelola Akun', 'icon' => 'ph-user-gear', 'href' => '/admin/pengguna', 'roles' => ['admin']],
    ['key' => 'sesi', 'label' => 'Kelola Sesi', 'icon' => 'ph-desktop', 'href' => '/admin/sesi', 'roles' => ['admin']],
];

$visible = static fn (array $item): bool => in_array($role, $item['roles'], true);
?>
<aside class="z-10 flex h-auto w-full shrink-0 flex-col justify-between bg-white px-[11px] pb-4 font-sans lg:sticky lg:top-0 lg:h-screen lg:w-64">
    <div>
        <div class="mb-[17px] flex items-center gap-4 pl-3 pt-6">
            <img src="/images/valenia-logo.svg" alt="VALENIA" class="h-[41px] w-[57px] object-contain"
                onerror="this.onerror=null;this.src='/images/logo.svg';">
            <span class="text-[26px] font-normal tracking-wide text-teal-500">VALENIA</span>
        </div>

        <nav class="flex flex-col gap-2.5">
            <?php foreach ($menu as $item) { ?>
                <?php if (!$visible($item)) {
                    continue;
                } ?>
                <?php $isActive = $item['key'] === $active; ?>
                <?php if (null === $item['href']) { ?>
                    <div class="flex h-[50px] cursor-not-allowed items-center gap-3 px-3 text-[14px] text-slate-400"
                        style="border-radius: 12px;" aria-disabled="true">
                        <i class="ph <?php echo e($item['icon']); ?> shrink-0 text-[24px]"></i>
                        <span class="min-w-0 flex-1 truncate"><?php echo e($item['label']); ?></span>
                        <span class="shrink-0 rounded-full bg-slate-100 px-[6px] py-[2px] text-[8px] font-medium text-slate-500">Soon</span>
                    </div>
                <?php } else { ?>
                    <a href="<?php echo e((string) $item['href']); ?>"
                        class="flex h-[50px] items-center gap-[15px] px-3 text-base transition-colors <?php echo $isActive ? 'bg-teal-500 text-white' : 'text-black hover:bg-teal-50 hover:text-teal-600'; ?>"
                        style="border-radius: 12px;"
                        <?php echo $isActive ? 'aria-current="page"' : ''; ?>>
                        <i class="ph <?php echo e($item['icon']); ?> text-[28px]"></i>
                        <span><?php echo e($item['label']); ?></span>
                    </a>
                <?php } ?>
            <?php } ?>
        </nav>

        <?php $kelolaVisible = array_values(array_filter($kelola, $visible)); ?>
        <?php if ([] !== $kelolaVisible) { ?>
            <p class="mt-6 mb-2 pl-3 text-[11px] font-semibold uppercase tracking-wide text-slate-400">Kelola</p>
            <nav class="flex flex-col gap-2.5">
                <?php foreach ($kelolaVisible as $item) { ?>
                    <?php $isActive = $item['key'] === $active; ?>
                    <a href="<?php echo e((string) $item['href']); ?>"
                        class="flex h-[50px] items-center gap-[15px] px-3 text-base transition-colors <?php echo $isActive ? 'bg-teal-500 text-white' : 'text-black hover:bg-teal-50 hover:text-teal-600'; ?>"
                        style="border-radius: 12px;"
                        <?php echo $isActive ? 'aria-current="page"' : ''; ?>>
                        <i class="ph <?php echo e($item['icon']); ?> text-[28px]"></i>
                        <span><?php echo e($item['label']); ?></span>
                    </a>
                <?php } ?>
            </nav>
        <?php } ?>
    </div>

    <form method="POST" action="/logout">
        <input type="hidden" name="_token" value="<?php echo e($token); ?>">
        <button type="submit"
            class="flex h-[52px] w-full items-center gap-3.5 bg-[#fbd5d5] px-3 text-base text-[#e0383e] transition-colors hover:bg-[#f8c2c2]"
            style="border-radius: 10px;">
            <i class="ph ph-sign-out text-[28px]"></i>
            <span>Keluar</span>
        </button>
    </form>
</aside>
