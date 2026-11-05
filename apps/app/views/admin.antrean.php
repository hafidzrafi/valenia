<?php

/** @var list<array<string, mixed>> $boards */
/** @var null|string $flash */
$active = 'antrian';
$token = $token ?? '';
$role = $role ?? 'admin';
$headerTitle = 'Antrean Poliklinik';
$adminNama = 'Petugas '.ucfirst($role);
$adminUnit = 'Poliklinik Polinema';
?>
<div class="flex min-h-screen flex-col lg:flex-row">
    <?php include __DIR__.'/../components/admin-sidebar.php'; ?>

    <div class="flex min-h-screen min-w-0 flex-1 flex-col"
        style="background: radial-gradient(circle at 85% 15%, rgba(160,215,205,0.7) 0%, transparent 50%), linear-gradient(135deg, #a9dcb0 0%, #88cebb 50%, #3fa896 100%);">

        <div class="mx-auto flex w-full max-w-[1080px] flex-1 flex-col px-8 pb-10">
            <?php include __DIR__.'/../components/admin-header.php'; ?>

            <main class="mt-[21px]">
                <?php if (null !== ($flash ?? null) && '' !== $flash) { ?>
                    <p class="mb-4 rounded-2xl bg-emerald-50 px-4 py-2 text-sm text-emerald-700">
                        <?php echo e($flash); ?>
                    </p>
                <?php } ?>


                <div class="flex flex-row items-center justify-between">
                    <p class="text-sm text-white/80">
                        Tekan "Panggil berikutnya" untuk memulai layanan. Papan diperbarui otomatis setiap 5 detik.
                    </p>

                    <!-- Form statis tambah manual Adamska -->
                    <section x-data="{ isOpen: false }">

                        <button type="button" @click="isOpen = true"
                            class="w-50 h-10 smooth-corners-md rounded-2xl bg-teal-500 text-white font-light hover:bg-teal-700">
                            + tambah antrian manual
                        </button>


                        <div x-show="isOpen" x-cloak
                            class="fixed inset-0 z-50 flex items-center justify-center bg-slate-900/50 p-4"
                            @keydown.escape.window="isOpen = false"
                            style="display: none;">

                            <div class="w-full max-w-[420px] rounded-[36px] bg-white px-8 py-7 text-center shadow-2xl"
                                @click.outside="isOpen = false">

                                <h3 class="font-display text-[28px] font-bold text-slate-900">Tambah Antrean Manual</h3>

                                <form class="flex flex-col gap-2 items-stretch w-full max-w-sm mx-auto mt-6 px-4 text-left" method="POST" action=""
                                    onsubmit="localStorage.removeItem('nama_lengkap'); localStorage.removeItem('nim_nip'); localStorage.removeItem('keluhan');">


                                    <?php echo csrf_field(); ?>


                                    <div class="flex flex-col items-start gap-2 w-full text-left"
                                        x-data="{ nama_lengkap: localStorage.getItem('nama_lengkap') || '', touched: false }">
                                        <label for="nama_lengkap" class="text-sm font-medium text-left text-black">Nama Lengkap</label>
                                        <input type="text" id="nama_lengkap" name="nama_lengkap" required maxlength="120"
                                            value="<?php echo e($old['full_name'] ?? ''); ?>"
                                            placeholder="Masukkan nama lengkap..."
                                            class="w-full text-xs font-medium text-left p-2 smooth-corners-lg rounded-2xl border-[1.5px] bg-transparent focus:outline-none focus:border-teal-500"
                                            :class="touched && nama_lengkap === '' ? 'border-red-500 text-red-500' : 'border-gray-500 text-gray-500'"
                                            x-model="nama_lengkap"
                                            @input="localStorage.setItem('nama_lengkap', nama_lengkap)"
                                            @blur="touched = true">
                                        <p class="min-h-4 w-full text-left text-xs text-red-500">
                                            <span x-show="touched && nama_lengkap === ''" x-transition>Wajib diisi</span>
                                        </p>
                                    </div>

                                    <div class="flex flex-col items-start gap-2 w-full text-left"
                                        x-data="{ nimNip: localStorage.getItem('nim_nip') || '', touched: false }">
                                        <label for="nim_nip" class="text-sm font-medium text-left text-black">NIM / NIP</label>
                                        <input type="text" inputmode="numeric" pattern="[0-9]*" id="nim_nip" name="nim_nip" required
                                            placeholder="Masukkan NIM / NIP..."
                                            class="w-full text-xs font-medium text-left p-2 smooth-corners-lg rounded-2xl border-[1.5px] bg-transparent focus:outline-none focus:border-teal-500"
                                            :class="touched && (nimNip === '' || !/^\d*$/.test(nimNip)) ? 'border-red-500 text-red-500' : 'border-gray-500 text-gray-500'"
                                            x-model="nimNip"
                                            @input="localStorage.setItem('nim_nip', nimNip)"
                                            @blur="touched = true">
                                        <p class="min-h-4 w-full text-left text-xs text-red-500">
                                            <span x-show="touched && nimNip === ''" x-transition>Wajib diisi</span>
                                            <span x-show="touched && nimNip !== '' && !/^\d*$/.test(nimNip)" x-transition>Hanya bisa diisi angka</span>
                                        </p>
                                    </div>


                                    <div class="flex flex-col items-start gap-2 w-full text-left">
                                        <label for="poli" class="text-sm font-medium text-left text-black">Poli</label>
                                        <select id="poli" name="poli" required
                                            class="w-full text-xs font-medium text-left p-2 smooth-corners-lg rounded-2xl border-[1.5px] border-gray-500 bg-transparent text-gray-500 focus:outline-none focus:border-teal-500">
                                            <option value="">Pilih Poli</option>
                                            <option value="PU" <?php echo ($old['poli'] ?? '') === 'PU' ? 'selected' : ''; ?>>Poli Umum</option>
                                            <option value="PG" <?php echo ($old['poli'] ?? '') === 'PG' ? 'selected' : ''; ?>>Poli Gigi</option>
                                            <option value="PK" <?php echo ($old['poli'] ?? '') === 'PK' ? 'selected' : ''; ?>>Konseling</option>
                                        </select>
                                        <p class="min-h-4 w-full text-left text-xs text-red-500"></p>
                                    </div>


                                    <div class="flex flex-col items-start gap-1 w-full text-left"
                                        x-data="{ keluhan: localStorage.getItem('keluhan') || '', touched: false }">
                                        <label for="keluhan" class="text-sm font-medium text-left text-black">Keluhan</label>
                                        <textarea id="keluhan" name="keluhan" required placeholder="Masukkan keluhan..."
                                            class="w-full h-20 px-3 py-2 text-left smooth-corners-lg rounded-2xl border-[1.5px] bg-transparent text-xs font-medium resize-none focus:outline-none focus:border-teal-500"
                                            :class="touched && keluhan === '' ? 'border-red-500 text-red-500' : 'border-gray-500 text-gray-500'"
                                            x-model="keluhan"
                                            @input="localStorage.setItem('keluhan', keluhan)"
                                            @blur="touched = true"><?php echo e($old['complaint'] ?? ''); ?></textarea>
                                        <p class="min-h-4 w-full text-left text-xs text-red-500">
                                            <span x-show="touched && keluhan === ''" x-transition>Wajib diisi</span>
                                        </p>
                                    </div>

                                    <div class="mt-6 flex items-center justify-center gap-3">
                                        <button type="button" @click="isOpen = false"
                                            onclick="localStorage.removeItem('nama_lengkap'); localStorage.removeItem('nim_nip'); localStorage.removeItem('keluhan'); location.reload();"
                                            class="inline-flex h-[40px] items-center rounded-full border border-slate-300 px-8 text-sm font-medium text-slate-600 transition-colors hover:bg-slate-100">
                                            Tutup
                                        </button>
                                        <!-- button statis, untuk submit V -->
                                        <button type="button" @click="isOpen = false"
                                            class="h-[40px] rounded-full bg-teal-500 px-8 text-sm font-semibold text-white shadow transition-colors hover:bg-teal-600">
                                            Simpan
                                        </button>
                                    </div>

                                </form>


                            </div>
                        </div>
                    </section>

                </div>













                <div id="queue-boards"
                    hx-get="/admin/antrean"
                    hx-trigger="every 5s"
                    hx-swap="innerHTML"
                    class="mt-6">
                    <?php include __DIR__.'/partials/queue-boards.php'; ?>
                </div>





                <!-- perlu dibikin component gak ini? V -->

                <!-- kemungkinan perlu penyesuaian karena adamska reuse kode milik dashboard -->

                <?php
                /** @var list<array<string, mixed>> $visits */
                $statusClass = [
                    'menunggu' => 'bg-[#d9d9d9] text-[#666]',
                    'dilayani' => 'bg-amber-500 text-white',
                    'selesai' => 'bg-[#10b981] text-white',
                ];

