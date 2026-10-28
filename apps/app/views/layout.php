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
    <main>
        <?= $content ?>
    </main>
    <script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.17.0/dist/cdn.min.js"></script>
    <script defer src="https://cdn.jsdelivr.net/npm/@alpinejs/morph@3.17.0/dist/cdn.min.js"></script>
    <script defer src="https://cdn.jsdelivr.net/npm/@alpinejs/intersect@3.17.0/dist/cdn.min.js"></script>
</body>

</html>