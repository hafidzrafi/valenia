<?php
/** @var string $staffName */
/** @var string $staffRole */
?>
<section class="w-full max-w-[560px] rounded-[60px] border-2 border-white/60 bg-white px-8 py-7 font-sans shadow-[0_5px_18px_rgba(40,120,105,0.18)]">
    <h2 class="mb-5 text-center font-display text-[28px] font-semibold leading-[34px] text-slate-900">
        Status Tugas
    </h2>

    <div class="flex items-center justify-between gap-4 rounded-full bg-white py-2 pl-6 pr-2 shadow-[0_3px_8px_rgba(40,120,105,0.18)] ring-1 ring-slate-200">
        <div class="min-w-0">
            <p class="truncate text-[15px] font-semibold text-slate-900"><?php echo e($staffName); ?></p>
            <p class="truncate text-xs text-slate-500"><?php echo e($staffRole); ?></p>
        </div>
        <span class="inline-flex h-[32px] shrink-0 items-center rounded-full bg-[#10b981] px-5 text-[12px] font-semibold text-white">
            Aktif
        </span>
    </div>

    <p class="mt-5 text-center text-xs text-slate-500">
        Penanggung jawab medis tercatat otomatis sesuai akun yang sedang login.
    </p>
</section>
