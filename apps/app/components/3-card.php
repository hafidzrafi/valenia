<?php

$services = [];
$attendance = $attendance ?? [];

foreach ($attendance as $medis) {
    $serviceName = (string) $medis['service_name'];
    $present = (bool) $medis['is_present'];
    $start = null !== ($medis['start_time'] ?? null) ? substr((string) $medis['start_time'], 0, 5) : '';
    $end = null !== ($medis['end_time'] ?? null) ? substr((string) $medis['end_time'], 0, 5) : '';
    $hours = ('' !== $start && '' !== $end) ? $start . ' - ' . $end : '-';

    // create the group the first time for each service
    if (!isset($services[$serviceName])) {
        $services[$serviceName] = [
            'service_name' => $serviceName,
            'is_available' => false,
            'hours' => $hours,
            'doctor_count' => 0,
        ];
    }

    if ($present) {
        $services[$serviceName]['is_available'] = true;
        ++$services[$serviceName]['doctor_count'];
        $services[$serviceName]['hours'] = $hours;
    }
}
?>




<div class="flex justify-center items-center py-8 relative z-10">
    <div class="flex flex-wrap justify-center gap-8">

        <?php foreach ($services as $service) { ?>
            <?php
            $serviceName = $service['service_name'];
            $isavailable = $service['is_available'];
            $jam = $service['hours'];
            $status = 'negative';
            $labelfortag = 'Tutup';

            if ($isavailable && '-' !== $jam) {
                $status = 'positive';
                $labelfortag = 'Buka';
            }

            $serviceimg = [
                'Poli Umum' => '/images/poliumum.webp',
                'Poli Gigi' => '/images/poligigi.webp',
                'Konseling/Psikologi' => '/images/konseling.webp',
            ];
            $imageform = $serviceimg[$serviceName];
            $formvalue = [
                'Poli Umum' => 'POLI UMUM',
                'Poli Gigi' => 'POLI GIGI',
                'Konseling/Psikologi' => 'KONSELING',
            ];
            $valueform = $formvalue[$serviceName];

            $formname = [
                'Poli Umum' => 'Poli Umum',
                'Poli Gigi' => 'Poli Gigi',
                'Konseling/Psikologi' => 'Konseling',
            ];
            $nameform = $formname[$serviceName];
            ?>

            <form action="/form" method="post">
                <input type="hidden" name="heroform" value="<?php echo $valueform; ?>">
                <?php echo csrf_field(); ?>
                <button type="submit" class="w-full text-left">
                    <div class="flex flex-col w-80 h-110 bg-[linear-gradient(180deg,rgba(102,102,102,0)_50.48%,rgba(0,0,0,0.7)_78.85%),url(<?php echo $imageform; ?>)] bg-cover bg-center p-4 transition-all transform hover:scale-110 hover:outline hover:outline-4 hover:outline-teal-500 hover:shadow-2xl hover:shadow-teal-700/75 shadow-md shadow-gray-500 active:scale-100">
                        <div class="mt-auto">
                            <h3 class="text-4xl font-bold m-2 text-white"><?php echo $nameform; ?></h3>
                            <div class="scale-80 left-0 -translate-x-4">
                                <?php
                                $status_tag = $status;
                                $label_tag = $labelfortag;

                                include __DIR__ . '/../components/tag.php'; ?>
                            </div>
                        </div>
                    </div>
                </button>
            </form>



        <?php } ?>
    </div>
</div>

<!-- backup code -->
<?php /*
 <form action="/form" method="post">
            <input type="hidden" name="heroform" value="POLI UMUM">
            <?php echo csrf_field(); ?>
            <button type="submit" class="w-full text-left">
                <div class="flex flex-col w-80 h-110 bg-[linear-gradient(180deg,rgba(102,102,102,0)_50.48%,rgba(0,0,0,0.7)_78.85%),url(/images/poliumum.webp)] bg-cover bg-center p-4 transition-all transform hover:scale-110 hover:outline hover:outline-4 hover:outline-teal-500 hover:shadow-2xl hover:shadow-teal-700/75 shadow-md shadow-gray-500 active:scale-100">
                    <div class="mt-auto">
                        <h3 class="text-4xl font-bold m-2 text-white">Poli Umum</h3>
                        <div class="scale-80 left-0 -translate-x-4">
                            <?php
                            $status_tag = 'positive';
                            $label_tag = 'Buka';

                            include __DIR__ . '/../components/tag.php'; ?>
                        </div>
                    </div>
                </div>
            </button>
        </form>

        <form action="/form" method="post">
            <input type="hidden" name="heroform" value="POLI GIGI">
            <?php echo csrf_field(); ?>
            <button type="submit" class="w-full text-left">
                <div class="flex flex-col w-80 h-110 bg-[linear-gradient(180deg,rgba(102,102,102,0)_50.48%,rgba(0,0,0,0.7)_78.85%),url(/images/poligigi.webp)] bg-cover bg-center p-4 transition-all transform hover:scale-110 hover:outline hover:outline-4 hover:outline-teal-500 hover:shadow-2xl hover:shadow-teal-700/75 shadow-md shadow-gray-500 active:scale-100">
                    <div class="mt-auto">
                        <h3 class="text-4xl font-bold m-2 text-white">Poli Gigi</h3>
                        <div class="scale-80 left-0 -translate-x-4">
                            <?php
                            $status_tag = 'negative';
                            $label_tag = 'Tutup';

                            include __DIR__ . '/../components/tag.php'; ?>
                        </div>
                    </div>
                </div>
            </button>
        </form>

        <form action="/form" method="post">
            <input type="hidden" name="heroform" value="KONSELING">
            <?php echo csrf_field(); ?>
            <button type="submit" class="w-full text-left">
                <div class="flex flex-col w-80 h-110 bg-[linear-gradient(180deg,rgba(102,102,102,0)_50.48%,rgba(0,0,0,0.7)_78.85%),url(/images/konseling.webp)] bg-cover bg-center p-4 transition-all transform hover:scale-110 hover:outline hover:outline-4 hover:outline-teal-500 hover:shadow-2xl hover:shadow-teal-700/75 shadow-md shadow-gray-500 active:scale-100">
                    <div class="mt-auto">
                        <h3 class="text-4xl  font-bold m-2 font-display-expanded text-white">Konseling</h3>
                        <div class="scale-80 left-0 -translate-x-4">
                            <?php
                            $status_tag = 'positive';
                            $label_tag = 'Buka';

                            include __DIR__ . '/../components/tag.php'; ?>
                        </div>
                    </div>
                </div>
            </button>
        </form>
*/
?>