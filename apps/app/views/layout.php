<?php

/** @var null|string $title */
/** @var string $content */
?>
<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title><?php echo e($title ?? 'VALENIA'); ?></title>

    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/@phosphor-icons/web@2.1.2/src/regular/style.css">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/@phosphor-icons/web@2.1.2/src/bold/style.css">

    <link rel="stylesheet" href="/assets/app.css">
</head>

<body class="min-h-screen bg-slate-50 font-sans text-slate-800 antialiased">
    <main>
        <?php echo $content; ?>
    </main>
    <script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.17.0/dist/cdn.min.js"></script>
    <script defer src="https://cdn.jsdelivr.net/npm/@alpinejs/morph@3.17.0/dist/cdn.min.js"></script>
    <script defer src="https://cdn.jsdelivr.net/npm/@alpinejs/intersect@3.17.0/dist/cdn.min.js"></script>
</body>

</html>