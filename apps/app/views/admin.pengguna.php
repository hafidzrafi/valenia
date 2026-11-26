<?php
/** @var list<array<string, mixed>> $users */
/** @var string $token */
/** @var string $role */
/** @var string $me */
/** @var null|string $flash */
/** @var null|string $error */
/** @var null|array{nama: string, password: string} $tempPassword */
/** @var null|array<string, mixed> $form */
/** @var int $total */
/** @var int $aktif */
/** @var int $nonaktif */
/** @var int $cocok */
/** @var string $q */
/** @var string $peran */
/** @var int $halaman */
/** @var int $halamanTotal */
/** @var int $perHalaman */
$active = 'akun';
$headerTitle = 'Kelola Akun';
$headerSubtitle = 'Layanan Poliklinik Politeknik Negeri Malang';
$adminNama = 'Petugas '.ucfirst($role);
$adminUnit = 'Poliklinik Polinema';

$labelPeran = ['admin' => 'Admin', 'perawat' => 'Perawat'];
$kelasPeran = [
    'admin' => 'bg-teal-100 text-teal-700',
    'perawat' => 'bg-indigo-100 text-indigo-600',
];

$inisial = static function (string $nama): string {
    $kata = preg_split('/\s+/', trim($nama)) ?: [];
    $huruf = '';

    foreach ($kata as $satu) {
        $huruf .= mb_substr($satu, 0, 1);

        if (mb_strlen($huruf) >= 2) {
            break;
        }
    }

    return mb_strtoupper($huruf);
};

$baris = array_map(static function (array $row) use ($me, $inisial): array {
    $nama = (string) $row['full_name'];
    $email = (string) ($row['email'] ?? '');
    $sendiri = (string) $row['id'] === $me;

    return [
        'id' => (string) $row['id'],
        'nama' => $nama,
        'inisial' => $inisial($nama),
        'username' => (string) $row['username'],
        'email' => $email,
        'peran' => (string) $row['role'],
        'aktif' => (bool) $row['is_active'],
        'sendiri' => $sendiri,
        'keterangan' => $sendiri ? 'Akun Anda' : ('' === $email ? 'Belum ada email' : $email),
    ];
}, $users);

$awal = [] === $baris ? 0 : ($halaman - 1) * $perHalaman + 1;
$akhir = ($halaman - 1) * $perHalaman + count($baris);

$tautanHalaman = static function (int $nomor) use ($q, $peran): string {
    $params = ['halaman' => $nomor];

    if ('' !== $q) {
        $params['q'] = $q;
    }

    if ('semua' !== $peran) {
        $params['peran'] = $peran;
    }

    return '/admin/pengguna?'.http_build_query($params);
};

$inputKelas = 'h-10 rounded-full border border-slate-500 px-4 text-[12px] font-normal text-slate-800 transition-colors hover:border-teal-600 focus:border-teal-600 focus:outline-none focus-visible:ring-2 focus-visible:ring-teal-600';
$labelKelas = 'flex flex-col gap-1 text-[11px] font-semibold text-slate-800';
$batalKelas = 'h-9 rounded-full bg-white px-6 text-xs font-medium text-teal-700 shadow-md transition-colors hover:bg-teal-50 focus:outline-none focus-visible:ring-2 focus-visible:ring-teal-600';
$simpanKelas = 'h-9 rounded-full bg-teal-700 px-6 text-xs font-medium text-white transition-colors hover:bg-teal-800 focus:outline-none focus-visible:ring-2 focus-visible:ring-teal-600';

// A rejected submission reopens its dialog with the values that were typed.
$formTambah = 'tambah' === ($form['aksi'] ?? '') ? $form : null;
$formUbah = 'ubah' === ($form['aksi'] ?? '') ? $form : null;

// Every action form carries the current filter so the redirect returns to it.
$konteks = '';

if ('' !== $q) {
    $konteks .= '<input type="hidden" name="q" value="'.e($q).'">';
}

