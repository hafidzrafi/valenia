<?php
/** @var list<array<string, mixed>> $boards */
?>
<div class="grid gap-4 md:grid-cols-3">
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
</div>

<?php include __DIR__.'/admin-antrean-table.php'; ?>
