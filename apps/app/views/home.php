<?php
/**
 * Home page (VALENIA-29) — standalone full page served at "/".
 *
 * IMPORTANT: this is a complete <html> document, not a content partial. Render
 * it via render(app_path('app/views/home.php'), [...]) from the route. Do NOT use
 * view('home'), which would wrap it in layout.php and emit two <html> documents.
 *
 * @var string $title
 */
?>
<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= e($title ?? 'VALENIA') ?></title>
    <link rel="stylesheet" href="/assets/app.css">
</head>

<body class="min-h-screen bg-cover bg-center bg-no-repeat bg-fixed" style="background-image: url('/images/background.png');">

    <div class="w-[1200px] mx-auto mt-6 h-20 px-8 py-4 bg-gradient-to-br from-white/90 to-white/60 rounded-[64px] shadow-[6px_7px_7.099999904632568px_0px_rgba(0,0,0,0.25)] outline outline-4 outline-offset-[-4px] outline-white backdrop-blur-xl flex justify-between items-center">
        <div class="flex items-center gap-3">
            <div class="size-16 relative overflow-hidden flex items-center justify-center">
                <img src="/images/valenia-logo.svg" alt="Logo VALENIA" class="w-full h-full object-contain" />
            </div>
            <span class="text-teal-500 text-2xl font-light font-stretch:expanded font-['SF_Pro_Display'] tracking-wider">
                VALENIA
            </span>
        </div>
        <div class="flex items-center gap-12">
            <nav class="flex items-center gap-12 text-black text-base font-['SF_Pro_Display']">
                <a href="/" class="hover:text-teal-500 transition-colors mx-10">Beranda</a>

                <!-- Dropdown Layanan -->
                <div class="relative group mx-10">
                    <a href="#" class="hover:text-teal-500 transition-colors flex items-center gap-1">
                        Layanan
                        <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" fill="currentColor" viewBox="0 0 256 256" class="transition-transform duration-300 group-hover:rotate-180">
                            <path d="M213.66,101.66l-80,80a8,8,0,0,1-11.32,0l-80-80A8,8,0,0,1,53.66,90.34L128,164.69l74.34-74.35a8,8,0,0,1,11.32,11.32Z"></path>
                        </svg>
                    </a>
                    <div class="absolute left-1/2 -translate-x-1/2 pt-4 opacity-0 invisible group-hover:opacity-100 group-hover:visible transition-all duration-300 z-50">
                        <div class="w-48 bg-white/90 backdrop-blur-md rounded-2xl shadow-xl border border-white/20 py-2 overflow-hidden">
                            <a href="#" class="block px-4 py-2 hover:bg-teal-500 hover:text-white transition-colors">Layanan A</a>
                            <a href="#" class="block px-4 py-2 hover:bg-teal-500 hover:text-white transition-colors">Layanan B</a>
                        </div>
                    </div>
                </div>

                <!-- Dropdown Template -->
                <div class="relative group mx-10">
                    <a href="#" class="hover:text-teal-500 transition-colors flex items-center gap-1">
                        Layanan
                        <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" fill="currentColor" viewBox="0 0 256 256" class="transition-transform duration-300 group-hover:rotate-180">
                            <path d="M213.66,101.66l-80,80a8,8,0,0,1-11.32,0l-80-80A8,8,0,0,1,53.66,90.34L128,164.69l74.34-74.35a8,8,0,0,1,11.32,11.32Z"></path>
                        </svg>
                    </a>
                    <div class="absolute left-1/2 -translate-x-1/2 pt-4 opacity-0 invisible group-hover:opacity-100 group-hover:visible transition-all duration-300 z-50">
                        <div class="w-48 bg-white/90 backdrop-blur-md rounded-2xl shadow-xl border border-white/20 py-2 overflow-hidden">
                            <a href="#" class="block px-4 py-2 hover:bg-teal-500 hover:text-white transition-colors">Layanan A</a>
                            <a href="#" class="block px-4 py-2 hover:bg-teal-500 hover:text-white transition-colors">Layanan B</a>
                        </div>
                    </div>
                </div>

            </nav>

            <!-- Tombol Kontak -->
            <a href="#" class="px-10 py-3 bg-teal-500 hover:bg-teal-600 text-white text-base font-['SF_Pro_Display'] rounded-full transition-colors">
                Kontak
            </a>
        </div>
    </div>

</body>

</html>
