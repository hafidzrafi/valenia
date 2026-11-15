<?php
/** @var list<array<string, mixed>> $medics */
?>
<section class="rounded-[40px] border-2 border-white/60 bg-white/60 p-6 font-sans shadow-[0_5px_18px_rgba(40,120,105,0.18)] backdrop-blur-md">
    <?php if ([] === $medics) { ?>
        <p class="py-6 text-center text-[12px] text-slate-600">Belum ada tenaga medis terdaftar.</p>
    <?php } else { ?>
    <div class="overflow-x-auto">
        <table class="w-full min-w-[720px] text-left text-[12px]">
            <thead>
                <tr class="text-[#379a89]">
                    <th class="pb-3 font-semibold">Nama</th>
                    <th class="pb-3 font-semibold">STR</th>
                    <th class="pb-3 font-semibold">Poli</th>
                    <th class="pb-3 font-semibold">Status</th>
                    <th class="pb-3 text-right font-semibold">Aksi</th>
                </tr>
            </thead>
            <tbody>
            <?php foreach ($medics as $row) { ?>
                <?php
                $id = (string) $row['id'];
                $nama = (string) $row['full_name'];
                $str = (string) ($row['str_number'] ?? '');
                $serviceId = (int) $row['service_id'];
                $aktif = (bool) $row['is_active'];
                $hadir = (bool) $row['is_present'];
                $tercatat = (bool) $row['recorded'];
                $status = $hadir ? 'Hadir' : ($tercatat ? 'Tidak hadir' : 'Belum dicatat');
                $statusClass = $hadir
                    ? 'bg-emerald-500 text-white'
                    : ($tercatat ? 'bg-slate-300 text-slate-600' : 'bg-amber-100 text-amber-700');
                $konfirmasi = [
                    'id' => $id,
                    'tab' => 'tenaga-medis',
                    'aktif' => $aktif,
                    'aksi' => $aktif ? 'nonaktif-medis' : 'aktif-medis',
                    'judul' => $aktif ? 'Nonaktifkan tenaga medis?' : 'Aktifkan tenaga medis?',
                    'pesan' => $aktif
                        ? 'Tenaga medis ini tidak lagi muncul di daftar kehadiran dan tidak dihitung pada poli. Riwayat pemeriksaan tetap tersimpan.'
                        : 'Tenaga medis ini akan aktif kembali dan muncul di daftar kehadiran.',
                ];
                ?>
                <tr class="border-t border-white/70">
                    <td class="py-3 font-semibold text-slate-800">
                        <?php echo e($nama); ?>
                        <?php if (!$aktif) { ?>
                            <span class="ml-1 text-[10px] font-medium text-rose-500">nonaktif</span>
                        <?php } ?>
                    </td>
                    <td class="py-3 text-slate-700"><?php echo e('' === $str ? '-' : $str); ?></td>
                    <td class="py-3 text-slate-700"><?php echo e((string) $row['service_name']); ?></td>
                    <td class="py-3">
                        <span class="rounded-full px-3 py-[3px] text-[10px] font-semibold <?php echo $statusClass; ?>">
                            <?php echo e($status); ?>
                        </span>
                    </td>
                    <td class="py-3">
                        <div class="flex justify-end gap-2">
                            <button type="button"
                                    @click="medic = <?php echo e((string) json_encode(['id' => $id, 'nama' => $nama, 'str' => $str, 'service_id' => $serviceId])); ?>"
                                    class="rounded-full border border-teal-500 px-4 py-[3px] text-[10px] font-semibold text-teal-600 hover:bg-teal-50">Ubah</button>
                            <button type="button"
                                    @click="confirm = <?php echo e((string) json_encode($konfirmasi)); ?>"
                                    class="rounded-full border px-4 py-[3px] text-[10px] font-semibold <?php echo $aktif ? 'border-rose-400 text-rose-500 hover:bg-rose-50' : 'border-emerald-500 text-emerald-600 hover:bg-emerald-50'; ?>">
                                <?php echo $aktif ? 'Nonaktifkan' : 'Aktifkan'; ?>
                            </button>
                        </div>
                    </td>
                </tr>
            <?php } ?>
            </tbody>
        </table>
    </div>
    <?php } ?>
</section>
