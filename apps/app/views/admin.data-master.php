<?php
/** @var string $tab */
/** @var string $role */
/** @var list<array<string, mixed>> $services */
/** @var list<array<string, mixed>> $medics */
/** @var null|string $flash */
$active = 'data-master';
$headerTitle = 'Data Master';

$tabs = ['poli' => 'Daftar Poli', 'tenaga-medis' => 'Tenaga Medis'];
?>
<div class="flex min-h-screen flex-col lg:flex-row"
     x-data="{ editPoli: null, medic: null, confirm: null }">
    <?php include __DIR__.'/../components/admin-sidebar.php'; ?>

    <div class="flex min-h-screen min-w-0 flex-1 flex-col"
         style="background: radial-gradient(circle at 85% 15%, rgba(160,215,205,0.7) 0%, transparent 50%), linear-gradient(135deg, #a9dcb0 0%, #88cebb 50%, #3fa896 100%);">

        <div class="mx-auto flex w-full max-w-[1080px] flex-1 flex-col px-8 pb-10">
            <?php include __DIR__.'/../components/admin-header.php'; ?>

            <main class="mt-[21px] flex flex-col gap-6">
                <?php if (!empty($flash)) { ?>
                    <p class="rounded-2xl bg-white/80 px-4 py-2 text-sm text-teal-800"><?php echo e($flash); ?></p>
                <?php } ?>

                <div class="flex flex-wrap items-center justify-between gap-3">
                    <nav class="flex gap-3" aria-label="Tab data master">
                        <?php foreach ($tabs as $key => $label) { ?>
                            <a href="/admin/data-master?tab=<?php echo e($key); ?>"
                               class="rounded-full px-5 py-2 text-sm font-semibold transition-colors <?php echo $tab === $key ? 'bg-teal-500 text-white' : 'bg-white/70 text-teal-700 hover:bg-white'; ?>"
                               <?php echo $tab === $key ? 'aria-current="page"' : ''; ?>>
                                <?php echo e($label); ?>
                            </a>
                        <?php } ?>
                    </nav>

                    <?php if ('tenaga-medis' === $tab) { ?>
                        <button type="button"
                                @click="medic = <?php echo e((string) json_encode(['id' => null, 'nama' => '', 'str' => '', 'service_id' => (int) ($services[0]['id'] ?? 0)])); ?>"
                                class="rounded-full bg-teal-500 px-5 py-2 text-sm font-semibold text-white hover:bg-teal-600">
                            + Tambah Data
                        </button>
                    <?php } ?>
                </div>

                <?php if ('tenaga-medis' === $tab) { ?>
                    <?php include __DIR__.'/../components/admin-master-tabel-medis.php'; ?>
                <?php } else { ?>
                    <?php include __DIR__.'/../components/admin-master-tabel-poli.php'; ?>
                <?php } ?>

                <?php /* Modals sit outside the table cards: the cards use backdrop-blur, which makes them the containing block for position: fixed. */ ?>

                <template x-if="editPoli">
                    <div class="fixed inset-0 z-50 flex items-center justify-center bg-slate-900/40 p-4"
                         @click.self="editPoli = null" @keydown.escape.window="editPoli = null"
                         role="dialog" aria-modal="true" aria-labelledby="judul-ubah-poli">
                        <div class="w-full max-w-[400px] rounded-[40px] bg-white p-8">
                            <h2 id="judul-ubah-poli" class="text-center font-display text-[30px] font-bold text-slate-500">Ubah Data Poli</h2>

                            <form method="POST" action="/admin/data-master" class="mt-6 flex flex-col gap-4">
                                <?php echo csrf_field(); ?>
                                <input type="hidden" name="tab" value="poli">
                                <input type="hidden" name="aksi" value="ubah-poli">
                                <input type="hidden" name="id" :value="editPoli.id">

                                <label class="flex flex-col gap-1 text-xs font-semibold text-slate-800">
                                    Nama Poli
                                    <input type="text" name="nama" required minlength="2" maxlength="60" x-model="editPoli.nama"
                                           class="h-10 rounded-full border border-slate-500 px-4 text-xs font-normal text-slate-700 focus:border-teal-500 focus:outline-none">
                                </label>

                                <p class="text-[11px] leading-relaxed text-slate-500">
                                    Jumlah dokter dihitung otomatis.
                                </p>

                                <div class="mt-2 flex justify-end gap-3">
                                    <button type="button" @click="editPoli = null"
                                            class="h-9 rounded-full bg-white px-6 text-xs font-medium text-teal-600 shadow-md hover:bg-teal-50">Batal</button>
                                    <button type="submit"
                                            class="h-9 rounded-full bg-teal-500 px-6 text-xs font-medium text-white hover:bg-teal-600">Simpan</button>
                                </div>
                            </form>
                        </div>
                    </div>
                </template>

                <template x-if="medic">
                    <div class="fixed inset-0 z-50 flex items-center justify-center bg-slate-900/40 p-4"
                         @click.self="medic = null" @keydown.escape.window="medic = null"
                         role="dialog" aria-modal="true" aria-labelledby="judul-medic">
                        <div class="w-full max-w-[420px] rounded-[40px] bg-white p-8">
                            <h2 id="judul-medic" class="text-center font-display text-[30px] font-bold text-slate-500"
                                x-text="medic.id ? 'Ubah Tenaga Medis' : 'Tambah Tenaga Medis'"></h2>

                            <form method="POST" action="/admin/data-master" class="mt-6 flex flex-col gap-3">
                                <?php echo csrf_field(); ?>
                                <input type="hidden" name="tab" value="tenaga-medis">
                                <input type="hidden" name="aksi" :value="medic.id ? 'ubah-medis' : 'tambah-medis'">
                                <input type="hidden" name="id" :value="medic.id ?? ''">

                                <label class="flex flex-col gap-1 text-xs font-semibold text-slate-800">
                                    Nama
                                    <input type="text" name="nama" required minlength="2" maxlength="120" x-model="medic.nama"
                                           class="h-10 rounded-full border border-slate-500 px-4 text-xs font-normal text-slate-700 focus:border-teal-500 focus:outline-none">
                                </label>

                                <label class="flex flex-col gap-1 text-xs font-semibold text-slate-800">
                                    Nomor STR (opsional)
                                    <input type="text" name="str_number" maxlength="32" x-model="medic.str"
                                           class="h-10 rounded-full border border-slate-500 px-4 text-xs font-normal text-slate-700 focus:border-teal-500 focus:outline-none">
                                </label>

                                <label class="flex flex-col gap-1 text-xs font-semibold text-slate-800">
                                    Poli
                                    <select name="service_id" x-model.number="medic.service_id"
                                            class="h-10 rounded-full border border-slate-500 px-4 text-xs font-normal text-slate-700">
                                        <?php foreach ($services as $service) { ?>
                                            <option value="<?php echo (int) $service['id']; ?>"><?php echo e((string) $service['name']); ?></option>
                                        <?php } ?>
                                    </select>
                                </label>

                                <p class="text-[11px] leading-relaxed text-slate-500">
                                    Status kehadiran diatur dari Dashboard, bukan dari form ini.
                                </p>

                                <div class="mt-2 flex justify-end gap-3">
                                    <button type="button" @click="medic = null"
                                            class="h-9 rounded-full bg-white px-6 text-xs font-medium text-teal-600 shadow-md hover:bg-teal-50">Batal</button>
                                    <button type="submit"
                                            class="h-9 rounded-full bg-teal-500 px-6 text-xs font-medium text-white hover:bg-teal-600">Simpan</button>
                                </div>
                            </form>
                        </div>
                    </div>
                </template>

                <template x-if="confirm">
                    <div class="fixed inset-0 z-50 flex items-center justify-center bg-slate-900/40 p-4"
                         @click.self="confirm = null" @keydown.escape.window="confirm = null"
                         role="dialog" aria-modal="true" aria-labelledby="judul-konfirmasi">
                        <div class="w-full max-w-[380px] rounded-[40px] bg-white p-8 text-center">
                            <h2 id="judul-konfirmasi" class="font-display text-[28px] font-bold text-slate-600" x-text="confirm.judul"></h2>
                            <p class="mt-3 text-[12px] leading-relaxed text-slate-500" x-text="confirm.pesan"></p>

                            <form method="POST" action="/admin/data-master" class="mt-6 flex justify-center gap-3">
                                <?php echo csrf_field(); ?>
                                <input type="hidden" name="tab" :value="confirm.tab">
                                <input type="hidden" name="aksi" :value="confirm.aksi">
                                <input type="hidden" name="id" :value="confirm.id">
                                <button type="button" @click="confirm = null"
                                        class="h-9 rounded-full bg-white px-6 text-xs font-medium text-teal-600 shadow-md hover:bg-teal-50">Batal</button>
                                <button type="submit"
                                        class="h-9 rounded-full px-6 text-xs font-medium text-white"
                                        :class="confirm.aktif ? 'bg-rose-500 hover:bg-rose-600' : 'bg-emerald-500 hover:bg-emerald-600'"
                                        x-text="confirm.aktif ? 'Ya, nonaktifkan' : 'Ya, aktifkan'"></button>
                            </form>
                        </div>
                    </div>
                </template>
            </main>
        </div>

        <?php include __DIR__.'/../components/admin-footer.php'; ?>
    </div>
</div>
