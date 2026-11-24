<?php
/**
 * Satu baris aktivitas akun (VALENIA-44). Dipakai kartu "Aktivitas Terbaru" di
 * /admin/profil dan daftar di /admin/profil/aktivitas.
 *
 * @var array{ikon: string, judul: string, detail: string, waktu: string} $baris
 */
?>
<li class="flex items-center gap-3 border-t border-slate-200 py-3">
    <span class="flex size-8 shrink-0 items-center justify-center rounded-full bg-teal-100 text-teal-700" aria-hidden="true">
        <i class="ph <?php echo e($baris['ikon']); ?> text-[16px]"></i>
    </span>
    <div class="min-w-0 flex-1">
        <p class="truncate text-sm font-semibold text-slate-800"><?php echo e($baris['judul']); ?></p>
        <p class="truncate text-xs text-slate-500"><?php echo e($baris['detail']); ?></p>
    </div>
    <time class="shrink-0 text-xs text-slate-500"><?php echo e($baris['waktu']); ?></time>
</li>
