<?php /** @var string $user */ ?>
<h1 class="text-xl font-bold">Area Admin</h1>
<p class="mt-2 text-slate-600 text-sm">
    Halaman ini hanya dapat diakses setelah lolos guard <code>auth</code> dan <code>role:admin</code>.
</p>
<p class="mt-2 text-sm">Pengguna terverifikasi: <strong><?= e($user) ?></strong></p>
