<?php

$heroImage = '/images/jadwal.webp';
$heroTitle = 'Jadwal Dokter';
$heroDesc = 'Informasi jam praktik dan ketersediaan dokter Poliklinik';
$showCta = false;
$attendance = $attendance ?? [];
?>
<div class="relative bg-cover bg-center bg-no-repeat" style="background-image: url('/images/background.png');">
    <div class="absolute inset-0 bg-white/40" aria-hidden="true"></div>

    <?php include __DIR__.'/../components/cursor.php'; ?>
    <?php include __DIR__.'/../components/header.php'; ?>
    <?php include __DIR__.'/../components/big-hero-card-landing.php'; ?>

    <div id="papan-featured" class="flex justify-center scale-90 mt-12">
        <?php include __DIR__.'/../views/partials/papan-antrean.php'; ?>
    </div>

    <div class="flex justify-center w-full mt-6">
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-8 w-full max-w-5xl">

            <div class="flex items-center p-6 gap-6 rounded-4xl bg-gradient-to-br from-white/80 to-white/50 backdrop-blur-md border border-white/20 transition-all duration-200 hover:scale-105">
                <div class="flex-shrink-0 flex items-center justify-center w-20 h-20 rounded-2xl">
                    <i class="ph ph-stethoscope text-[48px]"></i>
                </div>
                <div class="flex flex-col">
                    <h3 class="text-2xl font-bold text-gray-700">Poli Umum</h3>
                    <div class="mt-2">
                        <p class="text-sm font-semibold text-gray-500 uppercase ">Jadwal Operasional</p>
                        <p class="text-lg font-medium ">Senin - Jum'at</p>
                        <p class="text-base font-medium">08:00 - 15:00</p>
                    </div>
                </div>
            </div>


            <div class="flex items-center p-6 gap-6 rounded-4xl bg-gradient-to-br from-white/80 to-white/50 backdrop-blur-md border border-white/20 transition-all duration-200 hover:scale-105">
                <div class="flex-shrink-0 flex items-center justify-center w-20 h-20 rounded-2xl">
                    <i class="ph ph-tooth text-[48px]"></i>
                </div>
                <div class="flex flex-col">
                    <h3 class="text-2xl font-bold text-gray-700">Poli Gigi</h3>
                    <div class="mt-2">
                        <p class="text-sm font-semibold text-gray-500 uppercase">Jadwal Operasional</p>
                        <p class="text-lg font-medium ">Senin - Jum’at</p>
                        <p class="text-base font-medium">08:00 - 15:00</p>
                    </div>
                </div>
            </div>


            <div class="flex items-center p-6 gap-6 rounded-4xl bg-gradient-to-br from-white/80 to-white/50 backdrop-blur-md border border-white/20 transition-all duration-200 hover:scale-105">
                <div class="flex-shrink-0 flex items-center justify-center w-20 h-20 rounded-2x1">
                    <i class="ph ph-brain text-[48px]"></i>
                </div>
                <div class="flex flex-col">
                    <h3 class="text-2xl font-bold text-gray-700">Konseling</h3>
                    <div class="mt-2">
                        <p class="text-sm font-semibold text-gray-500 uppercase">Jadwal Operasional</p>
                        <p class="text-lg font-medium">Selasa & Jum’at</p>
                        <p class="text-base font-medium">09:00 - 13:00</p>
                    </div>
                </div>
            </div>
        </div>
    </div>


    <div class="relative flex items-center w-[90vw] mx-auto mt-20 mb-10 z-10">
        <hr class="flex-1 border-t-3 border-teal-700 rounded">
        <span class="px-4 text-teal-700  text-3xl font-black uppercase tracking-wide">Dokter Kami</span>
        <hr class="flex-1 border-t-3 border-teal-700 rounded">
    </div>

    <div class="flex justify-center mt-8 pb-12">
        <section class="smooth-corners-lg relative h-auto w-[901px] max-w-full py-5 sm:h-[298px] sm:py-0 font-sans mb-10">
            <div class="flex flex-row gap-10">
                <?php if ([] === $attendance) { ?>
                    <p class="text-center text-[12px] text-slate-600">Dokter kosong.</p>
                <?php } ?>

                <?php foreach ($attendance as $medis) { ?>
                    <?php
                    $present = (bool) $medis['is_present'];
                    $start = null !== ($medis['start_time'] ?? null) ? substr((string) $medis['start_time'], 0, 5) : '';
                    $end = null !== ($medis['end_time'] ?? null) ? substr((string) $medis['end_time'], 0, 5) : '';
                    $hours = ('' !== $start && '' !== $end) ? $start.' - '.$end : '-';
                    ?>
                    <div class="flex flex-col items-center overflow-hidden  gap-4 rounded-4xl bg-gradient-to-br from-white/80 to-white/50 backdrop-blur-md border border-white/20 transition-all duration-200 hover:scale-105  hover:border-teal-500">

                        <img src="/images/placeholder.jpg" class="w-80 aspect-[4.2/4.1] object-cover border-b border-white/20">

                        <?php
                        $serviceColors = [
                            'Poli Umum' => 'bg-blue-100 text-blue-800 border-gray-300',
                            'Poli Gigi' => 'bg-green-100 text-green-800 border-gray-300',
                            'Konseling/Psikologi' => 'bg-purple-100 text-purple-800 border-gray-300',
                        ];
                    $serviceName = (string) $medis['service_name'];
                    $serviceClass = $serviceColors[$serviceName] ?? 'bg-gray-100 text-gray-800 border-gray-300';
                    ?>

                        <span class="w-40 h-8 truncate text-sm flex justify-center items-center font-semibold rounded-full border transition-colors <?php echo e($serviceClass); ?>">
                            <?php echo e($serviceName); ?>
                        </span>

                        <span class="min-w-0  truncate text-base font-medium text-black">
                            <?php echo e((string) $medis['full_name']); ?>
                        </span>

                        <span class="min-w-0 pb-4 truncate text-sm font-light text-black">
                            <?php echo e($hours); ?>
                        </span>




                        <div
                            class="absolute h-[29px] w-[82px] translate-x-18 translate-y-5 rounded-full text-[10px] flex justify-center items-center font-semibold transition-colors <?php echo $present ? 'bg-[#10b981] text-white' : 'bg-[#d9d9d9] text-[#666]'; ?>">
                            <?php echo $present ? 'Hadir' : 'Kosong'; ?>
                        </div>


                    </div>
                <?php } ?>
            </div>
        </section>


    </div>


    <div class="w-full h-20"></div>

</div>

<?php include __DIR__.'/../components/footer.php'; ?>