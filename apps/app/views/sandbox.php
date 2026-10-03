<?php
/** @var list<array{id: int|string, name: string, created_at: string}> $items */
/** @var string $token */
/** @var string|null $error */
?>
<h1 class="text-xl font-bold">Sandbox</h1>
<p class="mt-1 text-slate-600 text-sm">Contoh query PDO inline di berkas rute, dilindungi CSRF.</p>

<?php if (!empty($error)): ?>
    <p class="mt-3 text-rose-600 text-sm"><?= e($error) ?></p>
<?php endif; ?>

<form method="POST" action="/sandbox" class="mt-4 flex gap-2">
    <input type="hidden" name="_token" value="<?= e($token) ?>">
    <input type="text" name="name" placeholder="Nama barang" required
           class="border border-slate-300 rounded px-2 py-1">
    <button type="submit" class="bg-slate-900 text-white rounded px-3 py-1">Tambah</button>
</form>

<table class="mt-4 w-full text-sm border-collapse">
    <thead>
        <tr class="text-left border-b border-slate-300">
            <th class="py-1">ID</th>
            <th>Nama</th>
            <th>Dibuat</th>
        </tr>
    </thead>
    <tbody>
        <?php foreach ($items as $item): ?>
            <tr class="border-b border-slate-200">
                <td class="py-1"><?= e((string) $item['id']) ?></td>
                <td><?= e($item['name']) ?></td>
                <td><?= e($item['created_at']) ?></td>
            </tr>
        <?php endforeach; ?>
        <?php if ($items === []): ?>
            <tr><td colspan="3" class="py-2 text-slate-500">Belum ada data.</td></tr>
        <?php endif; ?>
    </tbody>
</table>
