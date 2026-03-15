<aside class="fixed left-0 top-0 h-full w-64 sidebar-glass flex flex-col z-50">
    <!-- Logo / Brand -->
    <div class="p-8">
        <div class="bg-primary/10 w-12 h-12 rounded-2xl flex items-center justify-center mb-4">
            <h1 class="text-2xl font-black text-primary">U</h1>
        </div>
        <h1 class="text-xl font-extrabold text-gray-800 tracking-tight">UCPRUK <span class="text-primary">DB</span></h1>
        <p class="text-xs text-gray-500 font-bold uppercase tracking-widest mt-1">Non-Profit Portal</p>
    </div>

    <!-- Navigation -->
    <nav class="flex-1 py-4 overflow-y-auto">
        <a href="{{ route('dashboard') }}"
            class="{{ request()->routeIs('dashboard') ? 'nav-item nav-item-active' : 'nav-item' }}">
            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                    d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6" />
            </svg>
            <span>Dashboard</span>
        </a>

        <a href="{{ route('sebaran-data') }}"
            class="{{ request()->routeIs('sebaran-data') ? 'nav-item nav-item-active' : 'nav-item' }}">
            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                    d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z" />
            </svg>
            <span>Sebaran Data</span>
        </a>

        <div class="px-8 mt-8 mb-2">
            <p class="text-[10px] font-black text-gray-400 uppercase tracking-[0.2em]">Management</p>
        </div>

        <a href="{{ route('client-bio.create') }}"
            class="{{ request()->routeIs('client-bio.create') ? 'nav-item nav-item-active' : 'nav-item' }}">
            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4" />
            </svg>
            <span>Input Klien</span>
        </a>

        <a href="{{ route('client-bio.index') }}"
            class="{{ request()->routeIs('client-bio.index', 'client-bio.show', 'client-bio.edit') ? 'nav-item nav-item-active' : 'nav-item' }}">
            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                    d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0z" />
            </svg>
            <span>Database Klien</span>
        </a>

        <a href="{{ route('client-assessments.index') }}"
            class="{{ request()->routeIs('client-assessments.*') ? 'nav-item nav-item-active' : 'nav-item' }}">
            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                    d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
            </svg>
            <span>Asesmen Klien</span>
        </a>

        <a href="{{ route('wheelchairs.index') }}"
            class="{{ request()->routeIs('wheelchairs.*') ? 'nav-item nav-item-active' : 'nav-item' }}">
            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                    d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 012-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10" />
            </svg>
            <span>Inventaris Kursi Roda</span>
        </a>

        <a href="{{ route('export.client.index') }}"
            class="{{ request()->routeIs('export.client.*') ? 'nav-item nav-item-active' : 'nav-item' }}">
            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                    d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
            </svg>
            <span>Ekspor Klien</span>
        </a>

        @if(auth()->user()->isAdmin())
            <div class="px-8 mt-8 mb-2">
                <p class="text-[10px] font-black text-gray-400 uppercase tracking-[0.2em]">System Admin</p>
            </div>

            <a href="{{ route('users.index') }}"
                class="{{ request()->routeIs('users.*') ? 'nav-item nav-item-active' : 'nav-item' }}">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                        d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z" />
                </svg>
                <span>Kelola User</span>
            </a>

            <a href="{{ route('data-management.index') }}"
                class="{{ request()->routeIs('data-management.*') ? 'nav-item nav-item-active' : 'nav-item' }}">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                        d="M4 7v10c0 2.21 3.582 4 8 4s8-1.79 8-4V7M4 7c0 2.21 3.582 4 8 4s8-1.79 8-4M4 7c0-2.21 3.582-4 8-4s8 1.79 8 4m0 5c0 2.21-3.582 4-8 4s-8-1.79-8-4" />
                </svg>
                <span>Manajemen Data</span>
            </a>
        @endif
    </nav>

    <!-- Footer -->
    <div class="p-6 m-4 glass bg-white/60">
        <div class="flex items-center gap-3">
            <div class="w-2 h-2 rounded-full bg-green-500 animate-pulse"></div>
            <p class="text-[10px] font-bold text-gray-600 uppercase tracking-tight">System Online</p>
        </div>
    </div>
</aside>