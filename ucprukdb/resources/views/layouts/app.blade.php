<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>{{ $title ?? 'UCPRUK Database' }}</title>

    <!-- Google Fonts -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800&display=swap" rel="stylesheet">

    @vite(['resources/css/app.css', 'resources/js/app.js'])

    @stack('styles')
</head>

<body class="antialiased font-sans">
    <div class="flex min-h-screen">
        <!-- Sidebar -->
        @include('components.sidebar')

        <!-- Main Content -->
        <main class="flex-1 ml-64 min-h-screen">
            <!-- Top Bar (Glass) -->
            <header
                class="sticky top-0 z-40 px-8 py-4 bg-white/40 backdrop-blur-md border-b border-white/20 flex justify-between items-center">
                <h1 class="text-xl font-bold text-gray-800">@yield('page-title', 'Dashboard')</h1>
                <div class="flex items-center gap-6">
                    <div class="flex items-center gap-3">
                        <div
                            class="w-8 h-8 rounded-full bg-primary flex items-center justify-center text-white font-bold text-sm">
                            {{ substr(auth()->user()->nama_lengkap, 0, 1) }}
                        </div>
                        <span class="text-gray-700 font-semibold">{{ auth()->user()->nama_lengkap }}</span>
                    </div>
                    <form action="{{ route('logout') }}" method="POST" class="inline">
                        @csrf
                        <button type="submit"
                            class="btn-outline text-sm px-4 py-2 hover:bg-red-50 hover:text-red-600 transition-colors">Keluar</button>
                    </form>
                </div>
            </header>

            <!-- Page Content -->
            <div class="p-8">
                <!-- Flash Messages (Glass) -->
                @if(session('success'))
                    <div class="glass p-4 mb-6 border-l-4 border-green-500 text-green-700 font-medium">
                        {{ session('success') }}
                    </div>
                @endif

                @if(session('error'))
                    <div class="glass p-4 mb-6 border-l-4 border-red-500 text-red-700 font-medium">
                        {{ session('error') }}
                    </div>
                @endif

                @yield('content')
            </div>
        </main>
    </div>

    <!-- Global Custom Confirmation Modal -->
    <div id="confirmModal" class="modal-overlay">
        <div class="modal-content text-center">
            <div class="modal-icon-container modal-icon-red">
                <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                        d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.268 17c-.77 1.333.192 3 1.732 3z" />
                </svg>
            </div>
            <h3 class="text-xl font-black text-gray-800 mb-2" id="modalTitle">Konfirmasi Hapus</h3>
            <p class="text-sm text-gray-500 font-medium mb-8 leading-relaxed" id="modalMessage">
                Apakah Anda yakin ingin menghapus data ini? Tindakan ini tidak dapat dibatalkan.
            </p>
            <div class="flex gap-3">
                <button onclick="closeConfirmModal()" class="btn-outline flex-1 py-3 text-sm font-bold">Batal</button>
                <button id="confirmBtn"
                    class="btn-primary bg-red-500 hover:bg-red-600 border-none flex-1 py-3 text-sm">Hapus
                    Sekarang</button>
            </div>
        </div>
    </div>

    <script>
        let currentForm = null;

        window.confirmAction = function (form, title = 'Konfirmasi Hapus', message = 'Apakah Anda yakin ingin menghapus data ini?') {
            event.preventDefault();
            currentForm = form;
            document.getElementById('modalTitle').innerText = title;
            document.getElementById('modalMessage').innerText = message;
            document.getElementById('confirmModal').classList.add('active');
            return false;
        }

        window.closeConfirmModal = function () {
            document.getElementById('confirmModal').classList.remove('active');
            currentForm = null;
        }

        document.getElementById('confirmBtn').addEventListener('click', function () {
            if (currentForm) {
                currentForm.submit();
            }
            closeConfirmModal();
        });

        // Close on ESC
        window.addEventListener('keydown', function (event) {
            if (event.key === 'Escape') closeConfirmModal();
        });
    </script>

    @stack('scripts')
</body>

</html>