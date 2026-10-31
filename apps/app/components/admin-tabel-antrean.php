<?php
$antreanAktif = $antreanAktif ?? [];

$statusClass = [
    'Selesai'   => 'bg-[#10b981] text-white',
    'Menunggu'  => 'bg-[#d9d9d9] text-[#666]',
    'Ditangani' => 'bg-amber-500 text-white',
];
?>
<section class="font-['Poppins']">
    <h2 class="mb-[22px] ml-3 font-['Oswald'] text-[22px] font-semibold leading-[30px] text-white">Daftar Antrian Aktif</h2>

    <div class="smooth-corners-sm w-full overflow-hidden bg-[#f0fffb] pb-[7px] shadow-[0_4px_14px_rgba(30,110,95,0.18)]"
        style="border-radius: 24px;">
        <table class="ml-[27px] w-[calc(100%-27px)] table-fixed border-collapse text-left text-[13px] text-black">
            <colgroup>
                <col style="width:142px"><col style="width:210px"><col style="width:271px"><col style="width:154px"><col>
            </colgroup>
            <thead>
                <tr class="h-[45px] border-b border-slate-800 align-top text-[19px] font-light leading-[26px] text-[#555]">
                    <th class="pl-2.5 pt-[11px] font-light">No</th>
                    <th class="pt-[11px] font-light">Nama</th>
                    <th class="pt-[11px] font-light">NIM/NIP</th>
                    <th class="pt-[11px] font-light">Poli</th>
                    <th class="pt-[11px] text-center font-light">Status</th>
                </tr>
            </thead>
            <tbody>
                <?php if ($antreanAktif === []): ?>
                    <tr><td colspan="5" class="h-[54px] text-center text-slate-500">Belum ada antrean aktif.</td></tr>
                <?php endif; ?>
                <?php foreach ($antreanAktif as $row): ?>
                    <tr class="h-[54px] border-b border-slate-800 last:border-b-0">
                        <td class="pl-2.5"><?= e($row['no']) ?></td>
                        <td><?= e($row['nama']) ?></td>
                        <td><?= e($row['nim']) ?></td>
                        <td><?= e($row['poli']) ?></td>
                        <td class="text-center">
                            <button type="button" data-status-btn data-status="<?= e($row['status']) ?>"
                                class="h-[22px] w-[90px] rounded-full text-[10px] font-semibold shadow-[0_1px_2px_rgba(0,0,0,0.25)] transition-colors <?= $statusClass[$row['status']] ?? $statusClass['Menunggu'] ?>">
                                <?= e($row['status']) ?>
                            </button>
                        </td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>
</section>

<script>
    (function () {
        const cls = {
            Selesai:   'bg-[#10b981] text-white',
            Menunggu:  'bg-[#d9d9d9] text-[#666]',
            Ditangani: 'bg-amber-500 text-white'
        };
        const next = { Selesai: 'Menunggu', Menunggu: 'Ditangani', Ditangani: 'Selesai' };

        document.querySelectorAll('[data-status-btn]').forEach(function (btn) {
            btn.addEventListener('click', function () {
                const to = next[btn.dataset.status] || 'Menunggu';
                Object.values(cls).forEach(function (c) { btn.classList.remove(...c.split(' ')); });
                btn.classList.add(...cls[to].split(' '));
                btn.dataset.status = to;
                btn.textContent = to;
            });
        });
    })();
</script>