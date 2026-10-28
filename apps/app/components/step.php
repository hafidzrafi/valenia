<?php

$status_step = $status_step ?? "";
$step_text = $step_text ?? "";

$bgColor = match ($status_step) {
    'current' => 'bg-teal-500',
    'wait' => 'bg-gray-300',
    'done' => 'bg-[#DAF6F0]',
    default => 'bg-gray-400' // default if disabled
};



?>

<?php if ($status_step == "current"): ?>
    <div class=" w-11 h-11 rounded-full smooth-corners-lg  absolute flex justify-center items-center <?= $bgColor ?>">
        <h3 class="text-white text-[20px] font-semibold"> <?php echo ($step_text) ?></h3>
    </div>
<?php elseif ($status_step == "wait"): ?>
    <div class=" w-11 h-11 rounded-full smooth-corners-lg  absolute flex justify-center items-center <?= $bgColor ?>">
        <h3 class="text-black text-[20px] font-semibold"> <?php echo ($step_text) ?></h3>
    </div>
<?php elseif ($status_step == "done"): ?>
    <div class=" w-11 h-11 rounded-full smooth-corners-lg  absolute flex justify-center items-center <?= $bgColor ?>">
        <i class="ph ph-check text-teal-500 text-[32px]"></i>
    </div>
<?php else: ?>
    <div class=" w-11 h-11 rounded-full smooth-corners-lg  absolute flex justify-center items-center <?= $bgColor ?>">
        <h3 class="text-white text-[20px] font-semibold"> <?php echo ($step_text) ?></h3>
    </div>

<?php endif ?>