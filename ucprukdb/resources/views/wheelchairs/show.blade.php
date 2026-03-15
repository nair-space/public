@extends('layouts.app')

@section('page-title', 'Detail Kursi Roda')

@section('content')
    <div class="mb-8 flex items-center justify-between gap-4">
        <div class="flex items-center gap-4">
            <a href="{{ route('wheelchairs.index') }}"
                class="p-2 rounded-xl bg-white/50 border border-white hover:bg-white transition-all text-gray-500 hover:text-primary">
                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18" />
                </svg>
            </a>
            <div>
                <h2 class="text-3xl font-black text-gray-800 tracking-tight">Detail Kursi Roda</h2>
                <p class="text-sm text-gray-500 font-medium italic">ID: {{ $wheelchair->kursiroda_id }}</p>
            </div>
        </div>
        <div class="flex gap-3">
            <a href="{{ route('wheelchairs.edit', $wheelchair->kursiroda_id) }}"
                class="btn-primary flex items-center gap-2">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                        d="M15.232 5.232l3.536 3.536m-2.036-5.036a2.5 2.5 0 113.536 3.536L6.5 21.036H3v-3.572L16.732 3.732z" />
                </svg>
                Edit Data
            </a>
        </div>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
        <!-- Sidebar Info: Client Assignment -->
        <div class="lg:col-span-1 space-y-8">
            <div class="glass p-8">
                <div class="flex items-center gap-4 mb-6">
                    <div class="w-12 h-12 rounded-2xl bg-primary/10 flex items-center justify-center text-primary">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z" />
                        </svg>
                    </div>
                    <div>
                        <h3 class="text-lg font-black text-gray-800">Assignee</h3>
                        <p class="text-xs text-gray-400 font-bold uppercase tracking-wider">Klien Pengguna</p>
                    </div>
                </div>

                <div class="space-y-4">
                    <div class="p-4 rounded-2xl bg-white/40 border border-white/60">
                        <p class="text-[10px] text-gray-400 font-black uppercase mb-1">Nama Lengkap</p>
                        <p class="font-bold text-gray-800">{{ $wheelchair->nama_lengkap }}</p>
                    </div>
                    <div class="p-4 rounded-2xl bg-white/40 border border-white/60">
                        <p class="text-[10px] text-gray-400 font-black uppercase mb-1">NIK</p>
                        <p class="font-mono text-sm font-bold text-gray-700 tracking-tighter">{{ $wheelchair->nik }}</p>
                    </div>
                    <div class="p-4 rounded-2xl bg-white/40 border border-white/60">
                        <p class="text-[10px] text-gray-400 font-black uppercase mb-1">ID Klien</p>
                        <p class="font-mono text-sm font-bold text-gray-500">{{ $wheelchair->client_id }}</p>
                    </div>
                </div>

                <a href="{{ route('client-bio.show', $wheelchair->client_id) }}"
                    class="mt-6 w-full py-4 rounded-2xl bg-gray-800 text-white text-sm font-bold flex items-center justify-center gap-2 hover:bg-gray-900 transition-all group">
                    Lihat Biodata Klien
                    <svg class="w-4 h-4 group-hover:translate-x-1 transition-transform" fill="none" stroke="currentColor"
                        viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M14 5l7 7m0 0l-7 7m7-7H3" />
                    </svg>
                </a>
            </div>
        </div>

        <!-- Main Info: Wheelchair Specs -->
        <div class="lg:col-span-2 space-y-8">
            <div class="glass p-10">
                <div class="flex items-center gap-4 mb-10">
                    <div class="w-14 h-14 rounded-2xl bg-yellow-400/10 flex items-center justify-center text-yellow-600">
                        <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M9 17V7m0 10a2 2 0 01-2 2H5a2 2 0 01-2-2V7a2 2 0 012-2h2a2 2 0 012 2m0 10a2 2 0 002 2h2a2 2 0 002-2M9 7a2 2 0 012-2h2a2 2 0 012 2m0 10V7m0 10a2 2 0 012 2h2a2 2 0 012-2V7a2 2 0 01-2-2h-2a2 2 0 01-2 2" />
                        </svg>
                    </div>
                    <div>
                        <h3 class="text-2xl font-black text-gray-800">Spesifikasi Unit</h3>
                        <p class="text-sm text-gray-400 font-medium">Informasi teknis dan kondisi peralatan</p>
                    </div>
                </div>

                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <div class="col-span-1 md:col-span-2 p-6 rounded-3xl bg-blue-50/50 border border-blue-100">
                        <p class="text-xs text-blue-400 font-black uppercase tracking-wider mb-2">Jenis Kursi Roda</p>
                        <p class="text-2xl font-black text-blue-900">{{ $wheelchair->jenis_kursiroda }}</p>
                    </div>

                    <div class="p-6 rounded-3xl bg-white/40 border border-white/60">
                        <p class="text-xs text-gray-400 font-black uppercase tracking-wider mb-2">Terakhir Diperbarui</p>
                        <p class="text-lg font-bold text-gray-800">{{ $wheelchair->updated_at->format('d M Y, H:i') }}</p>
                    </div>

                    <div class="p-6 rounded-3xl bg-white/40 border border-white/60">
                        <p class="text-xs text-gray-400 font-black uppercase tracking-wider mb-2">Tanggal Input</p>
                        <p class="text-lg font-bold text-gray-800">{{ $wheelchair->created_at->format('d M Y') }}</p>
                    </div>
                </div>

                <div class="mt-10 grid grid-cols-1 md:grid-cols-2 gap-4">
                    <div class="flex items-center gap-3 p-4 rounded-2xl bg-green-50/50 border border-green-100">
                        <div class="w-8 h-8 rounded-full bg-green-500 flex items-center justify-center text-white">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="3" d="M5 13l4 4L19 7" />
                            </svg>
                        </div>
                        <span class="text-sm font-bold text-green-700">Unit Terdaftar</span>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection