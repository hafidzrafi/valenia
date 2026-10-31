<?php
$kehadiranMedis = $kehadiranMedis ?? [];
?>
<section class="smooth-corners-lg relative h-[298px] w-[631px] max-w-full border-2 border-white/60 bg-white/40 px-6 pt-5 shadow-[0_5px_18px_rgba(40,120,105,0.18)] backdrop-blur-md font-['Poppins']"
    style="border-radius: 60px;">
    <h2 class="mb-[16px] text-center font-['Oswald'] text-[28px] font-semibold leading-[34px] text-[#379a89]">Kehadiran Tenaga Medis</h2>

    <div class="flex flex-col gap-[18px]">
        <?php foreach ($kehadiranMedis as $medis) { ?>
            <div class="grid h-[49px] grid-cols-[204px_1fr_auto] items-center rounded-full bg-white pl-[15px] pr-[18px] shadow-[0_3px_5px_rgba(0,0,0,0.3)]">
                <span class="text-[11px] font-semibold text-black"><?php echo e($medis['poli']); ?></span>
                <span class="text-[11px] text-black"><?php echo e(($medis['jam'] ?? '') !== '' ? $medis['jam'] : '-'); ?></span>
                <button type="button"
                    onclick="this.textContent=this.textContent.trim()==='Hadir'?'Kosong':'Hadir';['bg-[#10b981]','text-white','bg-[#d9d9d9]','text-[#666]'].forEach(c=>this.classList.toggle(c));"
                    class="h-[29px] w-[82px] rounded-full text-[10px] font-semibold transition-colors <?php echo $medis['hadir'] ? 'bg-[#10b981] text-white' : 'bg-[#d9d9d9] text-[#666]'; ?>">
                    <?php echo $medis['hadir'] ? 'Hadir' : 'Kosong'; ?>
                </button>
            </div>
        <?php } ?>
    </div>

    <a href="#" class="absolute bottom-[18px] left-[41px] text-[11px] font-medium text-[#379a89] hover:underline">
        Lihat jadwal lengkap ➔
    </a>
</section>