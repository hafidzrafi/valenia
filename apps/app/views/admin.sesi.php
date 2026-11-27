<?php
/** @var list<array{id: string, user_id: string, username: string, role: string, ip_address: ?string, user_agent: ?string, created_at: string, last_seen_at: string, expires_at: string}> $sessions */
/** @var null|string $current */
/** @var string $token */
/** @var null|string $flash */
/** @var null|string $error */
$active = 'sesi';
$role = $role ?? 'admin';
$headerTitle = 'Kelola Sesi';
$headerSubtitle = 'Pantau dan cabut sesi login yang sedang aktif.';
$adminNama = 'Petugas '.ucfirst($role);
$adminUnit = 'Poliklinik Polinema';
$current = $current ?? null;
$flash = $flash ?? null;
$error = $error ?? null;

$bulan = [
    1 => 'Jan', 2 => 'Feb', 3 => 'Mar', 4 => 'Apr', 5 => 'Mei', 6 => 'Jun',
    7 => 'Jul', 8 => 'Agu', 9 => 'Sep', 10 => 'Okt', 11 => 'Nov', 12 => 'Des',
];

// Waktu terakhir aktif dalam zona Asia/Jakarta, contoh: "18 Nov 2026, 18.33".
$waktuTampil = static function (string $nilai) use ($bulan): string {
    try {
        $waktu = (new DateTimeImmutable($nilai))->setTimezone(new DateTimeZone('Asia/Jakarta'));
    } catch (Throwable) {
        return $nilai;
    }

    return $waktu->format('j').' '.$bulan[(int) $waktu->format('n')].' '.$waktu->format('Y, H.i');
};

