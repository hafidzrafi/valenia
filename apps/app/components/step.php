<?php

$status_step = $status_step ?? '';
$step_text = $step_text ?? '';

$bgColor = match ($status_step) {
    'current' => 'bg-teal-500',
    'wait' => 'bg-gray-300',
    'done' => 'bg-[#DAF6F0]',
    default => 'bg-gray-400' // default if disabled
};

?>

<?php if ('current' == $status_step) { ?>
    <div class=" w-11 h-11 rounded-full smooth-corners-lg  absolute flex justify-center items-center border border-gray-500 <?php echo $bgColor; ?>">
        <h3 class="text-white text-[20px]"> <?php echo $step_text; ?></h3>
    </div>
<?php } elseif ('wait' == $status_step) { ?>
    <div class=" w-11 h-11 rounded-full smooth-corners-lg  absolute flex justify-center items-center border border-gray-500 <?php echo $bgColor; ?>">
        <h3 class="text-black text-[20px]"> <?php echo $step_text; ?></h3>
    </div>
<?php } elseif ('done' == $status_step) { ?>
    <div class=" w-11 h-11 rounded-full smooth-corners-lg  absolute flex justify-center items-center border border-gray-500 <?php echo $bgColor; ?>">
        <i class="ph ph-check text-teal-500 text-[32px]"></i>
    </div>
<?php } else { ?>
    <div class=" w-11 h-11 rounded-full smooth-corners-lg  absolute flex justify-center items-center border border-gray-500 <?php echo $bgColor; ?>">
        <h3 class="text-white text-[20px]"> <?php echo $step_text; ?></h3>
    </div>

<?php } ?>