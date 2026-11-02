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
<section class="font-sans">
    <h2 class="mb-[22px] ml-3 font-display text-[22px] font-semibold leading-[30px] text-white">Daftar Antrian Aktif</h2>

    <div class="smooth-corners-sm w-full overflow-hidden bg-[#f0fffb] pb-[7px] shadow-[0_4px_14px_rgba(30,110,95,0.18)]"
        style="border-radius: 24px;">
        <table class="ml-[27px] w-[calc(100%-27px)] table-fixed border-collapse text-left text-[13px] text-black">
            <colgroup>
                <col style="width:142px"><col style="width:210px"><col style="width:271px"><col style="width:154px"><col>
            </colgroup>
            <thead>
                <tr class="h-[45px] border-b border-slate-800 align-top text-[19px] font-light leading-[26px] text-[#555]">
                    <th class="pl-2.5 pt-[11px] font-light">No</th>
                    <th class="pt-[11px] font-light">Nama</th>
                    <th class="pt-[11px] font-light">NIM/NIP</th>
                    <th class="pt-[11px] font-light">Poli</th>
                    <th class="pt-[11px] text-center font-light">Status</th>
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

                                <?php if ('dilayani' === $status) { ?>
                                    <form method="POST" action="/admin/selesai">
                                        <?php echo csrf_field(); ?>
                                        <input type="hidden" name="visit_id" value="<?php echo e((string) $row['id']); ?>">
                                        <button type="submit"
                                            class="h-[22px] rounded-full bg-teal-600 px-3 text-[10px] font-semibold text-white transition-colors hover:bg-teal-700">
                                            Selesaikan
                                        </button>
                                    </form>
                                <?php } ?>
                            </div>
                        </td>
                    </tr>
                <?php } ?>
            </tbody>
        </table>
    </div>
</section>
