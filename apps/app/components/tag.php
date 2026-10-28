<?php
$status_tag = $status_tag ?? ''; // 'positive', 'negative', or 'disabled'
$label_tag = $label_tag ?? 'Label'; // Text


$bgColor = match($status_tag) {
    'positive' => 'bg-emerald-500',
    'negative' => 'bg-amber-500',
    'disabled' => 'bg-gray-400',
    default => 'bg-gray-400' // default if disabled
};
?>

<div 
  class="w-[90px] h-[31px] relative overflow-hidden rounded-full <?= $bgColor ?>"
  style="box-shadow: 0px 1px 2px 0 rgba(0,0,0,0.25);"
>
  <p class="w-[162px] h-4 absolute left-[-36px] top-[7px] text-xs font-medium text-center text-white">
    <?= htmlspecialchars($label_tag) ?>
  </p>
</div>
