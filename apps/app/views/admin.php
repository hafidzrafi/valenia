<?php
/** @var string $user */
/** @var string $role */
/** @var string $token */
?>
<h1 class="text-xl font-bold">Area Admin</h1>
<p class="mt-2 text-slate-600 text-sm">
    Masuk sebagai <strong><?= e($user) ?></strong> (<?= e($role) ?>).
</p>

<ul class="mt-4 space-y-2 text-sm">
    <li><a class="text-teal-700 underline" href="/admin/pengguna">Kelola akun pengguna</a></li>
    <li><a class="text-teal-700 underline" href="/admin/sesi">Kelola sesi aktif</a></li>
</ul>

<form method="POST" action="/logout" class="mt-4">
    <input type="hidden" name="_token" value="<?= e($token) ?>">
    <button type="submit" class="bg-slate-900 text-white rounded px-3 py-1 text-sm">Keluar</button>
</form>
