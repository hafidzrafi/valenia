<?php

/**
 * Home page (VALENIA-29) — standalone full page served at "/".
 *
 * IMPORTANT: this is a complete <html> document, not a content partial. Render
 * it via render(app_path('app/views/home.php'), [...]) from the route. Do NOT use
 * view('home'), which would wrap it in layout.php and emit two <html> documents.
 */
$ticket = $ticket ?? [];
$heroform = $ticket['service']['name'] ?? 'Default';
$isform = true;
$no_antrean = isset($ticket['queue_code']) ? (string) $ticket['queue_code'] : '—';
$patientName = (string) ($ticket['patient']['full_name'] ?? '-');
$ticketTime = isset($ticket['created_at']) ? date('H:i', strtotime((string) $ticket['created_at'])) : '-';
$ticketDate = isset($ticket['created_at']) ? date('d M Y', strtotime((string) $ticket['created_at'])) : '-';
$ticketToken = (string) ($ticket['ticket_token'] ?? '');
$qrSvg = ticket_qr_svg($ticketToken, 180);

include __DIR__.'/../components/background-img.php';
?>
<?php include __DIR__.'/../components/cursor.php'; ?>

<body>



    <div class="min-h-screen flex flex-col">
        <div class="grow flex items-center justify-center">

            <div class="absolute">
                <div
                    class="w-115 h-130 rounded-[64px] bg-gradient-to-br from-white/80 to-white/60 backdrop-blur-[10px]"
                    style="box-shadow: 10px 15px 10px 0 rgba(0,0,0,0.1);">
                    <h1 class="text-[32px] pt-5 flex justify-center -mb-2.5 font-bold text-slate-900">Pendaftaran Berhasil!</h1>

                    <div class="flex flex-row justify-center items-center mt-10 gap-8">
                        <div class="flex h-[180px] w-[180px] items-center justify-center rounded-2xl bg-white p-1">
                            <?php if (null !== $qrSvg) { ?>
                                <?php echo $qrSvg; ?>
                            <?php } else { ?>
                                <span class="break-all text-center text-[10px] text-slate-600"><?php echo e($ticketToken); ?></span>
                            <?php } ?>
                        </div>

                        <div class="flex justify-end  gap-4">
                            <div class="flex flex-col -translate-y-2 gap-1">
                                <p class="text-[64px] font-black font-display-expanded translate-x-2 text-center text-slate-900"><?php echo e($no_antrean); ?></p>
                                <p class="text-sm font-medium text-black text-right"><?php echo e($patientName); ?></p>
                                <p class="text-sm font-medium text-black text-right"><?php echo e($heroform); ?></p>
                                <p class="text-sm font-medium text-black text-right"><?php echo e($ticketDate); ?></p>
                                <p class="text-sm font-medium text-black text-right"><?php echo e($ticketTime); ?> WIB</p>
                            </div>
                        </div>
                    </div>

                    <p class="flex justify-center mt-8 text-[14px]">Tunjukkan QR Code saat periksa di poliklinik</p>

                    <div class="flex flex-col gap-6 items-center mt-4">
                        <div class="flex flex-row gap-6 justify-center items-center scale-90">
                            <a href="/portal-pasien" class="w-[188px] h-[59px] smooth-corners-md rounded-3xl border-2 text-teal-500 border-teal-500 bg-white flex flex-row gap-2 items-center justify-center hover:bg-teal-500 hover:text-white">
                                <p class="text-base font-semibold">Cek Riwayat</p>
                            </a>
                            <a href="/muncul-qr?download=1" class="w-[188px] h-[59px] smooth-corners-md rounded-3xl bg-teal-500 flex flex-row gap-2 items-center justify-center hover:bg-teal-700">
                                <p class="text-base font-semibold text-white">Unduh</p>
                                <i class="ph ph-download-simple text-white text-[18px]"></i>
                            </a>
                        </div>
                    </div>
                    <a href="/" class="flex justify-center mt-5 text-teal-500 text-base font-medium hover:text-teal-800 hover:underline">Kembali ke Beranda</a>
                </div>
            </div>

        </div>

        <div>
            <?php include __DIR__.'/../components/subfooter.php'; ?>
        </div>
    </div>

</body>


