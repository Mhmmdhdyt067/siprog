<div x-data
    class="pointer-events-none fixed inset-x-3 bottom-3 z-[95] flex flex-col items-end gap-2 sm:inset-x-auto sm:bottom-4 sm:right-4 sm:w-[22rem]">
    <template x-for="toast in $store.toasts.items" :key="toast.id">
        <div x-transition:enter="transition ease-out duration-250"
            x-transition:enter-start="opacity-0 translate-y-2 sm:translate-x-3"
            x-transition:enter-end="opacity-100 translate-y-0 sm:translate-x-0"
            x-transition:leave="transition ease-in duration-150" x-transition:leave-start="opacity-100"
            x-transition:leave-end="opacity-0 translate-y-2"
            class="pointer-events-auto flex w-full items-start gap-3 rounded-2xl border bg-white px-4 py-3 shadow-[0_24px_60px_-30px_rgba(11,11,12,.45)]"
            :class="{
                'border-emerald-200': toast.type === 'success',
                'border-rose-200': toast.type === 'error',
                'border-[#0A66C8]/20': toast.type !== 'success' && toast.type !== 'error'
            }">
            <span class="mt-0.5 grid h-6 w-6 shrink-0 place-items-center rounded-full text-[11px] font-extrabold"
                :class="{
                    'bg-emerald-50 text-emerald-600': toast.type === 'success',
                    'bg-rose-50 text-rose-600': toast.type === 'error',
                    'bg-[#EAF4FF] text-[#0A66C8]': toast.type !== 'success' && toast.type !== 'error'
                }"
                x-text="toast.type === 'success' ? '✓' : (toast.type === 'error' ? '!' : 'i')"></span>
            <p class="min-w-0 flex-1 text-[12px] font-medium leading-relaxed text-slate-700" x-text="toast.message"></p>
            <button type="button" @click="$store.toasts.remove(toast.id)" aria-label="Tutup notifikasi"
                class="mt-0.5 text-slate-400 transition hover:text-[#0B0B0C]">
                <svg viewBox="0 0 24 24" fill="none" class="h-3.5 w-3.5" aria-hidden="true">
                    <path d="m7 7 10 10M17 7 7 17" stroke="currentColor" stroke-width="2"
                        stroke-linecap="round" />
                </svg>
            </button>
        </div>
    </template>
</div>
