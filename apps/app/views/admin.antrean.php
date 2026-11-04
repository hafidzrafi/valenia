<?php

/** @var list<array<string, mixed>> $boards */
/** @var null|string $flash */
$active = 'antrian';
$token = $token ?? '';
$role = $role ?? 'admin';
$headerTitle = 'Antrean Poliklinik';
$adminNama = 'Petugas ' . ucfirst($role);
$adminUnit = 'Poliklinik Polinema';
?>
<div class="flex min-h-screen flex-col lg:flex-row">
    <?php include __DIR__ . '/../components/admin-sidebar.php'; ?>

    <div class="flex min-h-screen min-w-0 flex-1 flex-col"
        style="background: radial-gradient(circle at 85% 15%, rgba(160,215,205,0.7) 0%, transparent 50%), linear-gradient(135deg, #a9dcb0 0%, #88cebb 50%, #3fa896 100%);">

        <div class="mx-auto flex w-full max-w-[1080px] flex-1 flex-col px-8 pb-10">
            <?php include __DIR__ . '/../components/admin-header.php'; ?>

            <main class="mt-[21px]">
                <?php if (null !== ($flash ?? null) && '' !== $flash) { ?>
                    <p class="mb-4 rounded-2xl bg-emerald-50 px-4 py-2 text-sm text-emerald-700">
                        <?php echo e($flash); ?>
                    </p>
                <?php } ?>

                <div class="flex flex-row items-center justify-between">
                    <p class="text-sm text-white/80">
                        Tekan "Panggil berikutnya" untuk memulai layanan. Papan diperbarui otomatis setiap 5 detik.
                    </p>
                    <button class="w-50 h-10 smooth-corners-md rounded-2xl bg-teal-500 text-white font-light hover:bg-teal-700 ">+ tambah antrian manual</button>
                </div>



                <div id="queue-boards"
                    hx-get="/admin/antrean"
                    hx-trigger="every 5s"
                    hx-swap="innerHTML"
                    class="mt-6">
                    <?php include __DIR__ . '/partials/queue-boards.php'; ?>
                </div>





                <!-- perlu dibikin component gak ini? V  -->

                <?php
                /** @var list<array<string, mixed>> $visits */
                $statusClass = [
                    'menunggu' => 'bg-[#d9d9d9] text-[#666]',
                    'dilayani' => 'bg-amber-500 text-white',
                    'selesai' => 'bg-[#10b981] text-white',
                ];

                $statusLabel = [
                    'menunggu' => 'Menunggu',
                    'dilayani' => 'Dilayani',
                    'selesai' => 'Selesai',
                ];
                ?>
                <section class="font-sans">
                    <h2 class="mb-[22px] ml-3 mt-7 font-display text-[22px] font-semibold leading-[30px] text-white">Daftar Antrian</h2>

                    <div class="smooth-corners-sm w-full overflow-x-auto bg-[#f0fffb] pb-[7px] shadow-[0_4px_14px_rgba(30,110,95,0.18)]"
                        style="border-radius: 24px;">
                        <table class="ml-[27px] w-[calc(100%-27px)] min-w-[760px] table-fixed border-collapse text-left text-[13px] text-black">
                            <colgroup>
                                <col style="width:122px">
                                <col style="width:150px">
                                <col style="width:170px">
                                <col style="width:100px">
                                <col style="width:140px">
                                <col style="width:180px">
                            </colgroup>
                            <thead>
                                <tr class="h-[45px] border-b border-slate-800 align-top text-[19px] font-light leading-[26px] text-[#555]">
                                    <th class="pl-2.5 pt-[11px] font-light">No</th>
                                    <th class="pt-[11px] font-light">Nama</th>
                                    <th class="pt-[11px] font-light">NIM/NIP</th>
                                    <th class="pt-[11px] font-light">Poli</th>
                                    <th class="pt-[11px] text-center font-light">Status</th>
                                    <th class="pt-[11px] text-center font-light">Aksi</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php if ([] === $visits) { ?>
                                    <tr>
                                        <td colspan="5" class="h-[54px] text-center text-slate-500">Belum ada antrean hari ini.</td>
                                    </tr>
                                <?php } ?>
                                <?php foreach ($visits as $row) { ?>
                                    <?php
                                    $status = (string) $row['status'];
                                    $label = $statusLabel[$status] ?? $status;
                                    $pillClass = $statusClass[$status] ?? $statusClass['menunggu'];
                                    ?>
                                    <tr class="h-[54px] border-b border-slate-800 last:border-b-0">
                                        <td class="pl-2.5"><?php echo e((string) ($row['queue_code'] ?? '')); ?></td>
                                        <td class="truncate pr-2"><?php echo e((string) $row['full_name']); ?></td>
                                        <td><?php echo e((string) $row['identity_number']); ?></td>
                                        <td class="truncate pr-2"><?php echo e((string) $row['service_name']); ?></td>
                                        <td class="text-center">
                                            <div class="flex items-center justify-center gap-2">
                                                <span class="inline-flex h-[22px] w-[90px] items-center justify-center rounded-full text-[10px] font-semibold shadow-[0_1px_2px_rgba(0,0,0,0.25)] <?php echo $pillClass; ?>">
                                                    <?php echo e($label); ?>
                                                </span>

                                                <?php if ('dilayani' === $status) { ?>
                                                    <a href="/admin/periksa?visit=<?php echo e((string) $row['id']); ?>"
                                                        class="inline-flex h-[22px] items-center rounded-full bg-teal-600 px-3 text-[10px] font-semibold text-white transition-colors hover:bg-teal-700">
                                                        Isi Hasil
                                                    </a>
                                                <?php } ?>
                                            </div>
                                        </td>
                                        <td class="text-center">

                                        </td>
                                    </tr>
                                <?php } ?>
                            </tbody>
                        </table>
                    </div>
                </section>








                <!--bosen dan gabut
                        ⠀⠀⠀⠀⠀⠀⠀⠀⠀⠀⠀⠀⠀⠀⠀⠀⠀⠀⠀⠀⠀⠀
                █████╗ ███╗   ███╗██████╗  █████╗ ████████╗██╗   ██╗██╗  ██╗ █████╗ ███╗   ███╗
                ██╔══██╗████╗ ████║██╔══██╗██╔══██╗╚══██╔══╝██║   ██║██║ ██╔╝██╔══██╗████╗ ████║
                ███████║██╔████╔██║██████╔╝███████║   ██║   ██║   ██║█████═╝ ███████║██╔████╔██║
                ██╔══██║██║╚██╔╝██║██╔══██╗██╔══██║   ██║   ██║   ██║██╔═██╗ ██╔══██║██║╚██╔╝██║
                ██║  ██║██║ ╚═╝ ██║██████╔╝██║  ██║   ██║   ╚██████╔╝██║  ██╗██║  ██║██║ ╚═╝ ██║
                ╚═╝  ╚═╝╚═╝     ╚═╝╚═════╝ ╚═╝  ╚═╝   ╚═╝    ╚═════╝ ╚═╝  ╚═╝╚═╝  ╚═╝╚═╝     ╚═╝

                delete this hehe <3 kejahilan kecil
                 -->




                <p class="mt-6 text-sm text-white/80">
                    <a href="/admin" class="underline">&larr; Kembali ke dashboard</a>
                </p>
            </main>
        </div>

        <?php include __DIR__ . '/../components/admin-footer.php'; ?>
    </div>
</div>