$statusLabel = [
    'menunggu' => 'Menunggu',
    'dilayani' => 'Dilayani',
    'selesai' => 'Selesai',
];
?>
                <section class="font-sans" x-data="{ confirmService: null, confirmCode: '', cancelService: null, cancelCode: '' }">
                    <h2 class="mb-[22px] ml-3 mt-7 font-display text-[22px] font-semibold leading-[30px] text-white">Daftar Antrian</h2>

                    <div class="smooth-corners-sm w-full overflow-x-auto bg-[#f0fffb] pb-[7px] shadow-[0_4px_14px_rgba(30,110,95,0.18)]"
                        style="border-radius: 24px;">
                        <table class="ml-[27px] w-[calc(100%-27px)] min-w-[760px] table-fixed border-collapse text-left text-[13px] text-black">
                            <colgroup>
                                <col style="width:122px">
                                <col style="width:150px">
                                <col style="width:170px">
                                <col style="width:100px">
                                <col style="width:140px">
                                <col style="width:180px">
                            </colgroup>
                            <thead>
                                <tr class="h-[45px] border-b border-slate-800 align-top text-[19px] font-light leading-[26px] text-[#555]">
                                    <th class="pl-2.5 pt-[11px] font-light">No</th>
                                    <th class="pt-[11px] font-light">Nama</th>
                                    <th class="pt-[11px] font-light">NIM/NIP</th>
                                    <th class="pt-[11px] font-light">Poli</th>
                                    <th class="pt-[11px] text-center font-light">Status</th>
                                    <th class="pt-[11px] text-center font-light">Aksi</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php if ([] === $visits) { ?>
                                    <tr>
                                        <td colspan="5" class="h-[54px] text-center text-slate-500">Belum ada antrean hari ini.</td>
                                    </tr>
                                <?php } ?>
                                <?php foreach ($visits as $row) { ?>
                                    <?php
                    $status = (string) $row['status'];
                                    $label = $statusLabel[$status] ?? $status;
                                    $pillClass = $statusClass[$status] ?? $statusClass['menunggu'];
                                    $formId = 'panggil-visit-'.$row['id'];
                                    ?>
                                    <tr class="h-[54px] border-b border-slate-800 last:border-b-0">
                                        <td class="pl-2.5"><?php echo e((string) ($row['queue_code'] ?? '')); ?></td>
                                        <td class="truncate pr-2"><?php echo e((string) $row['full_name']); ?></td>
                                        <td><?php echo e((string) $row['identity_number']); ?></td>
                                        <td class="truncate pr-2"><?php echo e((string) $row['service_name']); ?></td>
                                        <td class="text-center">
                                            <div class="flex items-center justify-center gap-2">
                                                <span class="inline-flex h-[22px] w-[90px] items-center justify-center rounded-full text-[10px] font-semibold shadow-[0_1px_2px_rgba(0,0,0,0.25)] <?php echo $pillClass; ?>">
                                                    <?php echo e($label); ?>
                                                </span>

                                            </div>
                                        </td>
                                        <td>
                                            <div class="flex flex-row justify-center gap-2">
                                                <?php if ('selesai' === $status || 'dilayani' === $status) { ?>
                                                    <!-- disabled -->
                                                    <a class="inline-flex h-7 w-20 items-center justify-center rounded-2xl bg-gray-200 px-3 text-[11px] font-medium text-gray-400">
                                                        Panggil
                                                    </a>
                                                    <a class="inline-flex h-7 w-20 items-center justify-center rounded-2xl bg-gray-200 px-3 text-[11px] font-medium text-gray-400">
                                                        Batalkan
                                                    </a>

                                                <?php } else { ?>

                                                    <!-- Form Panggil -->
                                                    <form method="POST" action="/admin/antrean" id="<?php echo $formId; ?>" class="hidden">
                                                        <?php echo csrf_field(); ?>
                                                        <input type="hidden" name="visit_id" value="<?php echo e((string) $row['id']); ?>">
                                                        <input type="hidden" name="action" value="serve">
                                                    </form>

                                                    <!-- Form Batal -->
                                                    <form method="POST" action="/admin/antrean" id="cancel-<?php echo $formId; ?>" class="hidden">
                                                        <?php echo csrf_field(); ?>
                                                        <input type="hidden" name="visit_id" value="<?php echo e((string) $row['id']); ?>">
                                                        <input type="hidden" name="action" value="cancel">
                                                    </form>


                                                    <button type="button"
                                                        @click="confirmService = '<?php echo $formId; ?>'; confirmCode = '<?php echo e((string) $row['queue_code']); ?>'"
                                                        class="inline-flex h-7 w-20 items-center justify-center rounded-2xl bg-teal-500 px-3 text-[11px] font-medium text-white transition-colors hover:bg-teal-700">
                                                        Panggil
                                                    </button>

                                                    <button type="button"
                                                        @click="cancelService = 'cancel-<?php echo $formId; ?>'; cancelCode = '<?php echo e((string) $row['queue_code']); ?>'"
                                                        class="inline-flex h-7 w-20 items-center justify-center rounded-2xl border border-red-500 px-3 text-[11px] font-medium text-red-500 transition-colors hover:bg-red-500 hover:text-white">
                                                        Batalkan
                                                    </button>

                                                <?php } ?>
                                            </div>
                                        </td>
                                    </tr>
                                <?php } ?>
                            </tbody>
                        </table>
                    </div>

                    <!-- panggil -->
                    <div x-show="confirmService !== null" x-cloak
                        class="fixed inset-0 z-50 flex items-center justify-center bg-slate-900/50 p-4"
                        @keydown.escape.window="confirmService = null">
                        <div class="w-full max-w-[420px] rounded-[36px] bg-white px-8 py-7 text-center shadow-2xl"
                            @click.outside="confirmService = null">
                            <h3 class="font-display text-[28px] font-bold text-slate-900">Panggil Pasien</h3>
                            <div class="mx-auto mt-4 max-w-[260px] rounded-2xl bg-teal-50 px-4 py-3">
                                <p class="text-3xl font-black text-slate-900" x-text="confirmCode"></p>
                            </div>
                            <p class="mt-3 text-sm text-slate-600">
                                Nomor antrean akan diumumkan dan status pasien berubah menjadi Dilayani.
                            </p>
                            <div class="mt-6 flex items-center justify-center gap-3">
                                <button type="button" @click="confirmService = null"
                                    class="inline-flex h-[40px] items-center rounded-full border border-slate-300 px-8 text-sm font-medium text-slate-600 transition-colors hover:bg-slate-100">
                                    Batal
                                </button>
                                <button type="button"
                                    @click="document.getElementById(confirmService).submit()"
                                    class="h-[40px] rounded-full bg-teal-500 px-8 text-sm font-semibold text-white shadow transition-colors hover:bg-teal-600">
                                    Panggil
                                </button>
                            </div>
                        </div>
                    </div>



                    <!-- batal -->


                    <div x-show="cancelService !== null" x-cloak
                        class="fixed inset-0 z-50 flex items-center justify-center bg-slate-900/50 p-4"
                        @keydown.escape.window="cancelService = null">
                        <div class="w-full max-w-[420px] rounded-[36px] bg-white px-8 py-7 text-center shadow-2xl"
                            @click.outside="cancelService = null">
                            <h3 class="font-display text-[28px] font-bold text-slate-900">Hapus Data Antrean</h3>
                            <div class="mx-auto mt-4 max-w-[260px] rounded-2xl bg-red-50 px-4 py-3">
                                <p class="text-3xl font-black text-red-600" x-text="cancelCode"></p>
                            </div>
                            <p class="mt-3 text-sm text-slate-600">
                                Apakah anda ingin membatalkan antrean ini? Antrean yang dibatalkan akan hilang dari daftar.
                            </p>
                            <div class="mt-6 flex items-center justify-center gap-3">
                                <button type="button" @click="cancelService = null"
                                    class="inline-flex h-[40px] items-center rounded-full border border-slate-300 px-8 text-sm font-medium text-slate-600 transition-colors hover:bg-slate-100">
                                    Kembali
                                </button>
                                <button type="button"
                                    @click="document.getElementById(cancelService).submit()"
                                    class="h-[40px] rounded-full bg-red-500 px-8 text-sm font-semibold text-white shadow transition-colors hover:bg-red-600">
                                    Batalkan
                                </button>
                            </div>
                        </div>
                    </div>

        </div>

        </section>


        <p class="m-10 mt-0 text-sm text-white/80">
            <a href="/admin" class="underline">&larr; Kembali ke dashboard</a>
        </p>
        </main>
    </div>

    <?php include __DIR__.'/../components/admin-footer.php'; ?>
</div>
</div>