@props(['title', 'message'])

<div class="flex flex-col items-center justify-center rounded-2xl border border-dashed border-slate-300 bg-white px-6 py-16 text-center">
    <div class="mb-4 grid size-14 place-items-center rounded-2xl bg-slate-100 text-slate-400">
        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5" class="size-7">
            <path stroke-linecap="round" stroke-linejoin="round" d="M20.25 7.5l-.625 10.632a2.25 2.25 0 01-2.247 2.118H6.622a2.25 2.25 0 01-2.247-2.118L3.75 7.5m8.25 3v6.75m0 0l-3-3m3 3l3-3M3.375 7.5h17.25c.621 0 1.125-.504 1.125-1.125v-1.5c0-.621-.504-1.125-1.125-1.125H3.375c-.621 0-1.125.504-1.125 1.125v1.5c0 .621.504 1.125 1.125 1.125z"/>
        </svg>
    </div>
    <h3 class="text-base font-semibold text-slate-900">{{ $title }}</h3>
    <p class="mt-1 max-w-sm text-sm text-slate-500">{{ $message }}</p>
    @if (isset($action))
        <div class="mt-6">{{ $action }}</div>
    @endif
</div>
