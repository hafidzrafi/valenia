<div
    x-data="{ x: -100, y: -100, visible: false, hovering: false }"
    @mousemove.window="x = $event.clientX; y = $event.clientY; visible = true"
    @mouseover.window="hovering = !!$event.target.closest('button, a')"
    @mouseout.window="if (!$event.relatedTarget) visible = false"
    @cursor-hide.window="visible = false"
    @cursor-show.window="visible = true">
    <div
        class="pointer-events-none flex items-center justify-center fixed top-0 left-0 rounded-full z-50 opacity-0 bg-teal-300/50 transition-[width,height,transform,opacity] duration-200 ease-out"
        :class="[visible ? 'opacity-100' : '', hovering ? 'w-10 h-10' : 'w-4 h-4']"
        :style="`transform: translate3d(${x - (hovering ? 20 : 8)}px, ${y - (hovering ? 20 : 8)}px, 0px);`">

        <i class="ph ph-arrow-circle-up-right text-white text-2xl"
        :class="[hovering ? 'opacity-100' : 'opacity-0']"
        ></i>
    </div>
</div>