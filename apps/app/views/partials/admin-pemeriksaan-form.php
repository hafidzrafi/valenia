<?php
/** @var array<string, mixed> $visit */
/** @var array<string, string> $input */
/** @var null|string $error */
$input = $input ?? [];
?>
<section
    x-data="{ confirmOpen: false, confirmed: false }"
    class="w-full max-w-[560px] rounded-[60px] border-2 border-white/60 bg-white/80 px-8 py-7 shadow-[0_5px_18px_rgba(40,120,105,0.18)] backdrop-blur-md font-sans">
    <h2 class="mb-1 text-center font-display text-[28px] font-semibold leading-[34px] text-slate-900">
        Hasil Pemeriksaan
    </h2>

    <?php if (null !== $error && '' !== $error) { ?>
        <p class="mt-4 rounded-2xl bg-red-50 px-4 py-2 text-center text-sm text-red-600"><?php echo e($error); ?></p>
    <?php } ?>

    <form method="POST" action="/admin/selesai" class="mt-5 flex flex-col gap-4" @submit.prevent="confirmed ? $el.submit() : confirmOpen = true">
        <?php echo csrf_field(); ?>
        <input type="hidden" name="visit_id" value="<?php echo e((string) $visit['id']); ?>">

        <div class="flex flex-col gap-1.5">
            <label for="diagnosis" class="text-[11px] font-semibold text-slate-900">Diagnosis / hasil pemeriksaan</label>
            <input id="diagnosis" type="text" name="diagnosis" required
                value="<?php echo e((string) ($input['diagnosis'] ?? '')); ?>"
                placeholder="Tulis hasil pemeriksaan pasien"
                class="w-full rounded-2xl border-[1.5px] border-slate-400 bg-transparent px-4 py-2.5 text-sm text-slate-900 placeholder:text-slate-400 focus:border-teal-500 focus:outline-none">
        </div>

        <div class="flex flex-col gap-1.5">
            <label for="treatment" class="text-[11px] font-semibold text-slate-900">Tindakan / penanganan</label>
            <input id="treatment" type="text" name="treatment" required
                value="<?php echo e((string) ($input['treatment'] ?? '')); ?>"
                placeholder="Tulis tindakan yang diberikan"
                class="w-full rounded-2xl border-[1.5px] border-slate-400 bg-transparent px-4 py-2.5 text-sm text-slate-900 placeholder:text-slate-400 focus:border-teal-500 focus:outline-none">
        </div>

        <div class="flex flex-col gap-1.5">
            <label for="note" class="text-[11px] font-semibold text-slate-900">Catatan tambahan / opsional</label>
            <input id="note" type="text" name="note"
                value="<?php echo e((string) ($input['note'] ?? '')); ?>"
                placeholder="Catatan untuk kunjungan berikutnya"
                class="w-full rounded-2xl border-[1.5px] border-slate-400 bg-transparent px-4 py-2.5 text-sm text-slate-900 placeholder:text-slate-400 focus:border-teal-500 focus:outline-none">
        </div>

        <div class="mt-4 flex items-center justify-center gap-3">
            <a href="/admin"
                class="inline-flex h-[40px] items-center rounded-full border border-slate-300 px-8 text-sm font-medium text-slate-600 transition-colors hover:bg-slate-100">
                Batal
            </a>
            <button type="submit"
                class="h-[40px] rounded-full bg-teal-500 px-8 text-sm font-semibold text-white shadow transition-colors hover:bg-teal-600">
                Simpan
            </button>
        </div>

        <div x-show="confirmOpen" x-cloak
            class="fixed inset-0 z-50 flex items-center justify-center bg-slate-900/50 p-4"
            @keydown.escape.window="confirmOpen = false">
            <div class="w-full max-w-[420px] rounded-[36px] bg-white px-8 py-7 text-center shadow-2xl"
                @click.outside="confirmOpen = false">
                <h3 class="font-display text-[28px] font-bold text-slate-900">Simpan Hasil?</h3>
                <p class="mt-3 text-sm text-slate-600">
                    Hasil penanganan akan masuk ke riwayat kunjungan pasien dan status
                    <?php echo e((string) ($visit['queue_code'] ?? '')); ?> menjadi Selesai.
                </p>
                <div class="mt-6 flex items-center justify-center gap-3">
                    <button type="button" @click="confirmOpen = false"
                        class="inline-flex h-[40px] items-center rounded-full border border-slate-300 px-8 text-sm font-medium text-slate-600 transition-colors hover:bg-slate-100">
                        Batal
                    </button>
                    <button type="button" @click="confirmed = true; $el.closest('form').requestSubmit()"
                        class="h-[40px] rounded-full bg-teal-500 px-8 text-sm font-semibold text-white shadow transition-colors hover:bg-teal-600">
                        Simpan
                    </button>
                </div>
            </div>
        </div>
    </form>
</section>
