<?php

/**
 * Home page (VALENIA-29) — standalone full page served at "/".
 *
 * IMPORTANT: this is a complete <html> document, not a content partial. Render
 * it via render(app_path('app/views/home.php'), [...]) from the route. Do NOT use
 * view('home'), which would wrap it in layout.php and emit two <html> documents.
 */
$patient = $patient ?? [];
$active = $active ?? null;
$selected = $selected ?? $active;
$history = $history ?? [];
$flash = $flash ?? null;
$isform = true;

$patientName = (string) ($patient['full_name'] ?? '-');
$identityNumber = (string) ($patient['identity_number'] ?? '-');
$identityType = (string) ($patient['identity_type'] ?? '-');
$activeQueue = isset($active['queue_code']) ? (string) $active['queue_code'] : '—';
$activeService = (string) ($active['service_name'] ?? '-');
$activeDate = isset($active['created_at']) ? date('d M Y', strtotime((string) $active['created_at'])) : '-';
$activeTime = isset($active['created_at']) ? date('H:i', strtotime((string) $active['created_at'])).' WIB' : '-';
$activeStatus = (string) ($active['status'] ?? '');
$activeTicketToken = (string) ($active['ticket_token'] ?? '');
$activeQrSvg = '' !== $activeTicketToken ? ticket_qr_svg($activeTicketToken, 128) : null;

$selectedQueue = isset($selected['queue_code']) ? (string) $selected['queue_code'] : '—';
$selectedService = (string) ($selected['service_name'] ?? '-');
$selectedDate = isset($selected['created_at']) ? date('d M Y', strtotime((string) $selected['created_at'])) : '-';
$selectedTime = isset($selected['created_at']) ? date('H:i', strtotime((string) $selected['created_at'])).' WIB' : '-';
$selectedStatus = (string) ($selected['status'] ?? '');
$selectedIsActive = (bool) ($selected['is_active'] ?? false);
$selectedTicketToken = (string) ($selected['ticket_token'] ?? '');
$selectedQrSvg = $selectedIsActive && '' !== $selectedTicketToken
    ? ticket_qr_svg($selectedTicketToken, 128)
    : null;

$statusMap = [
    'menunggu' => ['positive', 'Menunggu'],
    'dilayani' => ['negative', 'Dilayani'],
    'selesai' => ['positive', 'Selesai'],
    'batal' => ['disabled', 'Dibatalkan'],
];

$progressSteps = match ($activeStatus) {
    'menunggu' => ['done', 'current', 'wait', 'wait'],
    'dilayani' => ['done', 'done', 'current', 'wait'],
    'selesai' => ['done', 'done', 'done', 'done'],
    'batal' => ['done', 'done', 'wait', 'wait'],
    default => ['current', 'wait', 'wait', 'wait'],
};

include __DIR__.'/../components/background-img.php';

$heroImage = '/images/portalpasien.webp';
$heroTitle = 'Portal Pasien';
$heroDesc = 'Pantau status antrean dan riwayat kunjungan Poliklinik VALENIA';
$showCta = false;
?>
<?php include __DIR__.'/../components/cursor.php'; ?>
<?php include __DIR__.'/../components/header.php'; ?>
<div class="overflow-hidden">
    <?php include __DIR__.'/../components/big-hero-card-landing.php'; ?>
</div>


