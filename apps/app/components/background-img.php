<?php

$isform = $isform ?? false;

?>

<?php if (false == $isform) { ?>

    <div class="fixed inset-0 -z-[1]">
        <div class="absolute inset-0 bg-black/30 z-10"></div>
        <div
            class="absolute inset-0 bg-cover bg-center bg-no-repeat opacity-60"
            style="background-image: url('/images/background.png');"></div>
    </div>

<?php } else { ?>

    <div class="fixed inset-0 -z-[1]">
        <div class="absolute inset-0 bg-black/30 z-10"></div>
        <div
            class="absolute inset-0 bg-cover bg-center bg-no-repeat"
            style="background-image: url('/images/background.png');"></div>
    </div>

<?php } ?>