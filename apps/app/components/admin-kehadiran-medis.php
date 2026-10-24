<?php

/**
 * Kartu Kehadiran Tenaga Medis (VALENIA-34). Read-only.
 * Lencana memakai components/tag.php.
 *
 * @var array<int, array{poli: string, jam: string, hadir: bool}> $kehadiranMedis
 */
$kehadiranMedis = $kehadiranMedis ?? [];
?>
<section class="smooth-corners-lg rounded-[32px] border border-white/70 bg-white/50 p-5 shadow-lg backdrop-blur-md">
    <h2 class="mb-4 text-center text-lg font-bold text-teal-700">Kehadiran Tenaga Medis</h2>
    <div class="flex flex-col gap-3">
        <?php foreach ($kehadiranMedis as $medis): ?>
            <?php
            $status = $medis['hadir'] ? 'positive' : 'disabled';
            $label = $medis['hadir'] ? 'Hadir' : 'Belum hadir';
            ?>
            <div class="smooth-corners-md flex items-center justify-between gap-3 rounded-2xl bg-white/80 px-5 py-3 shadow-sm">
                <p class="w-28 text-sm font-semibold text-slate-800"><?= e($medis['poli']) ?></p>
                <p class="text-sm text-slate-600"><?= e($medis['jam']) ?></p>
                <?php include __DIR__ . '/tag.php'; ?>
            </div>
        <?php endforeach; ?>
    </div>
</section>