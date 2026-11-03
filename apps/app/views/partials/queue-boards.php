<?php
/** @var list<array<string, mixed>> $boards */
?>
<div class="grid gap-4 md:grid-cols-3" x-data="{ confirmService: null, confirmCode: '' }">
    <?php foreach ($boards as $board) { ?>
        <div class="rounded-3xl border border-slate-200 bg-white p-5 shadow-sm">
            <h2 class="text-lg font-semibold text-slate-900"><?php echo e((string) $board['service_name']); ?></h2>

            <p class="mt-4 text-xs font-semibold uppercase tracking-wide text-slate-500">Sedang dilayani</p>
            <p class="text-4xl font-black text-slate-900">
                <?php echo e(null !== $board['current_queue_code'] ? (string) $board['current_queue_code'] : '—'); ?>
            </p>

            <p class="mt-4 text-xs font-semibold uppercase tracking-wide text-slate-500">Menunggu</p>
            <p class="text-2xl font-semibold text-slate-900"><?php echo e((string) $board['waiting_count']); ?></p>
            <p class="text-sm text-slate-500">
                Berikutnya: <?php echo e(null !== $board['next_queue_code'] ? (string) $board['next_queue_code'] : '—'); ?>
            </p>

            <form method="POST" action="/admin/antrean" class="mt-5" id="panggil-<?php echo (int) $board['service_id']; ?>"
                @submit.prevent="confirmService = '<?php echo (int) $board['service_id']; ?>'; confirmCode = '<?php echo e((string) ($board['next_queue_code'] ?? '')); ?>'">
                <?php echo csrf_field(); ?>
                <input type="hidden" name="service_id" value="<?php echo e((string) $board['service_id']); ?>">
                <button type="submit"
                    <?php echo 0 === (int) $board['waiting_count'] ? 'disabled' : ''; ?>
                    class="w-full rounded-2xl bg-teal-500 px-4 py-2 font-medium text-white transition-colors hover:bg-teal-600 disabled:cursor-not-allowed disabled:opacity-40">
                    Panggil berikutnya
                </button>
            </form>
        </div>
    <?php } ?>

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
                    @click="document.getElementById('panggil-' + confirmService).submit()"
                    class="h-[40px] rounded-full bg-teal-500 px-8 text-sm font-semibold text-white shadow transition-colors hover:bg-teal-600">
                    Panggil
                </button>
            </div>
        </div>
    </div>
</div>
