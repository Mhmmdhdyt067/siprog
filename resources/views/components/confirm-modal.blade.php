<div x-data x-cloak x-show="$store.confirm.open"
    x-on:keydown.escape.window="$store.confirm.close()"
    class="fixed inset-0 z-[90] flex items-end justify-center p-3 sm:items-center sm:p-6" role="alertdialog"
    aria-modal="true" aria-label="Konfirmasi tindakan">
    <div x-show="$store.confirm.open" x-transition:enter="transition ease-out duration-200"
        x-transition:enter-start="opacity-0" x-transition:enter-end="opacity-100"
        x-transition:leave="transition ease-in duration-150" x-transition:leave-start="opacity-100"
        x-transition:leave-end="opacity-0" class="absolute inset-0 bg-[#0B0B0C]/50 backdrop-blur-sm"
        @click="$store.confirm.close()"></div>

    <div x-show="$store.confirm.open" x-transition:enter="transition ease-out duration-250"
        x-transition:enter-start="opacity-0 translate-y-4 sm:scale-[.98]"
        x-transition:enter-end="opacity-100 translate-y-0 sm:scale-100"
        x-transition:leave="transition ease-in duration-150"
        x-transition:leave-start="opacity-100 translate-y-0 sm:scale-100"
        x-transition:leave-end="opacity-0 translate-y-3 sm:scale-[.98]"
        class="relative w-full max-w-md overflow-hidden rounded-[26px] border border-black/5 bg-white shadow-[0_40px_120px_-40px_rgba(11,11,12,.55)]">

        <div class="px-5 pb-1 pt-6 text-center sm:px-7">
            <div class="mx-auto grid h-12 w-12 place-items-center rounded-2xl"
                :class="$store.confirm.tone === 'danger' ? 'bg-rose-50 text-rose-600' : 'bg-[#EAF4FF] text-[#0A66C8]'">
                <svg viewBox="0 0 24 24" fill="none" class="h-5 w-5" aria-hidden="true">
                    <path d="M12 8.5v4.5M12 16.5h.01" stroke="currentColor" stroke-width="2"
                        stroke-linecap="round" />
                    <path d="M10.4 4.2 3.6 16a1.9 1.9 0 0 0 1.6 2.9h13.6a1.9 1.9 0 0 0 1.6-2.9L13.6 4.2a1.9 1.9 0 0 0-3.2 0Z"
                        stroke="currentColor" stroke-width="1.7" stroke-linejoin="round" />
                </svg>
            </div>
            <h2 class="mt-4 text-[17px] font-bold tracking-[-0.02em]" x-text="$store.confirm.title"></h2>
            <p class="mt-1.5 text-[12px] leading-relaxed text-slate-500" x-text="$store.confirm.message"></p>
        </div>

        <div class="flex flex-col-reverse gap-2 px-5 pb-5 pt-5 sm:flex-row sm:justify-end sm:px-7">
            <button type="button" @click="$store.confirm.close()" :disabled="$store.confirm.busy"
                class="btn btn-light w-full sm:w-auto" x-text="$store.confirm.cancelText"></button>
            <button type="button" @click="$store.confirm.run()" :disabled="$store.confirm.busy"
                class="btn w-full sm:w-auto"
                :class="$store.confirm.tone === 'danger' ? 'btn-danger' : 'btn-primary'">
                <span x-show="$store.confirm.busy"
                    class="h-3 w-3 animate-spin rounded-full border-2 border-current border-t-transparent"></span>
                <span x-text="$store.confirm.busy ? 'Memproses…' : $store.confirm.confirmText"></span>
            </button>
        </div>
    </div>
</div>
