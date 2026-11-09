<?php
/**
 * Modal "Unduh Laporan" + toast keberhasilan (VALENIA-42).
 * State induk (unduhOpen, toastOpen, toastFile, simpanUnduh) ada di x-data pada view.
 * Dropdown poli dan kalender rentang tanggal adalah komponen Alpine kustom
 * agar sesuai desain Figma. Daftar tahun dibuat otomatis dari tahun mulai data
 * (2025) sampai tahun berjalan.
 * TODO(backend): simpanUnduh baru menampilkan toast. Saat route pembuat PDF
 * siap, fungsi ini harus memicu unduhan sungguhan.
 */
?>
<div x-show="unduhOpen" x-transition.opacity style="display: none;"
     @click.self="unduhOpen = false"
     @keydown.escape.window="unduhOpen = false"
     class="fixed inset-0 z-50 flex items-center justify-center bg-black/30 p-4"
     role="dialog" aria-modal="true" aria-labelledby="judul-unduh-laporan">
    <div class="w-full max-w-[400px] rounded-[40px] bg-white p-8 font-sans shadow-xl">
        <h2 id="judul-unduh-laporan" class="text-center font-display text-[34px] font-bold text-slate-500">Unduh Laporan</h2>

        <form class="mt-6 flex flex-col gap-4" @submit.prevent="simpanUnduh($el)" x-data="{
            poliOpen: false,
            poli: 'umum',
            poliList: [
                { value: 'umum', label: 'Umum', full: 'Poli Umum' },
                { value: 'gigi', label: 'Gigi', full: 'Poli Gigi' },
                { value: 'konseling', label: 'Konseling', full: 'Konseling' }
            ],
            kalOpen: false,
            bulanOpen: false,
            tahunOpen: false,
            bulanList: ['Januari', 'Februari', 'Maret', 'April', 'Mei', 'Juni', 'Juli', 'Agustus', 'September', 'Oktober', 'November', 'Desember'],
            tahunList: Array.from({ length: new Date().getFullYear() - 2025 + 1 }, (_, i) => 2025 + i),
            hariList: ['MIN', 'SEN', 'SEL', 'RAB', 'KAM', 'JUM', 'SAB'],
            viewMonth: new Date().getMonth(),
            viewYear: new Date().getFullYear(),
            start: null,
            end: null,
            get poliLabel() {
                return this.poliList.find((p) => p.value === this.poli).full;
            },
            iso(d) {
                return d.getFullYear() + '-' + String(d.getMonth() + 1).padStart(2, '0') + '-' + String(d.getDate()).padStart(2, '0');
            },
            get cells() {
                const first = new Date(this.viewYear, this.viewMonth, 1).getDay();
                const all = Array.from({ length: 42 }, (_, i) => {
                    const d = new Date(this.viewYear, this.viewMonth, 1 - first + i);
                    return { iso: this.iso(d), day: d.getDate(), other: d.getMonth() !== this.viewMonth };
                });
                return all[35].other ? all.slice(0, 35) : all;
            },
            geser(n) {
                const d = new Date(this.viewYear, this.viewMonth + n, 1);
                this.viewMonth = d.getMonth();
                this.viewYear = d.getFullYear();
            },
            pick(iso) {
                if (this.start === null || this.end !== null) {
                    this.start = iso;
                    this.end = null;
                    return;
                }
                if (iso < this.start) {
                    this.end = this.start;
                    this.start = iso;
                } else {
                    this.end = iso;
                }
                this.kalOpen = false;
            },
            isEdge(iso) {
                return iso === this.start || iso === this.end;
            },
            inRange(iso) {
                return this.start !== null && this.end !== null && iso > this.start && iso < this.end;
            },
            get rentangLabel() {
                if (this.start === null) {
                    return 'Pilih rentang tanggal';
                }
                const fmt = (s, o) => new Date(s + 'T00:00:00').toLocaleDateString('id-ID', o);
                if (this.end === null || this.end === this.start) {
                    return fmt(this.start, { weekday: 'long', day: 'numeric', month: 'long', year: 'numeric' });
                }
                const o = { day: 'numeric', month: 'short', year: 'numeric' };
                return fmt(this.start, o) + ' - ' + fmt(this.end, o);
            }
        }">
            <input type="hidden" name="poli" :value="poli">
            <input type="hidden" name="dari" :value="start || ''">
            <input type="hidden" name="sampai" :value="end || start || ''">

            <div class="flex flex-col gap-1">
                <span class="text-xs font-semibold text-slate-800">Pilih Poli</span>
                <div class="relative" @click.outside="poliOpen = false">
                    <button type="button" @click="poliOpen = !poliOpen"
                        class="flex h-10 w-full items-center justify-between rounded-full border border-slate-500 bg-white pl-4 pr-4 text-xs text-slate-700 focus:border-teal-500 focus:outline-none"
                        aria-haspopup="listbox" :aria-expanded="poliOpen">
                        <span x-text="poliLabel"></span>
                        <i class="ph ph-caret-down text-[16px] text-slate-700 transition-transform" :class="poliOpen ? 'rotate-180' : ''"></i>
                    </button>
                    <div x-show="poliOpen" x-transition style="display: none;" role="listbox"
                         class="absolute left-0 top-full z-30 mt-1 w-full rounded-xl border border-slate-200 bg-white p-1 shadow-lg">
                        <template x-for="p in poliList" :key="p.value">
                            <button type="button" role="option" @click="poli = p.value; poliOpen = false"
                                class="block w-full rounded-lg px-3 py-2 text-left text-[11px]"
                                :class="poli === p.value ? 'bg-teal-500 text-white' : 'text-slate-700 hover:bg-teal-50'"
                                x-text="p.label"></button>
                        </template>
                    </div>
                </div>
            </div>

            <div class="flex flex-col gap-1">
                <span class="text-xs font-semibold text-slate-800">Rentang Tanggal</span>
                <div class="relative" @click.outside="kalOpen = false; bulanOpen = false; tahunOpen = false">
                    <button type="button" @click="kalOpen = !kalOpen"
                        class="flex h-10 w-full items-center justify-between rounded-full border border-slate-500 bg-white pl-4 pr-4 text-xs text-slate-700 focus:border-teal-500 focus:outline-none"
                        aria-haspopup="dialog" :aria-expanded="kalOpen">
                        <span x-text="rentangLabel" :class="start === null ? 'text-slate-400' : ''"></span>
                        <i class="ph ph-calendar-blank text-[16px] text-slate-700"></i>
                    </button>

                    <div x-show="kalOpen" x-transition style="display: none;"
                         class="absolute left-0 top-full z-30 mt-2 w-[290px] rounded-2xl bg-white p-4 shadow-xl ring-1 ring-black/5">
                        <div class="mb-3 flex items-center justify-between gap-2">
                            <button type="button" @click="geser(-1)" aria-label="Bulan sebelumnya"
                                class="flex size-7 items-center justify-center rounded-full border border-slate-400 text-slate-700 hover:bg-teal-50">
                                <i class="ph ph-caret-left text-[12px]"></i>
                            </button>

                            <div class="flex items-center gap-2">
                                <div class="relative">
                                    <button type="button" @click="bulanOpen = !bulanOpen; tahunOpen = false"
                                        class="flex h-7 items-center gap-1 rounded-full border border-slate-500 bg-white px-3 text-[10px] text-slate-700">
                                        <span x-text="bulanList[viewMonth]"></span>
                                        <i class="ph ph-caret-down text-[10px]"></i>
                                    </button>
                                    <div x-show="bulanOpen" style="display: none;"
                                         class="absolute left-0 top-full z-40 mt-1 max-h-40 w-28 overflow-y-auto rounded-xl bg-white p-1 shadow-lg ring-1 ring-black/5">
                                        <template x-for="(b, i) in bulanList" :key="b">
                                            <button type="button" @click="viewMonth = i; bulanOpen = false"
                                                class="block w-full rounded-lg px-3 py-1.5 text-left text-[11px]"
                                                :class="i === viewMonth ? 'bg-teal-500 text-white' : 'text-slate-700 hover:bg-teal-50'"
                                                x-text="b"></button>
                                        </template>
                                    </div>
                                </div>

                                <div class="relative">
                                    <button type="button" @click="tahunOpen = !tahunOpen; bulanOpen = false"
                                        class="flex h-7 items-center gap-1 rounded-full border border-slate-500 bg-white px-3 text-[10px] text-slate-700">
                                        <span x-text="viewYear"></span>
                                        <i class="ph ph-caret-down text-[10px]"></i>
                                    </button>
                                    <div x-show="tahunOpen" style="display: none;"
                                         class="absolute left-0 top-full z-40 mt-1 max-h-40 w-20 overflow-y-auto rounded-xl bg-white p-1 shadow-lg ring-1 ring-black/5">
                                        <template x-for="t in tahunList" :key="t">
                                            <button type="button" @click="viewYear = t; tahunOpen = false"
                                                class="block w-full rounded-lg px-3 py-1.5 text-left text-[11px]"
                                                :class="t === viewYear ? 'bg-teal-500 text-white' : 'text-slate-700 hover:bg-teal-50'"
                                                x-text="t"></button>
                                        </template>
                                    </div>
                                </div>
                            </div>

                            <button type="button" @click="geser(1)" aria-label="Bulan berikutnya"
                                class="flex size-7 items-center justify-center rounded-full border border-slate-400 text-slate-700 hover:bg-teal-50">
                                <i class="ph ph-caret-right text-[12px]"></i>
                            </button>
                        </div>

                        <div class="grid grid-cols-7 gap-y-1 text-center">
                            <template x-for="h in hariList" :key="h">
                                <span class="pb-1 text-[9px] font-bold text-slate-600" x-text="h"></span>
                            </template>
                            <template x-for="c in cells" :key="c.iso">
                                <button type="button" @click="pick(c.iso)"
                                    class="mx-auto flex size-8 items-center justify-center rounded-lg text-[11px]"
                                    :class="isEdge(c.iso) ? 'bg-teal-700 font-bold text-white' : (inRange(c.iso) ? 'bg-teal-100 font-bold text-slate-800' : (c.other ? 'text-slate-400' : 'font-bold text-slate-800 hover:bg-teal-50'))"
                                    x-text="c.day"></button>
                            </template>
                        </div>

                        <p class="mt-3 text-center text-[10px] text-slate-500">Klik dua tanggal untuk rentang, atau satu tanggal lalu klik di luar kalender.</p>
                    </div>
                </div>
            </div>

            <div class="mt-6 flex justify-end gap-3">
                <button type="button" @click="unduhOpen = false"
                    class="h-9 rounded-full bg-white px-6 text-xs font-medium text-teal-600 shadow-md transition-colors hover:bg-teal-50">
                    Batal
                </button>
                <button type="submit"
                    class="h-9 rounded-full bg-teal-500 px-6 text-xs font-medium text-white transition-colors hover:bg-teal-600">
                    Simpan
                </button>
            </div>
        </form>
    </div>
</div>

<div class="pointer-events-none fixed inset-x-0 top-6 z-[60] flex justify-center px-4 lg:pl-64">
    <div x-show="toastOpen" x-transition style="display: none;"
         class="pointer-events-auto flex items-center gap-3 rounded-full bg-teal-500 py-3 pl-4 pr-6 font-sans text-white shadow-lg"
         role="status" aria-live="polite">
        <span class="flex size-9 shrink-0 items-center justify-center rounded-full bg-white/25">
            <i class="ph ph-check text-[22px]"></i>
        </span>
        <div class="leading-tight">
            <p class="text-sm font-semibold">Laporan berhasil diunduh</p>
            <p class="text-[11px]" x-text="toastFile"></p>
        </div>
    </div>
</div>