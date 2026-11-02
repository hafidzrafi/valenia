<?php

/**
 * Rotating "Nomor Dilayani Saat Ini" card for the public landing page.
 *
 * Expects $boards from queue_boards(): each row has current_queue_code and
 * service_name. Cycles one poli at a time every 4s (Alpine), while htmx
 * refreshes the underlying data every 10s from the wrapping container.
 *
 * All polis are stacked and morphed rather than re-created, so every step
 * animates in both directions. Movement is directional: advancing slides the
 * outgoing card out to the left while the incoming card arrives from the
 * right, and wrapping from the last poli back to the first counts as an
 * advance, so the loop reads as one continuous leftward flow.
 *
 * @var list<array<string, mixed>> $boards
 */
$cards = array_map(
    static fn (array $board): array => [
        'code' => null !== $board['current_queue_code'] ? (string) $board['current_queue_code'] : '—',
        'service' => (string) $board['service_name'],
    ],
    $boards
);

if ([] === $cards) {
    $cards = [['code' => '—', 'service' => 'Poli Umum']];
}

$cardsJson = htmlspecialchars((string) json_encode($cards), ENT_QUOTES, 'UTF-8');
?>
<div
    x-data="{
        cards: JSON.parse($el.dataset.cards),
        i: 0,
        prev: null,
        dir: 1,
        paused: false,
        timer: null,
        go(next) {
            if (next === this.i) return;

            this.prev = this.i;
            this.dir = (next === 0 && this.i === this.cards.length - 1) || next > this.i ? 1 : -1;
            this.i = next;

            clearTimeout(this.timer);
            this.timer = setTimeout(() => { this.prev = null; }, 520);
        },
        advance() { this.go((this.i + 1) % this.cards.length); },
        styleFor(idx) {
            const shift = 120;

            if (idx === this.i) return 'opacity: 1; transform: translateX(0px); transition-duration: 460ms; transition-delay: 160ms;';
            if (idx === this.prev) return 'opacity: 0; transform: translateX(' + (-this.dir * shift) + 'px); transition-duration: 220ms;';

            return 'opacity: 0; transform: translateX(' + (this.dir * shift) + 'px); transition-duration: 460ms;';
        }
    }"
    data-cards="<?php echo $cardsJson; ?>"
    x-init="
        setInterval(() => { if (!paused) advance(); }, 4000);
        setInterval(async () => {
            try {
                const response = await fetch('/papan-antrean', { headers: { Accept: 'application/json' } });
                if (!response.ok) return;
                const data = await response.json();
                if (!Array.isArray(data.cards) || 0 === data.cards.length) return;
                cards = data.cards;
                if (i >= cards.length) i = 0;
                prev = null;
            } catch (error) {
                /* keep showing the last known board */
            }
        }, 10000);
    "
    @mouseenter="paused = true"
    @mouseleave="paused = false"
    class="flex-grow-0 flex-shrink-0 w-[588px] h-[392px] relative overflow-hidden rounded-[60px] bg-gradient-to-br from-white/80 to-white/50 backdrop-blur-[44.9px] transition-all transform hover:scale-105"
    style="box-shadow: 9px 15px 12.899999618530273px 0 rgba(0,0,0,0.1);">

    <img src="/images/elegant-lotus.webp" alt="lotus" class="pt-7 pl-3">

    <div class="flex flex-col justify-start items-center w-[456px] absolute left-[65px] top-[66px]">
        <p class="w-[456px] h-[78.5px] text-[32px] font-medium text-center text-black">
            Nomor Dilayani Saat ini
        </p>

        <div class="relative w-[456px] h-[157px] overflow-hidden">
            <?php foreach ($cards as $index => $card) { ?>
                <div
                    :style="styleFor(<?php echo $index; ?>)"
                    <?php echo 0 === $index ? '' : 'style="opacity: 0;"'; ?>
                    class="absolute inset-0 flex flex-col items-center transition-[opacity,transform] ease-out will-change-[opacity,transform]">
                    <p class="w-[456px] h-[78.5px] text-8xl font-black text-center text-slate-900">
                        <?php echo e($card['code']); ?>
                    </p>
                    <p class="w-[456px] h-[78.5px] text-[32px] font-medium text-center text-black">
                        <?php echo e($card['service']); ?>
                    </p>
                </div>
            <?php } ?>
        </div>
    </div>

    <div class="absolute bottom-6 left-1/2 -translate-x-1/2 flex items-center justify-center gap-2">
        <template x-for="(card, idx) in cards" :key="idx">
            <button
                type="button"
                @click="go(idx)"
                :aria-label="'Tampilkan ' + card.service"
                :class="idx === i ? 'bg-slate-900 w-6' : 'bg-slate-400/60 w-2 hover:bg-slate-600'"
                class="h-2 rounded-full transition-all duration-300"></button>
        </template>
    </div>
</div>
