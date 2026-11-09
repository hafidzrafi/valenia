<?php
/** @var list<array{label: string, umum: int, gigi: int, konseling: int}> $minggu */
$active = 'rekap-bulanan';
$token = $token ?? '';
$role = $role ?? 'admin';
$headerTitle = 'Laporan Rekap Bulanan';
$headerSubtitle = 'Layanan Poliklinik Politeknik Negeri Malang';
$adminNama = 'Petugas '.ucfirst($role);
$adminUnit = 'Poliklinik Polinema';

$totalUmum = (int) array_sum(array_column($minggu, 'umum'));
$totalGigi = (int) array_sum(array_column($minggu, 'gigi'));
$totalKonseling = (int) array_sum(array_column($minggu, 'konseling'));
$totalSemua = $totalUmum + $totalGigi + $totalKonseling;
?>
<div class="flex min-h-screen flex-col lg:flex-row">
    <?php include __DIR__.'/../components/admin-sidebar.php'; ?>

    <div class="flex min-h-screen min-w-0 flex-1 flex-col"
         style="background: radial-gradient(circle at 85% 15%, rgba(160,215,205,0.7) 0%, transparent 50%), linear-gradient(135deg, #a9dcb0 0%, #88cebb 50%, #3fa896 100%);">

        <div class="mx-auto flex w-full max-w-[1080px] flex-1 flex-col px-8 pb-10">
            <?php include __DIR__.'/../components/admin-header.php'; ?>

            <div class="mt-[21px] flex flex-col gap-8" x-data="{
                    unduhOpen: false,
                    toastOpen: false,
                    toastFile: '',
                    simpanUnduh(form) {
                        const data = new FormData(form);
                        const dari = data.get('dari') ? new Date(data.get('dari') + 'T00:00:00') : new Date();
                        const bulan = dari.toLocaleDateString('id-ID', { month: 'long' });
                        this.toastFile = 'Rekap_kunjungan_' + bulan + '_' + dari.getFullYear() + '.pdf';
                        this.unduhOpen = false;
                        this.toastOpen = true;
                        setTimeout(() => { this.toastOpen = false; }, 4000);
                    }
                }">
                <?php include __DIR__.'/../components/admin-rekap-ringkasan.php'; ?>
                <?php include __DIR__.'/../components/admin-rekap-tabel.php'; ?>
                <?php include __DIR__.'/../components/admin-rekap-unduh.php'; ?>
            </div>
        </div>

        <?php include __DIR__.'/../components/admin-footer.php'; ?>
    </div>
</div>