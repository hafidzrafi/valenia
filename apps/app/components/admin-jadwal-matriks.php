<?php
/**
 * Weekly schedule matrix: one row per active doctor, one column per ISO weekday.
 * The row action and each slot chip open a modal defined in the page view.
 *
 * The doctor column is sticky so it stays visible when the table scrolls
 * sideways.
 */
/** @var list<array<string, mixed>> $doctors */
/** @var array<int, string> $days */
/** @var int $todayDow */
$doctors = $doctors ?? [];
$totalSlots = array_sum(array_map(
    static fn (array $doctor): int => array_sum(array_map(count(...), $doctor['slots'])),
    $doctors,
));
?>
<section class="rounded-[40px] border-2 border-white/60 bg-white p-6 font-sans shadow-[0_5px_18px_rgba(40,120,105,0.18)]">
    <div class="mb-5 flex flex-wrap items-end justify-between gap-3">
        <div>
            <h2 class="font-display text-[28px] font-semibold leading-[34px] text-[#379a89]">Jadwal Mingguan</h2>
            <p class="mt-1 text-xs text-teal-950/80">
                <?php echo count($doctors); ?> tenaga medis aktif
                &middot; <?php echo $totalSlots; ?> slot per minggu
            </p>
        </div>

        <button type="button"
                @click="tambah = { doctor_id: '', day: today, start: '', end: '' }"
                class="inline-flex h-9 items-center gap-1.5 rounded-full bg-teal-700 px-4 text-xs font-semibold text-white shadow-[0_2px_6px_rgba(20,90,80,0.25)] transition-colors hover:bg-teal-800 focus:outline-none focus-visible:ring-2 focus-visible:ring-teal-600 focus-visible:ring-offset-2">
            <i class="ph ph-plus text-[14px]" aria-hidden="true"></i>
            Tambah jadwal
        </button>
    </div>

    <div class="overflow-x-auto">
        <table class="w-full min-w-[900px] table-fixed border-collapse text-left text-xs text-slate-800">
            <caption class="sr-only">Jadwal praktik mingguan per tenaga medis</caption>
            <colgroup>
                <col class="w-[200px]">
                <?php foreach (array_keys($days) as $ignored) { ?>
                    <col>
                <?php } ?>
            </colgroup>
            <thead>
                <tr class="border-b border-teal-900/15">
                    <th scope="col" class="sticky left-0 z-10 whitespace-nowrap border-b border-r border-teal-900/15 bg-white px-3 pt-4 pb-3 text-xs font-semibold uppercase tracking-[0.06em] text-slate-600 shadow-[6px_0_10px_-8px_rgba(40,120,105,0.45)]">Tenaga Medis</th>
                    <?php foreach ($days as $dayNumber => $dayLabel) { ?>
                        <?php
                        $isToday = $dayNumber === $todayDow;
                        $dayLabelClass = 'text-slate-600';
                        if ($isToday) {
                            $dayLabelClass = 'bg-teal-700 text-white';
                        }
                        ?>
                        <th scope="col" class="border-l border-teal-900/10 px-1 py-2 text-center">
                            <span class="inline-flex items-center rounded-full px-2.5 py-1 text-xs font-semibold uppercase tracking-[0.06em] <?php echo $dayLabelClass; ?>"
                                <?php echo $isToday ? 'aria-current="date"' : ''; ?>><?php echo e($dayLabel); ?></span>
                        </th>
                    <?php } ?>
                </tr>
            </thead>
            <tbody>
                <?php if ([] === $doctors) { ?>
                    <tr>
                        <td colspan="<?php echo 1 + count($days); ?>" class="px-3 py-10 text-center text-sm text-slate-700">
                            Belum ada tenaga medis aktif.
                            <a href="/admin/data-master?tab=tenaga-medis" class="font-semibold text-teal-700 underline underline-offset-2 hover:text-teal-800">Tambahkan dari Data Master</a>.
                        </td>
                    </tr>
                <?php } ?>

                <?php foreach ($doctors as $doctor) { ?>
                    <tr class="group border-t border-teal-900/10 transition-colors hover:bg-teal-50/40">
                        <th scope="row" class="sticky left-0 z-10 border-r border-teal-900/10 bg-white px-3 py-2 text-left align-top shadow-[6px_0_10px_-8px_rgba(40,120,105,0.45)]">
                            <span class="block whitespace-nowrap text-[13px] font-semibold leading-snug text-slate-900"><?php echo e($doctor['full_name']); ?></span>
                            <span class="mt-0.5 block whitespace-nowrap text-xs font-normal leading-snug text-slate-600"><?php echo e($doctor['service_name']); ?></span>
                        </th>

                        <?php foreach (array_keys($days) as $dayNumber) { ?>
                            <?php
                            $slots = $doctor['slots'][$dayNumber] ?? [];
                            $isToday = $dayNumber === $todayDow;
                            ?>
                            <td class="border-l border-teal-900/10 px-1 py-2 align-top <?php echo $isToday ? 'bg-teal-600/[0.06]' : ''; ?>">
                                <div class="flex flex-col gap-1">
                                    <?php foreach ($slots as $slot) { ?>
                                        <button type="button"
                                                @click="hapus = <?php echo e((string) json_encode([
                                                    'id' => $slot['id'],
                                                    'nama' => $doctor['full_name'],
                                                    'hari' => $days[$dayNumber],
                                                    'jam' => $slot['start_time'].'-'.$slot['end_time'],
                                                ])); ?>"
                                                class="w-full whitespace-nowrap rounded-full bg-teal-100 px-2 py-1 text-center text-[11px] font-semibold tabular-nums text-teal-900 transition-colors hover:bg-rose-100 hover:text-rose-900 focus:outline-none focus-visible:ring-2 focus-visible:ring-rose-600"
                                                title="Hapus jadwal"
                                                aria-label="Hapus jadwal <?php echo e($doctor['full_name']); ?> hari <?php echo e($days[$dayNumber]); ?> pukul <?php echo e($slot['start_time']); ?> sampai <?php echo e($slot['end_time']); ?>">
                                            <?php echo e($slot['start_time'].'-'.$slot['end_time']); ?>
                                        </button>
                                    <?php } ?>

                                    <button type="button"
                                            @click="tambah = <?php echo e((string) json_encode([
                                                'doctor_id' => $doctor['id'],
                                                'day' => $dayNumber,
                                                'start' => '',
                                                'end' => '',
                                            ])); ?>"
                                            class="flex h-7 w-full items-center justify-center rounded-full text-teal-700/40 opacity-0 transition-all group-hover:opacity-100 hover:bg-teal-100 hover:text-teal-800 focus:opacity-100 focus:outline-none focus-visible:ring-2 focus-visible:ring-teal-700"
                                            aria-label="Tambah jadwal <?php echo e($doctor['full_name']); ?> hari <?php echo e($days[$dayNumber]); ?>">
                                        <i class="ph ph-plus text-[13px]" aria-hidden="true"></i>
                                    </button>
                                </div>
                            </td>
                        <?php } ?>
                    </tr>
                <?php } ?>
            </tbody>
        </table>
    </div>
</section>
