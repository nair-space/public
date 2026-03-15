@extends('layouts.app')

@section('page-title', 'Manajemen Data')

@section('content')
    <div class="mb-8 px-2">
        <h2 class="text-2xl font-black text-gray-800 tracking-tight">Pusat Manajemen Data</h2>
        <p class="text-sm text-gray-500 font-medium">Ekspor dan impor data dalam format CSV atau SQL (Cadangan Database)</p>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-2 gap-8">
        <!-- CSV Management -->
        <div class="glass p-8">
            <div class="flex items-center gap-4 mb-8">
                <div class="w-12 h-12 rounded-2xl bg-blue-500/10 flex items-center justify-center text-blue-600">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M9 17v-2a4 4 0 014-4h4m-4 4l4-4m-4 4l-4-4m3 5v3a2 2 0 01-2 2H5a2 2 0 01-2-2V5a2 2 0 012-2h9l5 5v11a2 2 0 01-2 2h-1" />
                    </svg>
                </div>
                <div>
                    <h3 class="text-xl font-bold text-gray-800">Manajemen CSV</h3>
                    <p class="text-xs text-gray-500 font-bold uppercase tracking-widest">Excel Compatible</p>
                </div>
            </div>

            <div class="space-y-8">
                <!-- Export Section -->
                <div class="p-6 rounded-2xl bg-white/40 border border-white/20">
                    <h4 class="text-sm font-black text-gray-800 uppercase tracking-wider mb-4">Ekspor Data</h4>
                    <form action="{{ route('data-management.export.csv') }}" method="GET" class="flex gap-3">
                        <select name="table" class="input flex-1">
                            <option value="client_bio">Data Klien (Biodata)</option>
                            <option value="wheelchair_client">Data Kursi Roda</option>
                        </select>
                        <button type="submit" class="btn-primary px-6">Download CSV</button>
                    </form>
                </div>

                <!-- Import Section -->
                <div class="p-6 rounded-2xl bg-white/40 border border-white/20">
                    <h4 class="text-sm font-black text-gray-800 uppercase tracking-wider mb-4">Impor Data</h4>
                    <form action="{{ route('data-management.import.csv') }}" method="POST" enctype="multipart/form-data"
                        class="space-y-4">
                        @csrf
                        <select name="table" class="input w-full" required>
                            <option value="client_bio">Data Klien (Biodata)</option>
                            <option value="wheelchair_client">Data Kursi Roda</option>
                        </select>
                        <div class="relative">
                            <input type="file" name="file" accept=".csv" class="input py-2 pt-3" required>
                        </div>
                        <button type="submit" class="btn-outline w-full py-3"
                            onclick="return confirm('Peringatan: Data yang ada akan diperbarui jika ID sama. Lanjutkan?')">Unggah
                            & Proses</button>
                    </form>
                </div>
            </div>
        </div>

        <!-- SQL Management -->
        <div class="glass p-8">
            <div class="flex items-center gap-4 mb-8">
                <div class="w-12 h-12 rounded-2xl bg-purple-500/10 flex items-center justify-center text-purple-600">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M4 7v10c0 2.21 3.582 4 8 4s8-1.79 8-4V7M4 7c0 2.21 3.582 4 8 4s8-1.79 8-4M4 7c0-2.21 3.582-4 8-4s8 1.79 8 4m0 5c0 2.21-3.582 4-8 4s-8-1.79-8-4" />
                    </svg>
                </div>
                <div>
                    <h3 class="text-xl font-bold text-gray-800">Database SQL</h3>
                    <p class="text-xs text-gray-500 font-bold uppercase tracking-widest">Full Backup / Restore</p>
                </div>
            </div>

            <div class="space-y-8">
                <!-- SQL Backup -->
                <div class="p-6 rounded-2xl bg-white/40 border border-white/20">
                    <h4 class="text-sm font-black text-gray-800 uppercase tracking-wider mb-4">Cadangkan Database</h4>
                    <p class="text-sm text-gray-500 mb-6 font-medium">Buat salinan lengkap database dalam format .sql untuk
                        keamanan.</p>
                    <a href="{{ route('data-management.export.sql') }}"
                        class="btn-primary w-full block text-center py-4">Download SQL Backup</a>
                </div>

                <!-- SQL Restore -->
                <div class="p-6 rounded-2xl bg-white/40 border border-white/20">
                    <h4 class="text-sm font-black text-gray-800 uppercase tracking-wider mb-4">Pulihkan Database</h4>
                    <form action="{{ route('data-management.import.sql') }}" method="POST" enctype="multipart/form-data"
                        class="space-y-4">
                        @csrf
                        <div class="relative">
                            <input type="file" name="file" accept=".sql" class="input py-2 pt-3" required>
                        </div>
                        <button type="submit"
                            class="btn-outline border-red-200 text-red-600 hover:bg-red-500 hover:text-white w-full py-3 font-black"
                            onclick="return confirm('PERINGATAN KRITIS: Seluruh database akan diganti dengan file ini. Tindakan ini tidak dapat dibatalkan. Lanjutkan?')">Restore
                            Database (.sql)</button>
                    </form>
                </div>
            </div>
        </div>
    </div>
@endsection