if ('semua' !== $peran) {
    $konteks .= '<input type="hidden" name="peran" value="'.e($peran).'">';
}

if ($halaman > 1) {
    $konteks .= '<input type="hidden" name="halaman" value="'.(int) $halaman.'">';
}
?>
<div class="flex min-h-screen flex-col lg:flex-row"
     x-data="{
         tambah: <?php echo null === $formTambah ? 'null' : e(json_encode($formTambah, JSON_UNESCAPED_UNICODE)); ?>,
         edit: <?php echo null === $formUbah ? 'null' : e(json_encode($formUbah, JSON_UNESCAPED_UNICODE)); ?>,
         reset: null,
         konfirmasi: null,
         tersalin: false,
         temp: <?php echo null === $tempPassword ? 'null' : e(json_encode($tempPassword)); ?>
     }">
    <?php include __DIR__.'/../components/admin-sidebar.php'; ?>

    <div class="flex min-h-screen min-w-0 flex-1 flex-col"
         style="background: radial-gradient(circle at 85% 15%, rgba(160,215,205,0.7) 0%, transparent 50%), linear-gradient(135deg, #a9dcb0 0%, #88cebb 50%, #3fa896 100%);">

        <div class="mx-auto flex w-full max-w-[1080px] flex-1 flex-col px-8 pb-10">
            <?php include __DIR__.'/../components/admin-header.php'; ?>

            <main class="mt-[21px] flex flex-col gap-5">
                <?php if (!empty($flash)) { ?>
                    <p class="flex items-center gap-2 rounded-2xl border border-teal-600/40 bg-teal-50 px-4 py-3 text-sm font-medium text-teal-900 shadow-[0_2px_8px_rgba(40,120,105,0.15)]" role="status">
                        <i class="ph ph-check-circle text-[18px] text-teal-700" aria-hidden="true"></i>
                        <?php echo e($flash); ?>
                    </p>
                <?php } ?>

                <?php if (!empty($error)) { ?>
                    <p class="flex items-center gap-2 rounded-2xl border border-rose-300 bg-rose-50 px-4 py-3 text-sm font-medium text-rose-800 shadow-[0_2px_8px_rgba(40,120,105,0.15)]" role="alert">
                        <i class="ph ph-warning-circle text-[18px] text-rose-600" aria-hidden="true"></i>
                        <?php echo e($error); ?>
                    </p>
                <?php } ?>

                <div class="grid gap-4 sm:grid-cols-3">
                    <?php
                    $ringkasan = [
                        ['label' => 'Total Akun', 'nilai' => $total],
                        ['label' => 'Aktif', 'nilai' => $aktif],
                        ['label' => 'Nonaktif', 'nilai' => $nonaktif],
                    ];
