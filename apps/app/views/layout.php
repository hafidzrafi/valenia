<?php
/** @var string $title */
/** @var string $content */
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= e($title) ?></title>
    <link rel="stylesheet" href="/assets/app.css">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/@phosphor-icons/web@2.1.2/src/regular/style.css">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/@phosphor-icons/web@2.1.2/src/bold/style.css">
    <script src="https://cdn.jsdelivr.net/npm/htmx.org@2.0.10/dist/htmx.min.js" defer></script>
</head>
<body class="min-h-screen bg-slate-50 text-slate-800 antialiased">
    <nav class="mx-auto max-w-2xl flex items-center gap-4 p-4 text-sm">
        <a href="/">Beranda</a>
        <?php $u = current_user(); ?>
        <?php if ($u === null): ?>
            <a href="/login">Masuk</a>
        <?php else: ?>
            <?php if (($u['role'] ?? '') === 'admin'): ?>
                <a href="/admin">Admin</a>
            <?php endif; ?>
            <span class="ml-auto text-slate-500"><?= e($u['username']) ?></span>
            <form method="POST" action="/logout" class="inline">
                <input type="hidden" name="_token" value="<?= e(csrf_token()) ?>">
                <button type="submit" class="underline">Keluar</button>
            </form>
        <?php endif; ?>
    </nav>
    <main class="mx-auto max-w-2xl p-4">
        <?= $content ?>
    </main>
    <script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.17.0/dist/cdn.min.js"></script>
</body>
</html>