// Menyederhanakan user agent menjadi "Browser, Sistem", contoh: "Chrome, Windows".
$perangkat = static function (?string $ua): string {
    $ua = (string) $ua;

    if ('' === $ua) {
        return '-';
    }

    $browser = match (true) {
        str_contains($ua, 'Edg/') => 'Edge',
        str_contains($ua, 'OPR/') => 'Opera',
        str_contains($ua, 'Firefox/') => 'Firefox',
        str_contains($ua, 'Chrome/') => 'Chrome',
        str_contains($ua, 'Safari/') => 'Safari',
        default => 'Browser lain',
    };

    $sistem = match (true) {
        str_contains($ua, 'Android') => 'Android',
        str_contains($ua, 'iPhone'), str_contains($ua, 'iPad') => 'iOS',
        str_contains($ua, 'Windows') => 'Windows',
        str_contains($ua, 'Mac OS X'), str_contains($ua, 'Macintosh') => 'macOS',
        str_contains($ua, 'Linux') => 'Linux',
        default => 'OS lain',
    };

    return $browser.', '.$sistem;
};
?>
<div class="flex min-h-screen flex-col lg:flex-row">
    <?php include __DIR__.'/../components/admin-sidebar.php'; ?>

    <div class="flex min-h-screen min-w-0 flex-1 flex-col"
         style="background: radial-gradient(circle at 85% 15%, rgba(160,215,205,0.7) 0%, transparent 50%), linear-gradient(135deg, #a9dcb0 0%, #88cebb 50%, #3fa896 100%);">

        <div class="mx-auto flex w-full max-w-[1080px] flex-1 flex-col px-8 pb-10">
            <?php include __DIR__.'/../components/admin-header.php'; ?>

            <main class="mt-[21px]"
                  x-data="{
                      cabutOpen: false,
                      cabutId: '',
                      cabutNama: '',
                      cabutPerangkat: '',
                      cabutSendiri: false,
                      pilih(data) {
                          this.cabutId = data.id;
                          this.cabutNama = data.nama;
                          this.cabutPerangkat = data.perangkat;
                          this.cabutSendiri = data.sendiri;
                          this.cabutOpen = true;
                      }
                  }">
                <?php if (null !== $flash && '' !== $flash) { ?>
                    <div x-data="{ show: true }" x-init="setTimeout(() => show = false, 4000)" x-show="show" x-transition.opacity
                         class="fixed left-1/2 top-6 z-50 flex -translate-x-1/2 items-center gap-3 rounded-full bg-teal-700 px-6 py-3 font-sans text-white shadow-lg"
                         role="status">
                        <i class="ph ph-check text-[24px]"></i>
                        <div class="leading-tight">
                            <p class="text-sm font-semibold"><?php echo e($flash); ?></p>
                            <p class="text-xs">Pengguna telah dikeluarkan dari sistem.</p>
                        </div>
                    </div>
                <?php } ?>

                <?php if (null !== $error && '' !== $error) { ?>
                    <p class="mb-4 rounded-2xl bg-rose-50 px-4 py-2 font-sans text-sm text-rose-700" role="alert">
                        <?php echo e($error); ?>
                    </p>
                <?php } ?>

                <section class="rounded-[28px] bg-white/90 px-7 py-6 font-sans shadow-[0_8px_24px_rgba(40,120,105,0.18)]">
                    <div class="overflow-x-auto">
                        <table class="w-full min-w-[640px] text-left text-sm text-slate-800">
                            <thead>
                                <tr class="border-b border-slate-700 text-xs text-slate-600">
                                    <th class="px-3 py-3 font-semibold">Pengguna</th>
                                    <th class="px-3 py-3 font-semibold">IP</th>
                                    <th class="px-3 py-3 font-semibold">Perangkat</th>
                                    <th class="px-3 py-3 font-semibold">Terakhir aktif</th>
                                    <th class="px-3 py-3 font-semibold">Aksi</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php foreach ($sessions as $row) { ?>
                                    <?php
                                    $sendiri = null !== $current && $row['id'] === $current;
                                    $data = [
                                        'id' => (string) $row['id'],
                                        'nama' => $row['username'],
                                        'perangkat' => $perangkat($row['user_agent'] ?? null),
                                        'sendiri' => $sendiri,
                                    ];
                                    ?>
                                    <tr class="border-b border-slate-200">
                                        <td class="px-3 py-4">
                                            <span class="font-semibold"><?php echo e($row['username']); ?></span>
                                            <span class="text-slate-600">(<?php echo e($row['role']); ?>)</span>
                                            <?php if ($sendiri) { ?>
                                                <span class="ml-1 rounded-full bg-teal-100 px-2.5 py-0.5 text-[11px] font-semibold text-teal-800">Sesi ini</span>
                                            <?php } ?>
                                        </td>
                                        <td class="px-3 py-4"><?php echo e((string) ($row['ip_address'] ?? '-')); ?></td>
                                        <td class="px-3 py-4"><?php echo e($perangkat($row['user_agent'] ?? null)); ?></td>
                                        <td class="px-3 py-4"><?php echo e($waktuTampil($row['last_seen_at'])); ?></td>
                                        <td class="px-3 py-4">
                                            <button type="button" @click="pilih(<?php echo e((string) json_encode($data)); ?>)"
                                                class="h-9 rounded-full border border-rose-300 bg-white px-5 text-xs font-semibold text-rose-700 transition-colors hover:bg-rose-50">
                                                Cabut
                                            </button>
                                        </td>
                                    </tr>
                                <?php } ?>
                                <?php if ([] === $sessions) { ?>
                                    <tr><td colspan="5" class="px-3 py-6 text-center text-slate-500">Tidak ada sesi aktif.</td></tr>
                                <?php } ?>
                            </tbody>
                        </table>
                    </div>
                    <p class="mt-4 text-xs text-slate-600">Mencabut sesi akan mengeluarkan pengguna dari sistem di perangkat tersebut.</p>
                </section>

                <!-- Dialog konfirmasi cabut sesi -->
                <div x-show="cabutOpen" x-transition.opacity style="display: none;"
                     @click.self="cabutOpen = false" @keydown.escape.window="cabutOpen = false"
                     class="fixed inset-0 z-50 flex items-center justify-center bg-black/30 p-4"
                     role="dialog" aria-modal="true" aria-labelledby="judul-cabut-sesi">
                    <div class="w-full max-w-[440px] rounded-[20px] bg-white p-6 font-sans shadow-xl">
                        <h2 id="judul-cabut-sesi" class="text-2xl font-bold text-teal-700">Cabut sesi?</h2>
                        <p class="mt-2 text-sm text-slate-600">
                            Pengguna <b class="text-slate-800" x-text="cabutNama"></b> akan dikeluarkan dari sistem di perangkat
                            <span x-text="cabutPerangkat"></span>.
                        </p>
                        <p x-show="cabutSendiri" class="mt-2 rounded-xl bg-amber-50 px-3 py-2 text-xs text-amber-800" style="display: none;">
                            Ini sesimu sendiri. Kamu akan keluar dari sistem dan diarahkan ke halaman login.
                        </p>
                        <form method="POST" action="/admin/sesi" class="mt-5 flex justify-end gap-3">
                            <input type="hidden" name="_token" value="<?php echo e($token); ?>">
                            <input type="hidden" name="id" :value="cabutId">
                            <button type="button" @click="cabutOpen = false"
                                class="h-10 rounded-full border border-slate-300 px-6 text-xs font-semibold text-slate-700 transition-colors hover:bg-slate-50">
                                Batal
                            </button>
                            <button type="submit"
                                class="h-10 rounded-full bg-rose-700 px-6 text-xs font-semibold text-white transition-colors hover:bg-rose-800">
                                Cabut Sesi
                            </button>
                        </form>
                    </div>
                </div>
            </main>
        </div>

        <?php include __DIR__.'/../components/admin-footer.php'; ?>
    </div>
</div>
