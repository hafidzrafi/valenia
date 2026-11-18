<div
    x-data="{ x: -100, y: -100, visible: false, hovering: false }"
    @mousemove.window="x = $event.clientX; y = $event.clientY; visible = true"
    @mouseover.window="hovering = !!$event.target.closest('button, a')"
    @mouseout.window="if (!$event.relatedTarget) visible = false"
    @cursor-hide.window="visible = false"
    @cursor-show.window="visible = true">
    <div
        class="pointer-events-none flex items-center justify-center fixed top-0 left-0 rounded-full z-50 opacity-0 bg-teal-300/50 transition-[width,height,transform,opacity] duration-200 ease-out"
        :class="[visible ? 'opacity-100 max-lg:opacity-0' : '', hovering ? 'w-12 h-12 bg-teal-400/50' : 'w-4 h-4']"
        :style="`transform: translate3d(${x - (hovering ? 24 : 8)}px, ${y - (hovering ? 24 : 8)}px, 0px);`">

        <i class="ph ph-arrow-up-right text-white text-lg"
        :class="[hovering ? 'opacity-100' : 'opacity-0']"
        ></i>
    </div>
</div>