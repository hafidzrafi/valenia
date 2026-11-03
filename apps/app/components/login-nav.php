<?php
/**
 * Slim login nav: the same glass pill as the landing header, without the menu,
 * the dropdowns, or the auth aware buttons. The login page never needs them.
 */
?>
<div class="w-[88%] max-w-[1100px] rounded-[64px] border border-white/40
            bg-gradient-to-br from-white/90 to-white/60 px-8 py-4
            shadow-[6px_7px_7px_0px_rgba(0,0,0,0.25)] backdrop-blur-xl">
    <a href="/" class="flex w-fit items-center gap-3">
        <span class="flex size-12 items-center justify-center">
            <img src="/images/valenia-logo.svg" alt="VALENIA" class="h-full w-full object-contain"
                 onerror="this.onerror=null;this.src='/images/logo.svg';">
        </span>
        <span class="font-display text-2xl font-light tracking-wider text-teal-500">VALENIA</span>
    </a>
</div>
