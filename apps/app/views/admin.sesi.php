<?php
/** @var list<array{id: string, user_id: string, username: string, role: string, ip_address: ?string, user_agent: ?string, created_at: string, last_seen_at: string, expires_at: string}> $sessions */
/** @var null|string $current */
/** @var string $token */
/** @var null|string $flash */
/** @var null|string $error */
?>
<h1 class="text-xl font-bold">Kelola Sesi Aktif</h1>
<p class="mt-1 text-slate-600 text-sm"><a class="underline" href="/admin">&larr; Kembali</a></p>

<?php if (!empty($flash)) { ?>
    <p class="mt-3 text-emerald-700 text-sm"><?php echo e($flash); ?></p>
<?php } ?>

<?php if (!empty($error)) { ?>
    <p class="mt-3 text-rose-600 text-sm"><?php echo e($error); ?></p>
<?php } ?>

<table class="mt-4 w-full text-sm border-collapse">
    <thead>
        <tr class="text-left border-b border-slate-300">
            <th class="py-1">Pengguna</th>
            <th>IP</th>
            <th>Terakhir aktif</th>
            <th>Aksi</th>
        </tr>
    </thead>
    <tbody>
        <?php foreach ($sessions as $row) { ?>
            <tr class="border-b border-slate-200">
                <td class="py-1">
                    <?php echo e($row['username']); ?> (<?php echo e($row['role']); ?>)<?php echo null !== $current && $row['id'] === $current ? ' · sesi ini' : ''; ?>
                </td>
                <td><?php echo e((string) ($row['ip_address'] ?? '-')); ?></td>
                <td><?php echo e($row['last_seen_at']); ?></td>
                <td>
                    <form method="POST" action="/admin/sesi" class="inline">
                        <input type="hidden" name="_token" value="<?php echo e($token); ?>">
                        <input type="hidden" name="id" value="<?php echo e((string) $row['id']); ?>">
                        <button type="submit" class="underline">Cabut</button>
                    </form>
                </td>
            </tr>
        <?php } ?>
        <?php if ([] === $sessions) { ?>
            <tr><td colspan="4" class="py-2 text-slate-500">Tidak ada sesi aktif.</td></tr>
        <?php } ?>
    </tbody>
</table>
