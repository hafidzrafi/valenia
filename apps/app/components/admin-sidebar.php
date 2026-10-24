<?php

/**
 * Sidebar admin (VALENIA-34): navigasi kiri untuk halaman admin.
 *
 * Dipanggil dari view admin dengan:
 *   include __DIR__ . '/../components/admin-sidebar.php';
 *
 * @var string $token  Token CSRF untuk form Keluar (dikirim oleh route /admin)
 * @var string $active Menu yang aktif, opsional. Default: 'dashboard'
 */
$active = $active ?? 'dashboard';
$token = $token ?? '';

// href '#' = halaman belum dibuat. Ganti kalau rutenya sudah ada.
$menu = [
    ['key' => 'dashboard',     'label' => 'Dashboard',     'icon' => 'ph-squares-four', 'href' => '/admin'],
    ['key' => 'antrian',       'label' => 'Antrian',       'icon' => 'ph-users-three',  'href' => '#'],
    ['key' => 'data-master',   'label' => 'Data Master',   'icon' => 'ph-database',     'href' => '#'],
    ['key' => 'rekap-bulanan', 'label' => 'Rekap Bulanan', 'icon' => 'ph-chart-bar',    'href' => '#'],
    ['key' => 'profil',        'label' => 'Profil',        'icon' => 'ph-user-circle',  'href' => '#'],
];
?>
<aside class="sticky top-0 flex h-screen w-56 shrink-0 flex-col bg-white p-4 shadow-lg">
    <div class="flex items-center gap-2 px-2 pb-6">
        <img src="/images/valenia-logo.svg" alt="VALENIA" class="size-9 object-contain">
        <span class="font-display text-xl font-medium tracking-wide text-teal-600">VALENIA</span>
    </div>

    <nav class="flex flex-col gap-1">
        <?php foreach ($menu as $item): ?>
            <?php $isActive = $item['key'] === $active; ?>
            <a href="<?= e($item['href']) ?>"
                class="smooth-corners-md flex items-center gap-3 rounded-2xl px-3 py-2.5 text-sm font-medium transition-colors <?= $isActive ? 'bg-teal-500 text-white' : 'text-slate-700 hover:bg-teal-50' ?>"
                <?= $isActive ? 'aria-current="page"' : '' ?>>
                <i class="ph <?= e($item['icon']) ?> text-xl"></i>
                <span><?= e($item['label']) ?></span>
            </a>
        <?php endforeach; ?>
    </nav>

    <!-- Keluar harus tetap form POST + token CSRF, bukan tautan biasa -->
    <form method="POST" action="/logout" class="mt-auto">
        <input type="hidden" name="_token" value="<?= e($token) ?>">
        <button type="submit"
            class="smooth-corners-md flex w-full items-center gap-3 rounded-2xl bg-red-100 px-3 py-2.5 text-sm font-medium text-red-500 transition-colors hover:bg-red-200">
            <i class="ph ph-sign-out text-xl"></i>
            <span>Keluar</span>
        </button>
    </form>
</aside>