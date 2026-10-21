<?php

/**
 * Home page (VALENIA-29) — standalone full page served at "/".
 *
 * IMPORTANT: this is a complete <html> document, not a content partial. Render
 * it via render(app_path('app/views/home.php'), [...]) from the route. Do NOT use
 * view('home'), which would wrap it in layout.php and emit two <html> documents.
 *
 */
$heroform = $_GET['heroform'] ?? "Default";


include __DIR__ . '/../components/background-img.php';
ob_start();
?>


<body>
    <div class="m-13 ml-15 absolute">
        <?php include __DIR__ . '/../components/form-breadcrumb.php'; ?>
    </div>


    <div class="min-h-screen flex flex-col">
        <div class="grow flex items-center justify-center">
            <div class="w-260 h-88  flex flex-col smooth-corners-lg rounded-[64px] bg-white">
                <h1 class="text-[64px] p-6 pl-15">  <?= htmlspecialchars($heroform) ?> </h1>
            </div>
        </div>

        <div>
            <?php include __DIR__ . '/../components/subfooter.php'; ?>
        </div>
    </div>

</body>


<?php
$content = ob_get_clean();
include __DIR__ . '/../views/layout.php';
?>