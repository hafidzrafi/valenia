<?php
/** @var string $role */
/** @var string $token */
/** @var list<array<string, mixed>> $doctors */
/** @var array<int, string> $days */
/** @var int $todayDow */
/** @var list<array<string, mixed>> $attendance */
/** @var null|string $flash */
$active = 'jadwal-medis';
$headerTitle = 'Jadwal Tenaga Medis';
$headerSubtitle = 'Layanan Poliklinik Politeknik Negeri Malang';
$adminNama = 'Petugas '.ucfirst($role);
$adminUnit = 'Poliklinik Polinema';
?>
<div class="flex min-h-screen flex-col lg:flex-row"
     x-data="{ tambah: null, hapus: null, today: <?php echo (int) $todayDow; ?> }">
    <?php include __DIR__.'/../components/admin-sidebar.php'; ?>

    <div class="flex min-h-screen min-w-0 flex-1 flex-col"
         style="background: radial-gradient(circle at 85% 15%, rgba(160,215,205,0.7) 0%, transparent 50%), linear-gradient(135deg, #a9dcb0 0%, #88cebb 50%, #3fa896 100%);">

        <div class="mx-auto flex w-full max-w-[1240px] flex-1 flex-col px-8 pb-10 xl:px-10">
            <?php include __DIR__.'/../components/admin-header.php'; ?>

            <main class="mt-[21px] flex flex-col gap-6">
                <?php if (!empty($flash)) { ?>
                    <p class="flex items-center gap-2 rounded-2xl border-l-4 border-teal-600 bg-white/85 px-4 py-3 text-sm font-medium text-teal-900 shadow-[0_2px_8px_rgba(40,120,105,0.15)]" role="status">
                        <i class="ph ph-check-circle text-[18px] text-teal-700" aria-hidden="true"></i>
                        <?php echo e($flash); ?>
                    </p>
                <?php } ?>

                <?php include __DIR__.'/../components/admin-jadwal-matriks.php'; ?>

                <div class="[&>section]:w-full">
                    <?php include __DIR__.'/../components/admin-kehadiran-medis.php'; ?>
                </div>

                <?php /* Outside the cards: backdrop-blur makes a card the containing block for fixed. */ ?>

                <template x-if="tambah">
                    <div class="fixed inset-0 z-50 flex items-center justify-center bg-slate-900/40 p-4"
                         @click.self="tambah = null" @keydown.escape.window="tambah = null"
                         role="dialog" aria-modal="true" aria-labelledby="judul-tambah-jadwal">
                        <div class="w-full max-w-[420px] rounded-[40px] bg-white p-8">
                            <h2 id="judul-tambah-jadwal" class="text-center font-display text-[30px] font-bold text-slate-600">Tambah Jadwal</h2>

                            <form method="POST" action="/admin/jadwal" class="mt-6 flex flex-col gap-3"
                                  x-data="{ kirim: false }" @submit="kirim = true">
                                <?php echo csrf_field(); ?>
                                <input type="hidden" name="aksi" value="tambah-jadwal">

                                <label class="flex flex-col gap-1 text-xs font-semibold text-slate-800">
                                    Tenaga Medis
                                    <select name="doctor_id" x-model="tambah.doctor_id" required
                                            class="h-10 rounded-full border border-slate-500 px-4 text-xs font-normal text-slate-800 transition-colors hover:border-teal-600 focus:border-teal-600 focus:outline-none focus-visible:ring-2 focus-visible:ring-teal-600">
                                        <option value="" disabled>Pilih tenaga medis</option>
                                        <?php foreach ($doctors as $doctor) { ?>
                                            <option value="<?php echo e($doctor['id']); ?>"><?php echo e($doctor['full_name'].' - '.$doctor['service_name']); ?></option>
                                        <?php } ?>
                                    </select>
                                </label>

                                <label class="flex flex-col gap-1 text-xs font-semibold text-slate-800">
                                    Hari
                                    <select name="day_of_week" x-model.number="tambah.day"
                                            class="h-10 rounded-full border border-slate-500 px-4 text-xs font-normal text-slate-800 transition-colors hover:border-teal-600 focus:border-teal-600 focus:outline-none focus-visible:ring-2 focus-visible:ring-teal-600">
                                        <?php foreach ($days as $dayNumber => $dayLabel) { ?>
                                            <option value="<?php echo (int) $dayNumber; ?>"><?php echo e($dayLabel); ?></option>
                                        <?php } ?>
                                    </select>
                                </label>

                                <div class="grid grid-cols-2 gap-3">
                                    <label class="flex flex-col gap-1 text-xs font-semibold text-slate-800">
                                        Mulai
                                        <input type="time" name="start_time" required x-model="tambah.start"
                                               class="h-10 rounded-full border border-slate-500 px-4 text-xs font-normal text-slate-800 transition-colors hover:border-teal-600 focus:border-teal-600 focus:outline-none focus-visible:ring-2 focus-visible:ring-teal-600">
                                    </label>
                                    <label class="flex flex-col gap-1 text-xs font-semibold text-slate-800">
                                        Selesai
                                        <input type="time" name="end_time" required x-model="tambah.end"
                                               class="h-10 rounded-full border border-slate-500 px-4 text-xs font-normal text-slate-800 transition-colors hover:border-teal-600 focus:border-teal-600 focus:outline-none focus-visible:ring-2 focus-visible:ring-teal-600">
                                    </label>
                                </div>

                                <p class="text-[11px] leading-relaxed text-slate-700">
                                    Jadwal berlaku setiap minggu sampai dihapus.
                                </p>

                                <div class="mt-2 flex justify-end gap-3">
                                    <button type="button" @click="tambah = null"
                                            class="h-9 rounded-full bg-white px-6 text-xs font-medium text-teal-700 shadow-md transition-colors hover:bg-teal-50 focus:outline-none focus-visible:ring-2 focus-visible:ring-teal-600">Batal</button>
                                    <button type="submit" :disabled="kirim"
                                            class="h-9 rounded-full bg-teal-700 px-6 text-xs font-medium text-white transition-colors hover:bg-teal-800 disabled:cursor-not-allowed disabled:opacity-60 focus:outline-none focus-visible:ring-2 focus-visible:ring-teal-600">
                                        <span x-text="kirim ? 'Menyimpan...' : 'Simpan'"></span>
                                    </button>
                                </div>
                            </form>
                        </div>
                    </div>
                </template>

                <template x-if="hapus">
                    <div class="fixed inset-0 z-50 flex items-center justify-center bg-slate-900/40 p-4"
                         @click.self="hapus = null" @keydown.escape.window="hapus = null"
                         role="dialog" aria-modal="true" aria-labelledby="judul-hapus-jadwal">
                        <div class="w-full max-w-[380px] rounded-[40px] bg-white p-8 text-center">
                            <h2 id="judul-hapus-jadwal" class="font-display text-[28px] font-bold text-slate-600">Hapus Jadwal</h2>
                            <p class="mt-3 text-[12px] leading-relaxed text-slate-700"
                               x-text="'Hapus jadwal ' + hapus.nama + ' hari ' + hapus.hari + ' pukul ' + hapus.jam + '?'"></p>

                            <form method="POST" action="/admin/jadwal" class="mt-6 flex justify-center gap-3">
                                <?php echo csrf_field(); ?>
                                <input type="hidden" name="aksi" value="hapus-jadwal">
                                <input type="hidden" name="id" :value="hapus.id">
                                <button type="button" @click="hapus = null"
                                        class="h-9 rounded-full bg-white px-6 text-xs font-medium text-teal-700 shadow-md transition-colors hover:bg-teal-50 focus:outline-none focus-visible:ring-2 focus-visible:ring-teal-600">Batal</button>
                                <button type="submit"
                                        class="h-9 rounded-full bg-rose-600 px-6 text-xs font-medium text-white transition-colors hover:bg-rose-700 focus:outline-none focus-visible:ring-2 focus-visible:ring-rose-400">Ya, hapus</button>
                            </form>
                        </div>
                    </div>
                </template>
            </main>
        </div>

        <?php include __DIR__.'/../components/admin-footer.php'; ?>
    </div>
</div>