?>
                    <?php foreach ($ringkasan as $kartu) { ?>
                        <div class="rounded-[28px] border-2 border-white/60 bg-white/60 px-6 py-4 shadow-[0_5px_18px_rgba(40,120,105,0.18)] backdrop-blur-md">
                            <p class="text-[11px] font-semibold uppercase tracking-wide text-teal-800"><?php echo e($kartu['label']); ?></p>
                            <p class="mt-1 font-display text-[26px] font-bold leading-tight text-slate-800"><?php echo (int) $kartu['nilai']; ?></p>
                        </div>
                    <?php } ?>
                </div>

                <div class="flex flex-col gap-3 sm:flex-row sm:items-center">
                    <?php /* htmx swaps only the table, so the caret never leaves the search box. The plain GET still works without JS. */ ?>
                    <form method="GET" action="/admin/pengguna"
                          class="flex min-w-0 flex-1 flex-col gap-3 sm:flex-row sm:items-center" role="search"
                          hx-get="/admin/pengguna"
                          hx-trigger="submit"
                          hx-target="#daftar-akun"
                          hx-select="#daftar-akun"
                          hx-swap="outerHTML"
                          hx-indicator="#daftar-akun"
                          hx-push-url="true"
                          hx-on::after-swap="window.Alpine && window.Alpine.initTree(document.getElementById('daftar-akun'))">
                        <div class="relative min-w-0 flex-1">
                            <i class="ph ph-magnifying-glass pointer-events-none absolute left-4 top-1/2 -translate-y-1/2 text-[16px] text-slate-500" aria-hidden="true"></i>
                            <input type="search" name="q" value="<?php echo e($q); ?>"
                                   placeholder="Cari nama, username, atau email" aria-label="Cari akun"
                                   autocomplete="off"
                                   hx-get="/admin/pengguna"
                                   hx-trigger="input changed delay:350ms"
                                   class="h-11 w-full rounded-full border border-white/70 bg-white/80 pl-11 pr-4 text-[13px] text-slate-800 placeholder:text-slate-600 focus:border-teal-600 focus:outline-none focus-visible:ring-2 focus-visible:ring-teal-600">
                        </div>

                        <select name="peran" aria-label="Saring menurut peran"
                                hx-get="/admin/pengguna"
                                hx-trigger="change"
                                class="h-11 rounded-full border border-white/70 bg-white/80 px-4 text-[13px] text-slate-800 focus:border-teal-600 focus:outline-none focus-visible:ring-2 focus-visible:ring-teal-600 sm:w-auto">
                            <option value="semua" <?php echo 'semua' === $peran ? 'selected' : ''; ?>>Semua peran</option>
                            <option value="admin" <?php echo 'admin' === $peran ? 'selected' : ''; ?>>Admin</option>
                            <option value="perawat" <?php echo 'perawat' === $peran ? 'selected' : ''; ?>>Perawat</option>
                        </select>

                        <button type="submit" class="sr-only">Cari</button>
                    </form>

                    <button type="button" @click="tambah = { nama: '', username: '', email: '', peran: 'perawat' }"
                            class="h-11 shrink-0 rounded-full bg-teal-700 px-6 text-[13px] font-semibold text-white transition-colors hover:bg-teal-800 focus:outline-none focus-visible:ring-2 focus-visible:ring-teal-600">
                        + Tambah akun
                    </button>
                </div>

                <section id="daftar-akun"
                         class="rounded-[40px] border-2 border-white/60 bg-white/60 p-6 font-sans shadow-[0_5px_18px_rgba(40,120,105,0.18)] backdrop-blur-md transition-opacity [&.htmx-request]:opacity-60">
                    <?php if ([] === $baris) { ?>
                        <p class="py-8 text-center text-[13px] text-slate-600">Tidak ada akun yang cocok dengan pencarian.</p>
                    <?php } else { ?>
                        <div class="overflow-x-auto">
                            <table class="w-full min-w-[760px] text-left text-[12px]">
                                <thead>
                                    <tr class="text-teal-800">
                                        <th class="pb-3 font-semibold">Nama</th>
                                        <th class="pb-3 font-semibold">Username</th>
                                        <th class="pb-3 font-semibold">Peran</th>
                                        <th class="pb-3 font-semibold">Status</th>
                                        <th class="pb-3 text-right font-semibold">Aksi</th>
                                    </tr>
                                </thead>
                                <tbody>
                                <?php foreach ($baris as $row) { ?>
                                    <?php
                $peranBaris = (string) $row['peran'];
                                    $dataBaris = e(json_encode([
                                        'id' => $row['id'],
                                        'nama' => $row['nama'],
                                        'username' => $row['username'],
                                        'email' => $row['email'],
                                        'peran' => $peranBaris,
                                        'aktif' => $row['aktif'],
                                        'sendiri' => $row['sendiri'],
                                    ], JSON_UNESCAPED_UNICODE));
                                    ?>
                                    <tr class="border-t border-slate-300/60">
                                        <td class="py-3 pr-3">
                                            <div class="flex items-center gap-3">
                                                <span class="grid size-8 shrink-0 place-items-center rounded-full bg-teal-100 text-[11px] font-bold text-teal-700"><?php echo e($row['inisial']); ?></span>
                                                <div class="min-w-0">
                                                    <p class="truncate text-[13px] font-semibold text-slate-800"><?php echo e($row['nama']); ?></p>
                                                    <p class="truncate text-[11px] text-slate-600"><?php echo e($row['keterangan']); ?></p>
                                                </div>
                                            </div>
                                        </td>
                                        <td class="py-3 pr-3 text-slate-700"><?php echo e($row['username']); ?></td>
                                        <td class="py-3 pr-3">
                                            <span class="inline-flex rounded-full px-3 py-1 text-[11px] font-semibold <?php echo e($kelasPeran[$peranBaris] ?? 'bg-slate-100 text-slate-600'); ?>">
                                                <?php echo e($labelPeran[$peranBaris] ?? $peranBaris); ?>
                                            </span>
                                        </td>
                                        <td class="py-3 pr-3">
                                            <?php if ($row['aktif']) { ?>
                                                <span class="inline-flex rounded-full bg-teal-700 px-3 py-1 text-[11px] font-semibold text-white">Aktif</span>
                                            <?php } else { ?>
                                                <span class="inline-flex rounded-full bg-slate-200 px-3 py-1 text-[11px] font-semibold text-slate-600">Nonaktif</span>
                                            <?php } ?>
                                        </td>
                                        <td class="py-3 text-right">
                                            <button type="button" @click='edit = <?php echo $dataBaris; ?>'
                                                    class="h-8 rounded-full border border-slate-300 bg-white px-4 text-[11px] font-medium text-slate-700 transition-colors hover:border-teal-600 hover:text-teal-700 focus:outline-none focus-visible:ring-2 focus-visible:ring-teal-600">
                                                Edit
                                            </button>
                                        </td>
                                    </tr>
                                <?php } ?>
                                </tbody>
                            </table>
                        </div>

                        <div class="mt-4 flex flex-wrap items-center justify-between gap-3 text-[11px] text-slate-600">
                            <p>Menampilkan <?php echo (int) $awal; ?>-<?php echo (int) $akhir; ?> dari <?php echo (int) $cocok; ?> akun</p>

                            <?php if ($halamanTotal > 1) { ?>
                                <nav class="flex items-center gap-1" aria-label="Halaman daftar akun">
                                    <a href="<?php echo e($tautanHalaman($halaman - 1)); ?>"
                                       class="grid size-7 place-items-center rounded-full text-slate-500 hover:bg-white <?php echo $halaman <= 1 ? 'pointer-events-none opacity-40' : ''; ?>"
                                       aria-label="Halaman sebelumnya">
                                        <i class="ph ph-caret-left text-[12px]" aria-hidden="true"></i>
                                    </a>

                                    <?php for ($nomor = 1; $nomor <= $halamanTotal; ++$nomor) { ?>
                                        <a href="<?php echo e($tautanHalaman($nomor)); ?>"
                                           class="grid size-7 place-items-center rounded-full font-semibold transition-colors <?php echo $nomor === $halaman ? 'bg-teal-700 text-white' : 'text-slate-600 hover:bg-white'; ?>"
                                           <?php echo $nomor === $halaman ? 'aria-current="page"' : ''; ?>>
                                            <?php echo $nomor; ?>
                                        </a>
                                    <?php } ?>

                                    <a href="<?php echo e($tautanHalaman($halaman + 1)); ?>"
                                       class="grid size-7 place-items-center rounded-full text-slate-500 hover:bg-white <?php echo $halaman >= $halamanTotal ? 'pointer-events-none opacity-40' : ''; ?>"
                                       aria-label="Halaman berikutnya">
                                        <i class="ph ph-caret-right text-[12px]" aria-hidden="true"></i>
                                    </a>
                                </nav>
                            <?php } ?>
                        </div>
                    <?php } ?>
                </section>

                <?php /* Modals sit outside the table card: backdrop-blur makes a card the containing block for position: fixed. */ ?>

                <template x-if="tambah">
                    <div class="fixed inset-0 z-50 flex items-center justify-center bg-slate-900/40 p-4"
                         @click.self="tambah = null" @keydown.escape.window="tambah = null"
                         role="dialog" aria-modal="true" aria-labelledby="judul-tambah-akun">
                        <div class="max-h-[90vh] w-full max-w-[520px] overflow-y-auto rounded-[40px] bg-white p-8">
                            <h2 id="judul-tambah-akun" class="font-display text-[30px] font-bold text-slate-600">Tambah Akun</h2>
                            <p class="mt-1 text-[12px] text-slate-600">Buat akun login untuk petugas atau tenaga medis.</p>

                            <form method="POST" action="/admin/pengguna" class="mt-6 flex flex-col gap-4"
                                  x-data="{ sandi: '', ulang: '', beda: false, lihat: false }"
                                  @submit="beda = sandi !== ulang; if (beda) { $event.preventDefault(); }">
                                <?php echo csrf_field(); ?>
                                <?php echo $konteks; ?>
                                <input type="hidden" name="aksi" value="tambah">

                                <label class="<?php echo e($labelKelas); ?>">
                                    Nama lengkap
                                    <input type="text" name="full_name" required maxlength="120" placeholder="Nama lengkap"
                                           x-model="tambah.nama"
                                           class="<?php echo e($inputKelas); ?>">
                                </label>

                                <div class="grid gap-4 sm:grid-cols-2">
                                    <label class="<?php echo e($labelKelas); ?>">
                                        Username
                                        <input type="text" name="username" required maxlength="40" placeholder="isi username"
                                               x-model="tambah.username"
                                               class="<?php echo e($inputKelas); ?>">
                                    </label>
                                    <label class="<?php echo e($labelKelas); ?>">
                                        Peran
                                        <select name="role" x-model="tambah.peran" class="<?php echo e($inputKelas); ?>">
                                            <option value="perawat">Perawat</option>
                                            <option value="admin">Admin</option>
                                        </select>
                                    </label>
                                </div>

                                <label class="<?php echo e($labelKelas); ?>">
                                    Email (opsional)
                                    <input type="email" name="email" maxlength="120" placeholder="nama@contoh.id"
                                           x-model="tambah.email"
                                           class="<?php echo e($inputKelas); ?>">
                                    <span class="text-[10px] font-normal text-slate-500">Alternatif login. NIP dapat dipakai sebagai username.</span>
                                </label>

                                <div class="grid gap-4 sm:grid-cols-2">
                                    <label class="<?php echo e($labelKelas); ?>">
                                        Password awal
                                        <span class="relative block">
                                            <input :type="lihat ? 'text' : 'password'" name="password" required minlength="8" x-model="sandi"
                                                   placeholder="Minimal 8 karakter" autocomplete="new-password"
                                                   class="<?php echo e($inputKelas); ?> w-full pr-10">
                                            <button type="button" @click="lihat = !lihat"
                                                    class="absolute right-3 top-1/2 -translate-y-1/2 text-slate-500 hover:text-teal-700 focus:outline-none focus-visible:ring-2 focus-visible:ring-teal-600"
                                                    :aria-label="lihat ? 'Sembunyikan kata sandi' : 'Tampilkan kata sandi'">
                                                <i class="ph text-[16px]" :class="lihat ? 'ph-eye-slash' : 'ph-eye'" aria-hidden="true"></i>
                                            </button>
                                        </span>
                                    </label>
                                    <label class="<?php echo e($labelKelas); ?>">
                                        Konfirmasi password
                                        <input :type="lihat ? 'text' : 'password'" name="password_confirm" required minlength="8" x-model="ulang"
                                               placeholder="Minimal 8 karakter" autocomplete="new-password"
                                               class="<?php echo e($inputKelas); ?>">
                                    </label>
                                </div>

                                <p class="text-[11px] text-rose-600" x-show="beda" x-cloak>Konfirmasi kata sandi tidak sama.</p>

                                <div class="mt-2 flex justify-end gap-3">
                                    <button type="button" @click="tambah = null" class="<?php echo e($batalKelas); ?>">Batal</button>
                                    <button type="submit" class="<?php echo e($simpanKelas); ?>">Simpan Akun</button>
                                </div>
                            </form>
                        </div>
                    </div>
                </template>

                <template x-if="edit">
                    <div class="fixed inset-0 z-50 flex items-center justify-center bg-slate-900/40 p-4"
                         @click.self="edit = null" @keydown.escape.window="edit = null"
                         role="dialog" aria-modal="true" aria-labelledby="judul-edit-akun">
                        <div class="max-h-[90vh] w-full max-w-[520px] overflow-y-auto rounded-[40px] bg-white p-8">
                            <h2 id="judul-edit-akun" class="font-display text-[30px] font-bold text-slate-600">Edit Akun</h2>

                            <form method="POST" action="/admin/pengguna" class="mt-6 flex flex-col gap-4">
                                <?php echo csrf_field(); ?>
                                <?php echo $konteks; ?>
                                <input type="hidden" name="aksi" value="ubah">
                                <input type="hidden" name="id" :value="edit.id">
                                <input type="hidden" name="role" :value="edit.peran">

                                <div class="grid gap-4 sm:grid-cols-2">
                                    <label class="<?php echo e($labelKelas); ?>">
                                        Nama lengkap
                                        <input type="text" name="full_name" required maxlength="120" x-model="edit.nama"
                                               class="<?php echo e($inputKelas); ?>">
                                    </label>
                                    <label class="<?php echo e($labelKelas); ?>">
                                        Username
                                        <input type="text" name="username" required maxlength="40" x-model="edit.username"
                                               class="<?php echo e($inputKelas); ?>">
                                    </label>
                                </div>

                                <div class="grid gap-4 sm:grid-cols-2">
                                    <label class="<?php echo e($labelKelas); ?>">
                                        Peran
                                        <select x-model="edit.peran" :disabled="edit.sendiri"
                                                class="<?php echo e($inputKelas); ?> disabled:cursor-not-allowed disabled:bg-slate-100 disabled:text-slate-500">
                                            <option value="admin">Admin</option>
                                            <option value="perawat">Perawat</option>
                                        </select>
                                    </label>
                                    <label class="<?php echo e($labelKelas); ?>">
                                        Email (opsional)
                                        <input type="email" name="email" maxlength="120" placeholder="nama@contoh.id" x-model="edit.email"
                                               class="<?php echo e($inputKelas); ?>">
                                    </label>
                                </div>

                                <p class="text-[11px] text-slate-500" x-show="edit.sendiri" x-cloak>
                                    Peran akun sendiri tidak dapat diubah.
                                </p>

                                <div class="flex items-center justify-between gap-3 rounded-2xl border border-slate-200 px-4 py-3" x-show="!edit.sendiri" x-cloak>
                                    <div>
                                        <p class="text-[12px] font-semibold text-slate-800">Reset password</p>
                                        <p class="text-[11px] text-slate-500">Buat password sementara untuk akun ini</p>
                                    </div>
                                    <button type="button"
                                            @click="reset = { id: edit.id, nama: edit.nama }; edit = null"
                                            class="h-8 shrink-0 rounded-full bg-teal-700 px-4 text-[11px] font-medium text-white transition-colors hover:bg-teal-800 focus:outline-none focus-visible:ring-2 focus-visible:ring-teal-600">
                                        Reset Password
                                    </button>
                                </div>

                                <div class="flex items-center justify-between gap-3 rounded-2xl border border-slate-200 px-4 py-3" x-show="!edit.sendiri" x-cloak>
                                    <div>
                                        <p class="text-[12px] font-semibold text-slate-800" x-text="edit.aktif ? 'Nonaktifkan akun' : 'Aktifkan akun'"></p>
                                        <p class="text-[11px] text-slate-500" x-text="edit.aktif ? 'Akun tidak bisa login, riwayat tetap tersimpan' : 'Akun bisa login dan riwayat tetap tersimpan'"></p>
                                    </div>
                                    <button type="button"
                                            @click="konfirmasi = edit.aktif
                                                ? { id: edit.id, nama: edit.nama, aksi: 'nonaktif', judul: 'Nonaktifkan Akun?', pesan: 'tidak bisa login lagi. Riwayat kunjungan yang pernah dicatat tetap tersimpan, dan akun bisa diaktifkan kembali lewat Edit.', tombol: 'Ya, Nonaktifkan' }
                                                : { id: edit.id, nama: edit.nama, aksi: 'aktif', judul: 'Aktifkan Kembali Akun?', pesan: 'bisa login lagi dengan password terakhirnya.', tombol: 'Ya, Aktifkan' }; edit = null"
                                            class="h-8 shrink-0 rounded-full border border-rose-300 bg-rose-50 px-4 text-[11px] font-medium text-rose-700 transition-colors hover:bg-rose-100 focus:outline-none focus-visible:ring-2 focus-visible:ring-rose-400"
                                            x-text="edit.aktif ? 'Nonaktifkan' : 'Aktifkan'"></button>
                                </div>

                                <div class="mt-2 flex justify-end gap-3">
                                    <button type="button" @click="edit = null" class="<?php echo e($batalKelas); ?>">Batal</button>
                                    <button type="submit" class="<?php echo e($simpanKelas); ?>">Simpan Perubahan</button>
                                </div>
                            </form>
                        </div>
                    </div>
                </template>

                <template x-if="reset">
                    <div class="fixed inset-0 z-50 flex items-center justify-center bg-slate-900/40 p-4"
                         @click.self="reset = null" @keydown.escape.window="reset = null"
                         role="dialog" aria-modal="true" aria-labelledby="judul-reset-sandi">
                        <div class="w-full max-w-[460px] rounded-[40px] bg-white p-8">
                            <h2 id="judul-reset-sandi" class="font-display text-[28px] font-bold text-slate-600">Reset Password</h2>
                            <p class="mt-3 text-[12px] leading-relaxed text-slate-700">
                                Password akun <span class="font-semibold" x-text="reset.nama"></span> akan diganti dengan
                                password sementara. Pengguna wajib menggantinya saat login berikutnya, dan semua sesi
                                yang sedang aktif berakhir.
                            </p>
                            <p class="mt-3 rounded-2xl bg-slate-100 px-4 py-3 text-[11px] leading-relaxed text-slate-600">
                                Password sementara dibuat otomatis dan hanya ditampilkan sekali setelah reset.
                            </p>

                            <form method="POST" action="/admin/pengguna" class="mt-6 flex justify-end gap-3">
                                <?php echo csrf_field(); ?>
                                <?php echo $konteks; ?>
                                <input type="hidden" name="aksi" value="reset">
                                <input type="hidden" name="id" :value="reset.id">
                                <button type="button" @click="reset = null" class="<?php echo e($batalKelas); ?>">Batal</button>
                                <button type="submit" class="<?php echo e($simpanKelas); ?>">Ya, Reset Password</button>
                            </form>
                        </div>
                    </div>
                </template>

                <template x-if="konfirmasi">
                    <div class="fixed inset-0 z-50 flex items-center justify-center bg-slate-900/40 p-4"
                         @click.self="konfirmasi = null" @keydown.escape.window="konfirmasi = null"
                         role="dialog" aria-modal="true" aria-labelledby="judul-konfirmasi-akun">
                        <div class="w-full max-w-[460px] rounded-[40px] bg-white p-8">
                            <h2 id="judul-konfirmasi-akun" class="font-display text-[28px] font-bold text-slate-600" x-text="konfirmasi.judul"></h2>
                            <p class="mt-3 text-[12px] leading-relaxed text-slate-700">
                                Akun <span class="font-semibold" x-text="konfirmasi.nama"></span>
                                <span x-text="konfirmasi.pesan"></span>
                            </p>

                            <form method="POST" action="/admin/pengguna" class="mt-6 flex justify-end gap-3">
                                <?php echo csrf_field(); ?>
                                <?php echo $konteks; ?>
                                <input type="hidden" name="aksi" :value="konfirmasi.aksi">
                                <input type="hidden" name="id" :value="konfirmasi.id">
                                <button type="button" @click="konfirmasi = null" class="<?php echo e($batalKelas); ?>">Batal</button>
                                <button type="submit"
                                        class="h-9 rounded-full px-6 text-xs font-medium text-white transition-colors focus:outline-none focus-visible:ring-2"
                                        :class="konfirmasi.aksi === 'aktif'
                                            ? 'bg-teal-700 hover:bg-teal-800 focus-visible:ring-teal-600'
                                            : 'bg-rose-600 hover:bg-rose-700 focus-visible:ring-rose-400'"
                                        x-text="konfirmasi.tombol"></button>
                            </form>
                        </div>
                    </div>
                </template>

                <template x-if="temp">
                    <div class="fixed inset-0 z-50 flex items-center justify-center bg-slate-900/40 p-4"
                         @click.self="temp = null" @keydown.escape.window="temp = null"
                         role="dialog" aria-modal="true" aria-labelledby="judul-sandi-sementara">
                        <div class="w-full max-w-[460px] rounded-[40px] bg-white p-8">
                            <h2 id="judul-sandi-sementara" class="font-display text-[26px] font-bold text-teal-700">Password Berhasil Direset</h2>
                            <p class="mt-2 text-[12px] leading-relaxed text-slate-700">
                                Password akun <span class="font-semibold" x-text="temp.nama"></span> sudah diganti dengan
                                password sementara.
                            </p>

                            <p class="mt-5 text-[11px] font-semibold text-slate-800">Password sementara</p>
                            <div class="mt-1 flex items-center gap-2">
                                <input type="text" readonly :value="temp.password" aria-label="Password sementara"
                                       class="h-10 min-w-0 flex-1 rounded-full bg-slate-100 px-4 font-mono text-[12px] text-slate-800">
                                <button type="button"
                                        @click="navigator.clipboard.writeText(temp.password).then(() => tersalin = true).catch(() => {})"
                                        class="h-10 shrink-0 rounded-full bg-white px-4 text-[11px] font-medium text-teal-700 shadow-md transition-colors hover:bg-teal-50 focus:outline-none focus-visible:ring-2 focus-visible:ring-teal-600">
                                    <i class="ph ph-copy text-[14px]" aria-hidden="true"></i>
                                    <span x-text="tersalin ? 'Tersalin' : 'Salin'"></span>
                                </button>
                            </div>

                            <p class="mt-4 rounded-2xl bg-amber-50 px-4 py-3 text-[11px] leading-relaxed text-amber-900">
                                Salin dan berikan ke pemilik akun sekarang. Password ini tidak ditampilkan lagi, dan
                                pengguna wajib menggantinya saat login berikutnya.
                            </p>

                            <div class="mt-6 flex justify-end">
                                <button type="button" @click="temp = null" class="<?php echo e($simpanKelas); ?>">Selesai</button>
                            </div>
                        </div>
                    </div>
                </template>
            </main>
        </div>

        <?php include __DIR__.'/../components/admin-footer.php'; ?>
    </div>
</div>