<body>
    <?php if (null !== $flash) { ?>
        <p class="mx-auto mt-4 w-fit rounded-2xl bg-emerald-50 px-4 py-2 text-sm text-emerald-700"><?php echo e($flash); ?></p>
    <?php } ?>
    <div class="w-full h-130 flex items-center justify-center flex-row gap-8">
        <div
            class="w-90 h-100 smooth-corners-md rounded-4xl bg-gradient-to-br from-white/80 to-white/60 backdrop-blur-[10px] border-2 border-gray-400"
            style="box-shadow: 10px 15px 10px 0 rgba(0,0,0,0.1);">
            <h1 class="text-[32px] pt-5 flex justify-center -mb-2.5 font-bold text-slate-900">Antrean Aktif</h1>
            <div class="flex flex-row justify-center items-center mt-10 gap-8">
                <div class="flex h-34 w-34 items-center justify-center rounded-2xl bg-white p-1">
                    <?php if (null !== $activeQrSvg) { ?>
                        <?php echo $activeQrSvg; ?>
                    <?php } else { ?>
                        <i class="ph ph-ticket text-[64px] text-teal-500"></i>
                    <?php } ?>
                </div>

                <div class="flex justify-end  gap-4">
                    <div class="flex flex-col -translate-y-2 gap-1">
                        <p class="text-[32px] font-black text-right text-slate-900"><?php echo e($activeQueue); ?></p>
                        <p class="text-sm  text-black text-right"><?php echo e($patientName); ?></p>
                        <p class="text-sm  text-black text-right"><?php echo e($activeService); ?></p>
                        <p class="text-sm  text-black text-right"><?php echo e($activeDate); ?></p>
                        <p class="text-sm  text-black text-right"><?php echo e($activeTime); ?></p>
                    </div>
                </div>
            </div>
            <div class="flex flex-col justify-center items-center">
                <p class="flex justify-center mt-8 text-[14px]"><?php echo null === $active ? 'Tidak ada antrean aktif hari ini.' : 'Tunjukkan QR Code saat periksa di poliklinik'; ?></p>
                <?php if (null !== $active) { ?>
                    <a href="/muncul-qr?download=1" class="w-[148px] h-[48px] mt-6 smooth-corners-md rounded-3xl bg-teal-500 flex flex-row gap-2 items-center justify-center hover:bg-teal-700">
                        <p class="text-base font-medium text-white">Unduh</p>
                        <i class="ph ph-download-simple text-white text-[18px]"></i>
                    </a>
                <?php } ?>
            </div>
        </div>


        <div
            class="w-130 h-100 rounded-4xl smooth-corners-md bg-gradient-to-br from-white/80 to-white/60 backdrop-blur-[10px] flex flex-col justify-start border-2 border-gray-400 px-12 pt-10"
            style="box-shadow: 10px 15px 10px 0 rgba(0,0,0,0.1);">
            <div class="flex w-full items-start justify-between gap-6">
                <div class="min-w-0 pt-1">
                    <h1 class="truncate text-[24px] font-medium leading-tight text-slate-900"
                        title="<?php echo e($patientName); ?>"><?php echo e($patientName); ?></h1>

                    <div class="mt-5 border-t border-gray-400 pt-3">
                        <p class="text-sm text-black"><?php echo e($identityType); ?></p>
                        <p class="text-sm text-black"><?php echo e($identityNumber); ?></p>
                        <div class="mt-3"><?php
                        $status_tag = 'positive';
$label_tag = 'Aktif';

