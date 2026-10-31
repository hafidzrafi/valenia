<?php

/** @var string $title */
/** @var string $content */
?>
<!DOCTYPE html>
<html lang="id">

<head>
   <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Valenia</title>

    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Oswald:wght@500;600;700&family=Poppins:wght@300;400;500;600&display=swap" rel="stylesheet">

    <link rel="stylesheet" href="/assets/app.css">
</head>

<body class="min-h-screen bg-slate-50 text-slate-800 antialiased">
    <main>
        <?php echo $content; ?>
    </main>
    <script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.17.0/dist/cdn.min.js"></script>
    <script defer src="https://cdn.jsdelivr.net/npm/@alpinejs/morph@3.17.0/dist/cdn.min.js"></script>
    <script defer src="https://cdn.jsdelivr.net/npm/@alpinejs/intersect@3.17.0/dist/cdn.min.js"></script>
</body>

</html>