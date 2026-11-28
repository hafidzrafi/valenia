<?php

/**
 * Slim login nav: the same glass pill as the landing header, without the menu,
 * the dropdowns, or the auth aware buttons. The login page never needs them.
 */
?>
<div class="fixed top-0 left-1/2 -translate-x-1/2 z-50 w-[70%] max-lg:w-full max-lg:rounded-[0px] mt-6 h-20 px-8 py-4 max-lg:mt-0 max-lg:py-11 bg-gradient-to-br from-white/90 to-white/60 rounded-full shadow-md shadow-gray-500 backdrop-blur-xl flex justify-between items-center"">
    <a href=" /" class="flex w-fit items-center gap-3">
    <span class="flex size-12 items-center justify-center">
        <img src="/images/valenia-logo.svg" alt="VALENIA" class="h-full w-full object-contain"
            onerror="this.onerror=null;this.src='/images/logo.svg';">
    </span>
    <span class="font-display text-2xl font-light tracking-wider text-teal-500">VALENIA</span>
    </a>
</div>