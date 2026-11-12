<?php

$heroImage = '/images/kontak.jpg';
$heroTitle = 'Tentang Poliklinik';
$heroDesc = 'Layanan Poliklinik Politeknik Negeri Malang';
$showCta = false;
$page = 'nonlanding';
?>
<div class="relative bg-cover bg-center bg-no-repeat " style="background-image: url('/images/background.png');">
    <div class="absolute inset-y-0 inset-x-20 bg-teal-100/70" aria-hidden="true"></div>

    <?php include __DIR__.'/../components/cursor.php'; ?>
    <?php include __DIR__.'/../components/header.php'; ?>
    <?php include __DIR__.'/../components/big-hero-card-landing.php'; ?>

    <div class="relative w-full h-410 overflow-hidden">

        <i class="ph ph-first-aid absolute text-7xl z-10 text-teal-500/30 left-20 m-10 -rotate-10"></i>

        <svg viewBox="0 0 200 200" class="absolute opacity-30 w-100 h-100  right-18 -translate-y-12">
            <path fill="#14B8A6" d="M38.5,-50.8C50.6,-44.2,61.5,-33.9,65.6,-21.3C69.6,-8.8,66.7,6,60.3,17.8C54,29.6,44.1,38.4,33.5,45.1C22.9,51.7,11.4,56.2,-3.3,60.7C-18,65.2,-35.9,69.7,-50.1,64.2C-64.3,58.6,-74.7,43.2,-71.8,28.8C-68.9,14.5,-52.6,1.4,-44.1,-11.1C-35.6,-23.6,-35,-35.4,-28.9,-43.9C-22.9,-52.5,-11.4,-57.7,0.9,-59C13.2,-60.2,26.4,-57.4,38.5,-50.8Z" transform="translate(100 100)" />
        </svg>

        <div class="relative flex top-25 mb-40 items-center justify-center">
            <div class="w-full max-w-4xl    flex flex-row items-center justify-center gap-18 p-8  rounded-4xl bg-gradient-to-br from-white/80 to-white/50 backdrop-blur-md border border-white/20">
                <img src="/images/kontak.jpg" alt="" class="h-75 rounded-2xl">
                <div class="w-95 h-75 flex flex-col gap-6 ">
                    <h1 class="font-black text-3xl text-slate-900 tracking-tight">Siap Melayani Civitas Polinema</h1>
                    <p class="text-sm text-justify">Poliklinik Politeknik Negeri Malang hadir sebagai fasilitas kesehatan yang memberikan pelayanan medis dasar bagi mahasiswa, dosen, dan tenaga kependidikan.</p>
                    <p class="text-sm text-justify">Dengan dukungan tenaga kesehatan profesional, kami berkomitmen menghadirkan layanan yang cepat, ramah, dan mudah diakses melalui sistem antrean yang terintegrasi.</p>
                    <div class="flex flex-row gap-2">
                        <span class="w-28 h-8 text-sm flex justify-center items-center font-medium rounded-full border transition-colors bg-blue-100 text-blue-800 border-gray-300">
                            Poli Umum
                        </span>
                        <span class="w-28 h-8 text-sm flex justify-center items-center font-medium rounded-full border transition-colors bg-green-100 text-green-800 border-gray-300">
                            Poli Gigi
                        </span>
                        <span class="w-28 h-8 text-sm flex justify-center items-center font-medium rounded-full border transition-colors bg-purple-100 text-purple-800 border-gray-300">
                            Konseling
                        </span>
                    </div>
                </div>
            </div>
        </div>
        <i class="ph ph-bed absolute text-5xl z-10 text-teal-500/30 left-1/2 -translate-y-5 -translate-x-50 -rotate-15""></i>
        <i class="ph ph-first-aid-kit absolute text-6xl z-10 text-teal-500/30 left-1/2 -translate-y-5 translate-x-30 rotate-10"></i>

        <h2 class="relative text-center text-teal-700  text-lg font-medium">Prinsip Kami</h2>

        <div class="relative flex items-center w-[65vw] mx-auto  mb-10 z-10">
            <hr class="flex-1 border-t-3 border-teal-700 rounded">
            <span class="px-4 text-teal-700  text-5xl font-black tracking-tight">Nilai layanan</span>
            <hr class="flex-1 border-t-3 border-teal-700 rounded">
        </div>


        <div class="relative flex flex-row gap-6 items-center justify-center mb-15 z-10">
            <div class="flex flex-col gap-2 p-6 w-75 h-50 bg-white rounded-2xl border border-teal-500">
                <div class="flex w-10 h-10 rounded-full bg-teal-500 items-center justify-center text-white text-lg font-medium">01</div>
                <h6 class="text-teal-500 text-lg font-medium">Ramah & Peduli</h6>
                <p>Setiap pasien diperlakukan dengan empati dan rasa hormat.</p>
            </div>
            <div class="flex flex-col gap-2 p-6 w-75 h-50 bg-white rounded-2xl border border-teal-500">
                <div class="flex w-10 h-10 rounded-full bg-teal-500 items-center justify-center text-white text-lg font-medium">02</div>
                <h6 class="text-teal-500 text-lg font-medium">Cepat & Tepat</h6>
                <p>Antrean yang jelas dan penanganan medis yang sigap.</p>
            </div>

            <div class="flex flex-col gap-2 p-6 w-75 h-50 bg-white rounded-2xl border border-teal-500">
                <div class="flex w-10 h-10 rounded-full bg-teal-500 items-center justify-center text-white text-lg font-medium">03</div>
                <h6 class="text-teal-500 text-lg font-medium">Kompeten & Terpercaya</h6>
                <p>Tenaga kesehatan berkompeten dengan standar layanan yang jelas.</p>
            </div>
        </div>

        <svg viewBox="0 0 200 200" class="absolute opacity-30 w-130 h-130 left-30 -translate-y-80 z-0 rotate-30">
            <path fill="#14B8A6" d="M32.3,-42C42.1,-30.2,50.4,-20.3,48.9,-11.3C47.4,-2.2,35.9,5.9,29,15.6C22,25.2,19.7,36.3,11.4,45.5C3.2,54.6,-10.9,61.7,-25.2,60.3C-39.4,59,-53.8,49.2,-61.1,35.7C-68.3,22.2,-68.5,5,-61.5,-6.8C-54.6,-18.5,-40.5,-25,-29,-36.5C-17.6,-48,-8.8,-64.7,1.2,-66.1C11.2,-67.6,22.4,-53.8,32.3,-42Z" transform="translate(100 100)" />
        </svg>

        <i class="ph ph-heartbeat absolute text-7xl z-10 text-teal-500/30 right-65 -rotate-20"></i>
         <i class="ph ph-stethoscope absolute text-8xl z-10 text-teal-500/30 right-40 rotate-30"></i>

        <div class="relative ml-45 mb-10">
            <h2 class="  text-teal-700  text-lg font-medium">Arah Kami</h2>
            <span class=" text-teal-700  text-5xl font-black tracking-tight">Visi & Misi</span>

        </div>
       

        <div class="relative flex flex-row gap-30  justify-center">
            <div class="flex flex-col gap-6">
                <div class="flex flex-col gap-2 p-6 w-120 h-55 bg-white rounded-2xl border border-teal-500">
                    <div class="flex w-12 h-12 rounded-2xl bg-teal-500 items-center justify-center text-white text-lg font-medium">
                        <i class="ph ph-shield-check text-white text-3xl"></i>
                    </div>
                    <h6 class="text-teal-500 text-2xl font-medium tracking-tight">Visi</h6>
                    <p>Menjadi poliklinik kampus yang unggul, terpercaya, dan berorientasi pada kesehatan serta kesejahteraan seluruh civitas akademika.</p>
                </div>

                <div class="flex flex-col gap-2 p-6 w-120 h-75 bg-teal-500 rounded-2xl">
                    <div class="flex w-12 h-12 rounded-2xl bg-white items-center justify-center text-white text-lg font-medium">
                        <i class="ph ph-target text-teal-500 text-3xl"></i>
                    </div>
                    <h6 class="text-white text-2xl font-medium tracking-tight">Misi</h6>
                    <ol class="list-decimal pl-5 text-white">
                        <li>Memberikan pelayanan kesehatan yang cepat dan berkualitas.</li>
                        <li>Mengembangkan sistem layanan digital yang mudah diakses.</li>
                        <li>Meningkatkan kesadaran hidup sehat di lingkungan kampus.</li>

                    </ol>
                </div>

            </div>
            <img src="/images/shade.webp" alt="" class="w-90 h-auto -translate-x-5 z-1">



        </div>

          

        <svg viewBox="0 0 200 200" class="absolute opacity-30 w-140 h-140 right-15 bottom-0  rotate-30">
            <path fill="#14B8A6" d="M23.4,-43C28.4,-33.2,29.3,-23.5,36.1,-14.7C42.9,-5.9,55.6,2,60.7,13.1C65.9,24.2,63.4,38.5,56,50.5C48.6,62.5,36.3,72.1,21.6,78.7C7,85.3,-10.1,88.8,-14.5,75.5C-18.8,62.2,-10.6,32.1,-7.2,16.9C-3.8,1.7,-5.3,1.3,-16.7,-4.4C-28.1,-10.2,-49.5,-21.3,-55.5,-32.5C-61.5,-43.8,-52.1,-55.1,-40.3,-61.8C-28.5,-68.5,-14.2,-70.5,-2.5,-66.6C9.2,-62.7,18.4,-52.8,23.4,-43Z" transform="translate(100 100)" />
        </svg>


    </div>




</div>
<?php include __DIR__.'/../components/footer.php'; ?>