<?php
/** @var list<array{time: string, code: string, patient: string, event: string, actor: string}> $activity */
$activity = $activity ?? [];

$eventClass = [
    'Didaftarkan' => 'bg-[#d9f5ec] text-[#1f7a6b]',
    'Dipanggil' => 'bg-amber-100 text-amber-700',
    'Selesai' => 'bg-[#d1fae5] text-[#047857]',
    'Dibatalkan' => 'bg-rose-100 text-rose-700',
];
?>
<section class="mt-8 font-sans">
    <h2 class="mb-[14px] ml-3 font-display text-[22px] font-semibold leading-[30px] text-white">Aktivitas Antrian Hari Ini</h2>

    <div class="smooth-corners-sm w-full overflow-hidden bg-[#f0fffb] shadow-[0_4px_14px_rgba(30,110,95,0.18)]"
        style="border-radius: 24px;">
        <?php if ([] === $activity) { ?>
            <p class="px-6 py-5 text-center text-[13px] text-slate-500">Belum ada aktivitas hari ini.</p>
        <?php } else { ?>
            <ul class="divide-y divide-slate-200/70">
                <?php foreach ($activity as $row) { ?>
                    <?php $chip = $eventClass[$row['event']] ?? 'bg-slate-100 text-slate-600'; ?>
                    <li class="flex flex-wrap items-center gap-x-4 gap-y-1 px-6 py-3 text-[13px] text-black">
                        <span class="w-[46px] shrink-0 tabular-nums text-slate-500"><?php echo e($row['time']); ?></span>
                        <span class="w-[66px] shrink-0 font-semibold"><?php echo e($row['code']); ?></span>
                        <span class="min-w-0 flex-1 truncate"><?php echo e($row['patient']); ?></span>
                        <span class="rounded-full px-3 py-[3px] text-[11px] font-semibold <?php echo $chip; ?>">
                            <?php echo e($row['event']); ?>
                        </span>
                        <span class="w-[130px] shrink-0 truncate text-right text-[12px] text-slate-500">
                            <?php echo e($row['actor']); ?>
                        </span>
                    </li>
                <?php } ?>
            </ul>
        <?php } ?>
    </div>
</section>
