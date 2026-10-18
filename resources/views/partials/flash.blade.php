@if (session('success') || session('error'))
    <div class="pointer-events-none fixed inset-x-4 top-4 z-[60] flex flex-col items-end gap-2 sm:inset-x-auto sm:right-6" data-toasts>
        @if (session('success'))
            <div data-toast class="pointer-events-auto flex w-full items-start gap-3 rounded-xl border border-emerald-200 bg-white p-4 shadow-lg shadow-slate-900/10 sm:w-96">
                <span class="mt-0.5 grid size-6 shrink-0 place-items-center rounded-full bg-emerald-100 text-emerald-600">
                    <svg viewBox="0 0 20 20" fill="currentColor" class="size-3.5"><path fill-rule="evenodd" d="M16.704 4.153a.75.75 0 0 1 .143 1.052l-8 10.5a.75.75 0 0 1-1.127.075l-4.5-4.5a.75.75 0 0 1 1.06-1.06l3.894 3.893 7.48-9.817a.75.75 0 0 1 1.05-.143Z" clip-rule="evenodd"/></svg>
                </span>
                <p class="flex-1 text-sm font-medium text-slate-700">{{ session('success') }}</p>
                <button type="button" data-toast-close class="text-slate-300 transition hover:text-slate-500" aria-label="Dismiss">
                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" class="size-4"><path stroke-linecap="round" d="M6 18 18 6M6 6l12 12"/></svg>
                </button>
            </div>
        @endif

        @if (session('error'))
            <div data-toast class="pointer-events-auto flex w-full items-start gap-3 rounded-xl border border-rose-200 bg-white p-4 shadow-lg shadow-slate-900/10 sm:w-96">
                <span class="mt-0.5 grid size-6 shrink-0 place-items-center rounded-full bg-rose-100 text-rose-600">
                    <svg viewBox="0 0 20 20" fill="currentColor" class="size-3.5"><path d="M6.28 5.22a.75.75 0 0 0-1.06 1.06L8.94 10l-3.72 3.72a.75.75 0 1 0 1.06 1.06L10 11.06l3.72 3.72a.75.75 0 1 0 1.06-1.06L11.06 10l3.72-3.72a.75.75 0 0 0-1.06-1.06L10 8.94 6.28 5.22Z"/></svg>
                </span>
                <p class="flex-1 text-sm font-medium text-slate-700">{{ session('error') }}</p>
                <button type="button" data-toast-close class="text-slate-300 transition hover:text-slate-500" aria-label="Dismiss">
                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" class="size-4"><path stroke-linecap="round" d="M6 18 18 6M6 6l12 12"/></svg>
                </button>
            </div>
        @endif
    </div>
@endif
