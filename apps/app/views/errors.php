<?php
// Default fallback

$code = $code ?? 404;
$title = $title ?? match ($code) {
    404 => 'Halaman tidak ditemukan',
    419 => 'Form Kadaluarsa',
    default => 'Error',
};

$desc = $desc ?? match ($code) {
    404 => 'Maaf, halaman yang Anda cari tidak tersedia. Silakan kembali ke beranda.',
    419 => 'Maaf, halaman sudah kadaluarsa. Silahkan refresh halaman atau kembali ke beranda',
    default => '',
};

?>



<div class="relative bg-cover bg-center bg-no-repeat " style="background-image: url('/images/background.png');">
    <div class="absolute inset-y-14 inset-x-20 bg-teal-100/60 rounded-4xl" aria-hidden="true"></div>
    <?php include __DIR__ . '/../components/cursor.php'; ?>

    <div class="relative min-h-screen flex items-center overflow-hidden">

        
        <div class="pointer-events-none inset-0 absolute text-teal-500/50" aria-hidden="true">
            <i class="ph ph-magnifying-glass-plus absolute text-5xl z-10 left-175 top-35 drop-shadow-sm"></i>
            <i class="ph ph-first-aid absolute text-7xl z-10 bottom-12 right-18 m-10 rotate-2 drop-shadow-sm"></i>
            <i class="ph ph-file-x absolute text-8xl z-10 top-38 right-50 m-10 rotate-20 drop-shadow-sm"></i>
            <i class="ph ph-ambulance absolute text-6xl z-10 bottom-14 left-20 m-10 rotate-2 drop-shadow-sm"></i>
        </div>

        <svg viewBox="0 0 200 200" class="absolute w-60 top-30 left-22 opacity-30">
            <path fill="#14B8A6" d="M24.1,-38.1C30.7,-23.1,35.4,-15.1,37,-6.5C38.6,2.2,37.2,11.4,33.6,21C30,30.7,24.2,40.8,16.6,42C8.9,43.3,-0.7,35.8,-15.7,34.2C-30.8,32.6,-51.2,37,-64.5,29.7C-77.8,22.3,-83.8,3.2,-81.8,-15.6C-79.9,-34.4,-69.9,-52.9,-55,-66.5C-40,-80,-20,-88.5,-5.6,-81.8C8.7,-75.1,17.4,-53.1,24.1,-38.1Z" transform="translate(100 100)" />
        </svg>


        <div
            class="absolute right-1/2 -translate-x-20  flex flex-col gap-6 w-full max-w-120 h-auto p-8 rounded-4xl bg-gradient-to-br from-white/80 to-white/60 backdrop-blur-[10px]"
            style="box-shadow: -5px 10px 5px 0 rgba(0,0,0,0.1);">
            <h1 class="text-5xl font-black text-left max-w-[90%]  text-teal-900"><?php echo $title; ?></h1>
            <p class="text-left text-base max-w-[90%] mb-2"><?php echo $desc; ?></p>
            <a href="/" class="w-42 h-10 bg-teal-500 flex items-center justify-center rounded-2xl text-white transition duration-150  hover:bg-teal-700 hover:scale-110  ">Kembali ke Beranda</a>
        </div>

        <svg viewBox="0 0 200 200" class="absolute w-160 left-1/2 -translate-x-10 rotate-30  opacity-20">
            <path fill="#14B8A6" d="M49.7,-43.5C59.4,-27.3,59.1,-7,54.6,12.4C50.1,31.7,41.5,50,26.6,58.3C11.8,66.6,-9.4,65,-29.2,56.8C-49,48.7,-67.5,34.2,-68.4,18.8C-69.4,3.4,-52.9,-12.8,-38.6,-30C-24.3,-47.2,-12.1,-65.3,3.9,-68.5C19.9,-71.6,39.9,-59.6,49.7,-43.5Z" transform="translate(100 100)" />
        </svg>


        <h1 class="absolute  left-1/2 translate-x-20 text-[11rem] font-black tracking-tight text-teal-700/30 rotate-8 pointer-events-none"><?php echo $code; ?></h1>
        <div class="z-1">
            <?php include __DIR__ . '/../components/subfooter.php'; ?>
        </div>



    </div>
</div>