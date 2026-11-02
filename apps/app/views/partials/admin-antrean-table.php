<?php
/** @var list<array<string, mixed>> $visits */
$visits = $visits ?? [];

$statusClass = [
    'menunggu' => 'bg-[#d9d9d9] text-[#666]',
    'dilayani' => 'bg-amber-500 text-white',
    'selesai' => 'bg-[#10b981] text-white',
    'batal' => 'bg-rose-200 text-rose-700',
];

$statusLabel = [
    'menunggu' => 'Menunggu',
    'dilayani' => 'Dilayani',
    'selesai' => 'Selesai',
    'batal' => 'Dibatalkan',
];
?>
<section class="mt-8 font-sans">
    <h2 class="mb-3 text-[18px] font-semibold text-slate-900">Antrean terbaru hari ini</h2>

    <div class="overflow-x-auto rounded-3xl border border-slate-200 bg-white shadow-sm">
        <table class="w-full min-w-[640px] border-collapse text-left text-[13px] text-slate-900">
            <thead>
                <tr class="border-b border-slate-200 text-[11px] uppercase tracking-wide text-slate-500">
                    <th class="px-5 py-3 font-semibold">No</th>
                    <th class="px-5 py-3 font-semibold">Nama</th>
                    <th class="px-5 py-3 font-semibold">NIM/NIP</th>
                    <th class="px-5 py-3 font-semibold">Poli</th>
                    <th class="px-5 py-3 text-right font-semibold">Status</th>
                </tr>
            </thead>
            <tbody>
                <?php if ([] === $visits) { ?>
                    <tr>
                        <td colspan="5" class="px-5 py-5 text-center text-slate-500">
                            Belum ada antrean hari ini.
                        </td>
                    </tr>
                <?php } ?>

                <?php foreach ($visits as $visit) { ?>
                    <?php
                    $status = (string) $visit['status'];
                    $pill = $statusClass[$status] ?? $statusClass['menunggu'];
                    ?>
                    <tr class="border-b border-slate-100 last:border-b-0 hover:bg-teal-50/60">
                        <td class="px-5 py-3">
                            <a href="/admin/kunjungan?visit=<?php echo e((string) $visit['id']); ?>"
                                class="font-semibold text-teal-700 hover:underline">
                                <?php echo e((string) ($visit['queue_code'] ?? '')); ?>
                            </a>
                        </td>
                        <td class="px-5 py-3">
                            <a href="/admin/kunjungan?visit=<?php echo e((string) $visit['id']); ?>" class="block">
                                <?php echo e((string) $visit['full_name']); ?>
                            </a>
                        </td>
                        <td class="px-5 py-3 text-slate-600"><?php echo e((string) $visit['identity_number']); ?></td>
                        <td class="px-5 py-3 text-slate-600"><?php echo e((string) $visit['service_name']); ?></td>
                        <td class="px-5 py-3 text-right">
                            <span class="inline-flex h-[22px] items-center rounded-full px-3 text-[10px] font-semibold <?php echo $pill; ?>">
                                <?php echo e($statusLabel[$status] ?? $status); ?>
                            </span>
                        </td>
                    </tr>
                <?php } ?>
            </tbody>
        </table>
    </div>
</section>
