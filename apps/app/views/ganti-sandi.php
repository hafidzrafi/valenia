<?php
/** @var string $token */
/** @var null|string $error */
?>
<h1 class="text-xl font-bold">Ganti Kata Sandi</h1>
<p class="mt-1 text-slate-600 text-sm">Akun ini wajib mengganti kata sandi sebelum melanjutkan.</p>

<?php if (!empty($error)) { ?>
    <p class="mt-3 text-rose-600 text-sm"><?php echo e($error); ?></p>
<?php } ?>

<form method="POST" action="/ganti-sandi" class="mt-4 space-y-3 max-w-sm">
    <input type="hidden" name="_token" value="<?php echo e($token); ?>">
    <div>
        <label class="block text-sm" for="kata_sandi_saat_ini">Kata sandi saat ini</label>
        <input id="kata_sandi_saat_ini" type="password" name="kata_sandi_saat_ini" required
               autocomplete="current-password"
               class="mt-1 w-full border border-slate-300 rounded px-2 py-1">
    </div>
    <div>
        <label class="block text-sm" for="kata_sandi_baru">Kata sandi baru (min. 8 karakter)</label>
        <input id="kata_sandi_baru" type="password" name="kata_sandi_baru" required
               autocomplete="new-password"
               class="mt-1 w-full border border-slate-300 rounded px-2 py-1">
    </div>
    <div>
        <label class="block text-sm" for="konfirmasi_kata_sandi">Konfirmasi kata sandi baru</label>
        <input id="konfirmasi_kata_sandi" type="password" name="konfirmasi_kata_sandi" required
               autocomplete="new-password"
               class="mt-1 w-full border border-slate-300 rounded px-2 py-1">
    </div>
    <button type="submit" class="bg-slate-900 text-white rounded px-3 py-1">Simpan</button>
</form>
