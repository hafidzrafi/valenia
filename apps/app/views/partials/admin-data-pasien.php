<?php
/** @var array<string, mixed> $visit */
$doctor = $visit['doctor_on_duty'] ?? null;
$doctorName = is_array($doctor) ? (string) ($doctor['full_name'] ?? '') : '';

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

$status = (string) $visit['status'];
$date = new DateTimeImmutable((string) $visit['queue_date']);
?>
<section class="w-full max-w-[560px] rounded-[60px] border-2 border-white/60 bg-white/80 px-8 py-7 shadow-[0_5px_18px_rgba(40,120,105,0.18)] backdrop-blur-md font-sans">
    <div class="mb-5 flex items-center justify-between gap-4">
        <h2 class="font-display text-[28px] font-semibold leading-[34px] text-slate-900">Data Pasien</h2>
        <span class="rounded-full px-4 py-1 text-[11px] font-semibold <?php echo $statusClass[$status] ?? $statusClass['menunggu']; ?>">
            <?php echo e($statusLabel[$status] ?? $status); ?>
        </span>
    </div>

    <dl class="grid grid-cols-2 gap-x-6 gap-y-4 text-[13px]">
        <div>
            <dt class="text-[11px] text-slate-500">Tanggal</dt>
            <dd class="font-semibold text-slate-900"><?php echo e($date->format('d/m/Y')); ?></dd>
        </div>
        <div>
            <dt class="text-[11px] text-slate-500">No. antrian</dt>
            <dd class="font-semibold text-slate-900"><?php echo e((string) ($visit['queue_code'] ?? '')); ?></dd>
        </div>
        <div>
            <dt class="text-[11px] text-slate-500">Nama</dt>
            <dd class="font-semibold text-slate-900"><?php echo e((string) $visit['full_name']); ?></dd>
        </div>
        <div>
            <dt class="text-[11px] text-slate-500">NIM/NIP</dt>
            <dd class="font-semibold text-slate-900"><?php echo e((string) $visit['identity_number']); ?></dd>
        </div>
        <div>
            <dt class="text-[11px] text-slate-500">Poli</dt>
            <dd class="font-semibold text-slate-900"><?php echo e((string) $visit['service_name']); ?></dd>
        </div>
        <div>
            <dt class="text-[11px] text-slate-500">Keluhan</dt>
            <dd class="font-semibold text-slate-900"><?php echo e((string) ($visit['complaint'] ?? '-')); ?></dd>
        </div>
    </dl>

    <div class="mt-5 border-t border-slate-200 pt-4 text-[13px]">
        <p class="text-[11px] text-slate-500">Penanggung jawab medis</p>
        <p class="font-semibold text-slate-900">
            <?php echo '' !== $doctorName ? e($doctorName).' (Dokter)' : 'Belum ditentukan'; ?>
        </p>
    </div>
</section>
