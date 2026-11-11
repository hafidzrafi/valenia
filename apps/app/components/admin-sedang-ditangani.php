<?php
/** @var null|array<string, mixed> $served */
$served = $served ?? null;
?>
<section class="w-full max-w-[560px] rounded-[60px] border-2 border-white/60 bg-white px-8 py-7 font-sans shadow-[0_5px_18px_rgba(40,120,105,0.18)]">
    <h2 class="mb-5 text-center font-display text-[28px] font-semibold leading-[34px] text-slate-900">
        Sedang Ditangani
    </h2>

    <?php if (null === $served) { ?>
        <p class="py-6 text-center text-sm text-slate-500">Belum ada pasien yang sedang dilayani.</p>
    <?php } else { ?>
        <div class="mx-auto max-w-[360px] rounded-[28px] bg-[#d9f5ec] px-6 py-5 text-center shadow-[0_3px_8px_rgba(40,120,105,0.18)]">
            <p class="text-[40px] font-black leading-none text-slate-900">
                <?php echo e((string) ($served['queue_code'] ?? '')); ?>
            </p>
            <p class="mt-2 text-[15px] font-semibold text-teal-700">
                <?php echo e((string) ($served['full_name'] ?? '')); ?>
            </p>
            <p class="mt-1 text-xs text-slate-600">
                <?php echo e((string) ($served['identity_number'] ?? '')); ?>
                &middot;
                <?php echo e((string) ($served['service_name'] ?? '')); ?>
            </p>
            <?php if ('' !== (string) ($served['complaint'] ?? '')) { ?>
                <p class="mt-1 text-xs text-slate-600">
                    Keluhan: <?php echo e((string) $served['complaint']); ?>
                </p>
            <?php } ?>
        </div>
    <?php } ?>

    <p class="mt-5 text-center text-xs text-slate-500">
        Untuk memanggil pasien atau mengisi hasil penanganan, buka menu
        <a href="/admin/antrean" class="font-semibold text-teal-600 hover:underline">Antrian Pasien</a>.
    </p>
</section>
