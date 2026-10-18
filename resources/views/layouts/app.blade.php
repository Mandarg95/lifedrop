<!DOCTYPE html>
<html lang="en" class="h-full">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title', 'Dashboard') · LifeDrop</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="h-full bg-slate-50 text-slate-800 antialiased">
<div class="min-h-full">

    <div id="sidebar-overlay" class="fixed inset-0 z-40 hidden bg-slate-950/40 backdrop-blur-sm lg:hidden"></div>

    <aside id="sidebar"
           class="fixed inset-y-0 left-0 z-50 flex w-72 -translate-x-full flex-col border-r border-slate-200 bg-white transition-transform duration-200 ease-out lg:translate-x-0">
        <div class="flex h-16 items-center gap-3 border-b border-slate-100 px-6">
            <span class="grid size-10 place-items-center rounded-xl bg-gradient-to-br from-rose-500 to-red-600 text-white shadow-sm shadow-rose-500/30">
                <svg viewBox="0 0 24 24" fill="currentColor" class="size-5"><path d="M12 2.69s5.42 4.94 8.63 8.79A5.36 5.36 0 0 1 19.36 19a5.33 5.33 0 0 1-7.36-.3 5.33 5.33 0 0 1-7.36.3A5.36 5.36 0 0 1 3.37 11.5C6.58 7.63 12 2.69 12 2.69Z"/></svg>
            </span>
            <div class="leading-tight">
                <p class="text-[15px] font-bold tracking-tight text-slate-900">LifeDrop</p>
                <p class="text-[11px] font-medium uppercase tracking-wider text-slate-400">Donor Management</p>
            </div>
            <button type="button" data-sidebar-close class="ml-auto rounded-lg p-1.5 text-slate-400 hover:bg-slate-100 hover:text-slate-600 lg:hidden" aria-label="Close menu">
                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" class="size-5"><path stroke-linecap="round" d="M6 18 18 6M6 6l12 12"/></svg>
            </button>
        </div>

        <nav class="flex-1 space-y-1 overflow-y-auto p-4">
            <p class="px-3 pb-2 pt-1 text-[11px] font-semibold uppercase tracking-wider text-slate-400">Menu</p>

            @php
                $nav = [
                    ['active' => 'dashboard', 'url' => route('dashboard'), 'label' => 'Dashboard', 'icon' => '<path stroke-linecap="round" stroke-linejoin="round" d="M3.75 6A2.25 2.25 0 0 1 6 3.75h2.25A2.25 2.25 0 0 1 10.5 6v2.25a2.25 2.25 0 0 1-2.25 2.25H6a2.25 2.25 0 0 1-2.25-2.25V6ZM13.5 6a2.25 2.25 0 0 1 2.25-2.25H18A2.25 2.25 0 0 1 20.25 6v2.25A2.25 2.25 0 0 1 18 10.5h-2.25a2.25 2.25 0 0 1-2.25-2.25V6ZM3.75 15.75A2.25 2.25 0 0 1 6 13.5h2.25a2.25 2.25 0 0 1 2.25 2.25V18a2.25 2.25 0 0 1-2.25 2.25H6A2.25 2.25 0 0 1 3.75 18v-2.25ZM13.5 15.75a2.25 2.25 0 0 1 2.25-2.25H18a2.25 2.25 0 0 1 2.25 2.25V18A2.25 2.25 0 0 1 18 20.25h-2.25A2.25 2.25 0 0 1 13.5 18v-2.25Z"/>'],
                    ['active' => 'donors.*', 'url' => route('donors.index'), 'label' => 'Donors', 'icon' => '<path stroke-linecap="round" stroke-linejoin="round" d="M15 19.128a9.38 9.38 0 0 0 2.625.372 9.337 9.337 0 0 0 4.121-.952 4.125 4.125 0 0 0-7.533-2.493M15 19.128v-.003c0-1.113-.285-2.16-.786-3.07M15 19.128v.106A12.318 12.318 0 0 1 8.624 21c-2.331 0-4.512-.645-6.374-1.766l-.001-.109a6.375 6.375 0 0 1 11.964-3.07M12 6.375a3.375 3.375 0 1 1-6.75 0 3.375 3.375 0 0 1 6.75 0Zm8.25 2.25a2.625 2.625 0 1 1-5.25 0 2.625 2.625 0 0 1 5.25 0Z"/>'],
                    ['active' => 'donations.*', 'url' => route('donations.index'), 'label' => 'Donations', 'icon' => '<path stroke-linecap="round" stroke-linejoin="round" d="M21 8.25c0-2.485-2.099-4.5-4.688-4.5-1.935 0-3.597 1.126-4.312 2.733-.715-1.607-2.377-2.733-4.313-2.733C5.1 3.75 3 5.765 3 8.25c0 7.22 9 12 9 12s9-4.78 9-12Z"/>'],
                    ['active' => 'requests.*', 'url' => route('requests.index'), 'label' => 'Blood Requests', 'icon' => '<path stroke-linecap="round" stroke-linejoin="round" d="M12 6.042A8.967 8.967 0 0 0 6 3.75c-1.052 0-2.062.18-3 .512v14.25A8.987 8.987 0 0 1 6 18c2.305 0 4.408.867 6 2.292m0-14.25a8.966 8.966 0 0 1 6-2.292c1.052 0 2.062.18 3 .512v14.25A8.987 8.987 0 0 0 18 18a8.967 8.967 0 0 0-6 2.292m0-14.25v14.25"/>'],
                ];
            @endphp

            @foreach ($nav as $item)
                <a href="{{ $item['url'] }}"
                   @class([
                       'group flex items-center gap-3 rounded-xl px-3 py-2.5 text-sm font-medium transition',
                       'bg-rose-600 text-white shadow-sm shadow-rose-600/30' => request()->routeIs($item['active']),
                       'text-slate-600 hover:bg-slate-100 hover:text-slate-900' => ! request()->routeIs($item['active']),
                   ])>
                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.75" class="size-5 shrink-0" aria-hidden="true">{!! $item['icon'] !!}</svg>
                    {{ $item['label'] }}
                </a>
            @endforeach
        </nav>

        <div class="p-4">
            <div class="rounded-2xl bg-gradient-to-br from-rose-50 to-amber-50 p-4 ring-1 ring-rose-100">
                <div class="mb-2 grid size-8 place-items-center rounded-lg bg-white text-rose-600 shadow-sm">
                    <svg viewBox="0 0 24 24" fill="currentColor" class="size-4"><path d="M12 2.69s5.42 4.94 8.63 8.79A5.36 5.36 0 0 1 19.36 19a5.33 5.33 0 0 1-7.36-.3 5.33 5.33 0 0 1-7.36.3A5.36 5.36 0 0 1 3.37 11.5C6.58 7.63 12 2.69 12 2.69Z"/></svg>
                </div>
                <p class="text-sm font-semibold text-slate-800">One donation, up to 3 lives</p>
                <p class="mt-1 text-xs leading-relaxed text-slate-500">Eligible donors can give whole blood every {{ \App\Models\Donor::DEFERRAL_DAYS }} days.</p>
            </div>
        </div>
    </aside>

    <div class="lg:pl-72">
        <header class="sticky top-0 z-30 border-b border-slate-200 bg-slate-50/85 backdrop-blur">
            <div class="flex h-16 items-center gap-4 px-4 sm:px-6 lg:px-8">
                <button type="button" data-sidebar-open class="rounded-lg p-2 text-slate-500 hover:bg-white hover:text-slate-800 lg:hidden" aria-label="Open menu">
                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" class="size-5"><path stroke-linecap="round" d="M3.75 6.75h16.5M3.75 12h16.5m-16.5 5.25h16.5"/></svg>
                </button>

                <div class="min-w-0 flex-1">
                    <h1 class="truncate text-lg font-bold tracking-tight text-slate-900">@yield('title', 'Dashboard')</h1>
                    <p class="hidden truncate text-xs text-slate-500 sm:block">@yield('subtitle', 'Blood donor management at a glance')</p>
                </div>

                <div class="flex items-center gap-2 sm:gap-3">
                    @yield('actions')
                </div>
            </div>
        </header>

        <main class="mx-auto w-full max-w-7xl px-4 py-6 sm:px-6 sm:py-8 lg:px-8">
            @include('partials.flash')
            @yield('content')
        </main>

        <footer class="border-t border-slate-200 px-4 py-6 text-center text-xs text-slate-400 sm:px-6 lg:px-8">
            LifeDrop · Blood Donor Management System
        </footer>
    </div>
</div>

@stack('scripts')
</body>
</html>
