<?php
/** @var list<array<string, mixed>> $boards */
/** @var null|string $flash */
?>
<h1 class="text-2xl font-bold text-slate-900">Antrean Poliklinik</h1>
<p class="mt-1 text-sm text-slate-600">
    <a href="/admin" class="underline">&larr; Kembali ke area admin</a>
</p>

<?php if (null !== $flash) { ?>
    <p class="mt-4 rounded-2xl bg-emerald-50 px-4 py-2 text-sm text-emerald-700"><?php echo e($flash); ?></p>
<?php } ?>

<div id="queue-boards"
     hx-get="/admin/antrean"
     hx-trigger="every 5s"
     hx-swap="innerHTML"
     class="mt-6">
    <?php include __DIR__.'/partials/queue-boards.php'; ?>
</div>
