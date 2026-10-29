<div
    x-data="{ x: -100, y: -100, visible: false }"
    @mousemove.window="x = $event.clientX; y = $event.clientY; visible = true"
    @mouseout.window="if (!$event.relatedTarget) visible = false"
>
    <div
        class="pointer-events-none fixed top-0 left-0 w-8 h-8 bg-teal-300/50 rounded-full z-50 opacity-0"
        :class="visible && 'opacity-100'"
        :style="`transform: translate3d(${x - 16}px, ${y - 16}px, 0px);`"
    ></div>
</div>