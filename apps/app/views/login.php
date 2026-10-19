<?php
/** @var string $token */
/** @var string|null $error */
/** @var string|null $username */
?>
<h1 class="text-xl font-bold">Masuk</h1>
<p class="mt-1 text-slate-600 text-sm">Masuk dengan akun Admin atau Perawat.</p>

<?php if (!empty($error)): ?>
    <p class="mt-3 text-rose-600 text-sm"><?= e($error) ?></p>
<?php endif; ?>

<form method="POST" action="/login" class="mt-4 space-y-3 max-w-sm">
    <input type="hidden" name="_token" value="<?= e($token) ?>">
    <div>
        <label class="block text-sm" for="username">Nama pengguna</label>
        <input id="username" type="text" name="username" required autocomplete="username"
               value="<?= e($username ?? '') ?>"
               class="mt-1 w-full border border-slate-300 rounded px-2 py-1">
    </div>
    <div>
        <label class="block text-sm" for="password">Kata sandi</label>
        <input id="password" type="password" name="password" required autocomplete="current-password"
               class="mt-1 w-full border border-slate-300 rounded px-2 py-1">
    </div>
    <button type="submit" class="bg-slate-900 text-white rounded px-3 py-1">Masuk</button>
</form>
