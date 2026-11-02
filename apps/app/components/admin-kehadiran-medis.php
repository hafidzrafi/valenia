<?php
/** @var list<array<string, mixed>> $attendance */
$attendance = $attendance ?? [];
?>
<section class="smooth-corners-lg relative h-auto w-[631px] max-w-full py-5 sm:h-[298px] sm:py-0 border-2 border-white/60 bg-white/40 px-6 pt-5 shadow-[0_5px_18px_rgba(40,120,105,0.18)] backdrop-blur-md font-sans"
    style="border-radius: 60px;">
    <h2 class="mb-[16px] text-center font-display text-[28px] font-semibold leading-[34px] text-[#379a89]">Kehadiran Tenaga Medis</h2>

    <div class="flex flex-col gap-[18px]">
        <?php if ([] === $attendance) { ?>
            <p class="text-center text-[12px] text-slate-600">Belum ada tenaga medis terdaftar.</p>
        <?php } ?>

        <?php foreach ($attendance as $medis) { ?>
            <?php
            $present = (bool) $medis['is_present'];
            $start = null !== ($medis['start_time'] ?? null) ? substr((string) $medis['start_time'], 0, 5) : '';
            $end = null !== ($medis['end_time'] ?? null) ? substr((string) $medis['end_time'], 0, 5) : '';
            $hours = ('' !== $start && '' !== $end) ? $start.' - '.$end : '-';
            ?>
            <div class="flex flex-col gap-2 rounded-2xl bg-white p-3 shadow-[0_3px_5px_rgba(0,0,0,0.3)] sm:grid sm:h-[49px] sm:grid-cols-[204px_1fr_auto] sm:items-center sm:gap-0 sm:rounded-full sm:pl-[15px] sm:pr-[18px]">
                <span class="min-w-0 truncate pr-2 text-[11px] font-semibold text-black">
                    <?php echo e((string) $medis['full_name']); ?>
                </span>
                <span class="min-w-0 truncate text-[11px] text-black">
                    <?php echo e((string) $medis['service_name']); ?> &middot; <?php echo e($hours); ?>
                </span>

                <form method="POST" action="/admin/kehadiran" class="sm:justify-self-end">
                    <?php echo csrf_field(); ?>
                    <input type="hidden" name="doctor_id" value="<?php echo e((string) $medis['id']); ?>">
                    <input type="hidden" name="present" value="<?php echo $present ? '0' : '1'; ?>">
                    <button type="submit"
                        class="h-[29px] w-[82px] rounded-full text-[10px] font-semibold transition-colors <?php echo $present ? 'bg-[#10b981] text-white' : 'bg-[#d9d9d9] text-[#666]'; ?>">
                        <?php echo $present ? 'Hadir' : 'Kosong'; ?>
                    </button>
                </form>
            </div>
        <?php } ?>
    </div>
</section>