include __DIR__.'/../components/tag.php'; ?>
                        </div>
                    </div>
                </div>

                <div class="h-[120px] w-[120px] shrink-0 rounded-full border border-gray-400"></div>
            </div>

            <div class="mt-7 w-full border-t border-gray-400 pt-3">
                <p class="text-black font-medium">Progres Antrean Aktif</p>
            </div>

            <div class=" flex flex-row gap-10 items-center justify-center h-15 w-100 mt-6">
                <?php foreach ([1, 2, 3, 4] as $index) { ?>
                    <?php if ($index > 1) { ?>
                        <span class=" w-[42px] h-[2px] bg-gray-500"></span>
                    <?php } ?>

                    <div class="flex justify-center items-center">
                        <?php
                        $status_step = $progressSteps[$index - 1];
                    $step_text = (string) $index;

                    include __DIR__.'/../components/step.php'; ?>
                    </div>
                <?php } ?>

            </div>
            <div class=" flex flex-row gap-10 items-center justify-center h-4 w-112">
                <p class=" flex-grow  text-xs font-medium text-center  text-black">
                    Terdaftar
                </p>
                <p class=" flex-grow  text-xs font-medium text-center -translate-x-2  text-black">
                    Menunggu
                </p>
                <p class=" flex-grow  text-xs font-medium text-center -translate-x-2.5  text-black">
                    Dilayani
                </p>
                <p class=" flex-grow  text-xs font-medium text-center -translate-x-1  text-black">
                    Selesai
                </p>
            </div>

        </div>

    </div>

    <div class="flex flex-col relative w-full h-260 mt-5 mx-auto  smooth-corners-lg rounded-tl-[90px] rounded-tr-[90px] bg-slate-900/70 items-center pt-15">
        <h1 class="text-4xl  flex justify-center font-bold text-white mt-5">Riwayat Kunjungan</h1>

        <p class="text-7xl font-black mt-30 text-white"><?php echo e($activeQueue); ?></p>
        <p class="  text-white mt-6 ">Nomor yang dilayani saat ini</p>


        <div class="grid grid-cols-1 gap-6 md:grid-cols-3 max-w-5xl mx-auto mt-40">
            <div class="flex flex-col gap-3 space-y-2 col-span-2">
                <div class="flex flex-col gap-3 items-center justify-start rounded-3xl bg-gradient-to-br from-white/80 to-white/60 backdrop-blur-[20px] border border-gray-300/50 p-6 shadow-sm min-h-[324px]">

                    <?php if ([] === $history) { ?>
                        <p class="text-sm text-gray-600">Belum ada riwayat kunjungan.</p>
                    <?php } ?>

                    <?php foreach ($history as $visit) { ?>
                        <?php
                    [$status_tag, $label_tag] = $statusMap[$visit['status']] ?? ['disabled', ucfirst((string) $visit['status'])];
                        $isSelected = is_array($selected) && (string) $selected['id'] === (string) $visit['id'];
                        ?>
                        <a href="/portal-pasien?visit=<?php echo e((string) $visit['id']); ?>"
                           class="grid grid-cols-4 items-center px-8 h-15 w-130 rounded-3xl no-underline transition-colors border bg-white shadow-[0_2px_8px_rgba(30,60,55,0.08)] <?php echo $isSelected ? 'border-teal-500 ring-1 ring-teal-500' : 'border-gray-200 hover:border-teal-400'; ?>">
                            <div class="flex min-w-0 flex-col gap-0.5 leading-tight">
                                <p class="truncate text-lg font-medium text-black"><?php echo e($visit['service_name']); ?></p>
                                <p class="text-xs font-medium text-slate-500"><?php echo e($visit['queue_code'] ?? ''); ?></p>
                            </div>
                            <p class="text-sm font-medium text-black text-center"><?php echo e(date('d M Y', strtotime((string) $visit['created_at']))); ?></p>
                            <p class="text-sm font-medium text-black text-center"><?php echo e(date('H:i', strtotime((string) $visit['created_at']))); ?> WIB</p>
                            <div class="flex justify-end scale-90">
                                <?php include __DIR__.'/../components/tag.php'; ?>
                            </div>
                        </a>
                    <?php } ?>

                </div>

                <div class="rounded-3xl bg-gradient-to-br from-white/80 to-white/60 backdrop-blur-[20px] border border-gray-300/50 p-4 shadow-sm flex flex-row items-center justify-center space-x-2">

                    <input type="text" id="filter" name="filter" placeholder="Cari..."
                        class="w-50 text-xs  text-left bg-transparent p-2 smooth-corners-lg rounded-2xl mr-8  border-[1.5px] border-gray-500 focus:outline-none focus:border-teal-500 focus:bg-teal-100/50 hover:bg-teal-100/50 ">

                    <div
                        x-data="{ 
                            currentPage: 1, 
                            totalPages: 4,
                            goToPage(page) {
                                if(page < 1 || page > this.totalPages) return;
                                this.currentPage = page;
                                // fetch data
                                console.log('Fetching data for page:', this.currentPage);
                            }
                        }"
                        class="flex items-center space-x-2 text-sm font-medium">
                        <!-- Previous button -->
                        <button
                            @click="goToPage(currentPage - 1)"
                            :disabled="currentPage === 1"
                            class="w-9 h-9 flex items-center justify-center rounded-[8px] border border-gray-500 hover:bg-teal-100 active:bg-teal-400/50 active:border-teal-500 transition-colors disabled:opacity-40 disabled:pointer-events-none">
                            <i class="ph ph-caret-left text-[18px]"></i>
                        </button>

                        <!-- number -->
                        <template x-for="i in totalPages" :key="i">
                            <button
                                @click="goToPage(i)"
                                :class="currentPage === i ? 'bg-teal-400/50 text-white border-teal-500' : 'border-gray-500 hover:bg-teal-100'"
                                class="w-9 h-9 flex items-center justify-center rounded-[8px] border transition-colors"
                                x-text="i">
                            </button>
                        </template>

                        <!-- Next button -->
                        <button
                            @click="goToPage(currentPage + 1)"
                            :disabled="currentPage === totalPages"
                            class="w-9 h-9 flex items-center justify-center rounded-[8px] border border-gray-500 hover:bg-teal-100 active:bg-teal-400/50 active:border-teal-500 transition-colors disabled:opacity-40 disabled:pointer-events-none">
                            <i class="ph ph-caret-right text-[18px]"></i>
                        </button>
                    </div>

                </div>
            </div>





            <div class="flex flex-col items-center rounded-3xl bg-gradient-to-br from-white/80 to-white/60 backdrop-blur-[20px] border border-gray-300/50 p-4 shadow-md min-h-[400px] min-w-70">
                <?php [$selectedTag, $selectedLabel] = $statusMap[$selectedStatus] ?? ['disabled', 'Tidak aktif']; ?>
                <h1 data-testid="selected-visit-service" class="text-2xl flex justify-center font-bold text-slate-900">
                    <?php echo e(null === $selected ? 'Pilih riwayat kunjungan' : $selectedService); ?>
                </h1>

                <?php if (null !== $selected) { ?>
                    <p data-testid="selected-visit-code" class="mt-1 text-sm font-medium text-slate-500"><?php echo e($selectedQueue); ?></p>
                <?php } ?>

                <div class="flex flex-col items-center">
                    <div class="flex h-40 w-40 items-center justify-center rounded-2xl bg-white p-1">
                        <?php if (null !== $selectedQrSvg) { ?>
                            <?php echo $selectedQrSvg; ?>
                        <?php } else { ?>
                            <i class="ph ph-clock text-[72px] text-teal-500"></i>
                        <?php } ?>
                    </div>

                    <?php if (null !== $selected) { ?>
                        <div data-testid="selected-visit-status" class="mt-4">
                            <?php $status_tag = $selectedTag;
                        $label_tag = $selectedLabel;

                        include __DIR__.'/../components/tag.php'; ?>
                        </div>
                    <?php } ?>

                    <div class="mt-4 w-full border-t border-gray-400"></div>
                </div>
                <?php if (null !== $selected) { ?>
                    <p class="text-sm mt-4 text-black text-center"><?php echo e($selectedDate); ?></p>
                    <p class="text-sm text-black text-center"><?php echo e($selectedTime); ?></p>
                <?php } else { ?>
                    <p class="text-sm mt-4 text-black text-center">Klik salah satu riwayat untuk melihat detail.</p>
                <?php } ?>

                <div class="flex flex-row items-center justify-center gap-4">

                    <?php if (null !== $selected && 'menunggu' === $selectedStatus && $selectedIsActive) { ?>
                        <form method="POST" action="/portal-batal"
                              onsubmit="return confirm('Batalkan antrean ini?')">
                            <?php echo csrf_field(); ?>
                            <input type="hidden" name="visit_id" value="<?php echo e((string) $selected['id']); ?>">
                            <button type="submit" class="w-[105px] h-[40px] mt-6 smooth-corners-md rounded-3xl border-2 border-amber-500 bg-white/80 text-amber-500 flex items-center justify-center hover:bg-amber-500 hover:text-white">
                                <p class="font-medium ">Batal</p>
                            </button>
                        </form>
                    <?php } ?>

                    <?php if (null !== $selectedQrSvg) { ?>
                        <a href="/muncul-qr?download=1" class="w-[105px] h-[40px] mt-6 smooth-corners-md rounded-3xl bg-teal-500 flex flex-row gap-2 items-center justify-center hover:bg-teal-700">
                            <p class="font-medium text-white">Unduh</p>
                            <i class="ph ph-download-simple text-white text-[18px]"></i>
                        </a>
                    <?php } ?>

                </div>

                <form method="POST" action="/portal-keluar" class="mt-6">
                    <?php echo csrf_field(); ?>
                    <button type="submit" class="text-sm font-medium text-gray-500 hover:text-teal-600 hover:underline">Keluar portal</button>
                </form>



            </div>
        </div>

    </div>


</body>

<?php include __DIR__.'/../components/footer.php'; ?>