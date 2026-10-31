<?php
/** @var list<array{id: string, username: string, full_name: string, role: string, is_active: bool, created_at: string, must_change_password: bool, failed_attempts: int|string, locked: bool}> $users */
/** @var string $token */
/** @var null|string $flash */
/** @var null|string $error */
?>
<h1 class="text-xl font-bold">Kelola Akun Pengguna</h1>
<p class="mt-1 text-slate-600 text-sm"><a class="underline" href="/admin">&larr; Kembali</a></p>

<?php if (!empty($flash)) { ?>
    <p class="mt-3 text-emerald-700 text-sm"><?php echo e($flash); ?></p>
<?php } ?>

<?php if (!empty($error)) { ?>
    <p class="mt-3 text-rose-600 text-sm"><?php echo e($error); ?></p>
<?php } ?>

<h2 class="mt-5 font-semibold">Tambah akun</h2>
<form method="POST" action="/admin/pengguna" class="mt-2 flex flex-wrap gap-2 text-sm">
    <input type="hidden" name="_token" value="<?php echo e($token); ?>">
    <input type="hidden" name="aksi" value="tambah">
    <input type="text" name="username" placeholder="Nama pengguna" required
           class="border border-slate-300 rounded px-2 py-1">
    <input type="text" name="full_name" placeholder="Nama lengkap" required
           class="border border-slate-300 rounded px-2 py-1">
    <select name="role" class="border border-slate-300 rounded px-2 py-1">
        <option value="perawat">perawat</option>
        <option value="admin">admin</option>
    </select>
    <input type="password" name="password" placeholder="Kata sandi (min. 8)" required
           class="border border-slate-300 rounded px-2 py-1">
    <button type="submit" class="bg-slate-900 text-white rounded px-3 py-1">Tambah</button>
</form>

<table class="mt-4 w-full text-sm border-collapse">
    <thead>
        <tr class="text-left border-b border-slate-300">
            <th class="py-1">Pengguna</th>
            <th>Nama</th>
            <th>Peran</th>
            <th>Status</th>
            <th>Aksi</th>
        </tr>
    </thead>
    <tbody>
        <?php foreach ($users as $row) { ?>
            <tr class="border-b border-slate-200">
                <td class="py-1"><?php echo e($row['username']); ?></td>
                <td><?php echo e($row['full_name']); ?></td>
                <td><?php echo e($row['role']); ?></td>
                <td>
                    <?php echo $row['is_active'] ? 'aktif' : 'nonaktif'; ?><?php echo !empty($row['locked']) ? ' · terkunci' : ''; ?><?php echo !empty($row['must_change_password']) ? ' · wajib ganti sandi' : ''; ?>
                </td>
                <td class="flex gap-2 py-1">
                    <form method="POST" action="/admin/pengguna" class="inline">
                        <input type="hidden" name="_token" value="<?php echo e($token); ?>">
                        <input type="hidden" name="aksi" value="<?php echo $row['is_active'] ? 'nonaktif' : 'aktif'; ?>">
                        <input type="hidden" name="id" value="<?php echo e((string) $row['id']); ?>">
                        <button type="submit" class="underline"><?php echo $row['is_active'] ? 'Nonaktifkan' : 'Aktifkan'; ?></button>
                    </form>
                    <form method="POST" action="/admin/pengguna" class="inline">
                        <input type="hidden" name="_token" value="<?php echo e($token); ?>">
                        <input type="hidden" name="aksi" value="reset">
                        <input type="hidden" name="id" value="<?php echo e((string) $row['id']); ?>">
                        <input type="password" name="password" placeholder="Sandi sementara (min. 8)" required
                               class="border border-slate-300 rounded px-1 py-0.5">
                        <button type="submit" class="underline">Reset sandi</button>
                    </form>
                </td>
            </tr>
        <?php } ?>
    </tbody>
</table>
