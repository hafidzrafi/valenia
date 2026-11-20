<?php
/** @var list<array<string, mixed>> $boards */
?>
<div class="flex flex-wrap justify-center gap-6">
    <?php foreach ($boards as $board) { ?>
        <div class="flex min-w-[220px] flex-col items-center rounded-3xl bg-white/90 px-8 py-6 shadow-lg backdrop-blur-sm">
            <p class="text-sm font-medium uppercase tracking-wide text-slate-500">
                <?php echo e((string) $board['service_name']); ?>
            </p>
            <p class="mt-2 text-5xl font-black text-slate-900">
                <?php echo e(null !== $board['current_queue_code'] ? (string) $board['current_queue_code'] : '—'); ?>
            </p>
        </div>
    <?php } ?>
</div>
