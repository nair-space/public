@extends('layouts.app')

@section('page-title', 'Detail Asesmen Klien')

@section('content')
    <div class="mb-8 flex items-center justify-between gap-4">
        <div class="flex items-center gap-4">
            <a href="{{ route('client-assessments.index') }}"
                class="p-3 rounded-2xl bg-white/60 text-gray-500 hover:text-primary transition-all glass">
                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18" />
                </svg>
            </a>
            <div>
                <h2 class="text-2xl font-black text-gray-800 tracking-tight">Detail Asesmen</h2>
                <p class="text-sm text-gray-500 font-medium">ID: {{ $assessment->client_basic_assessment_id }}</p>
            </div>
        </div>
        <a href="{{ route('client-assessments.edit', $assessment->client_basic_assessment_id) }}"
            class="btn-primary flex items-center gap-2">
            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                    d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z" />
            </svg>
            <span>Edit Asesmen</span>
        </a>
    </div>

    <div class="space-y-8 pb-12">
        <!-- SECTION 1: Klien & Diagnosis -->
        <div class="glass p-8">
            <div class="flex items-center gap-4 mb-6">
                <div class="w-10 h-10 rounded-xl bg-primary/10 flex items-center justify-center text-primary">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z" />
                    </svg>
                </div>
                <h3 class="text-lg font-bold text-gray-800">Identitas & Diagnosis</h3>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-2 gap-8">
                <div>
                    <p class="text-[10px] font-black text-gray-400 uppercase tracking-widest mb-1">Nama Klien</p>
                    <p class="text-lg font-bold text-gray-800">{{ $assessment->client->nama_lengkap ?? 'Unknown' }}</p>
                    <p class="text-xs text-gray-500 font-bold mt-0.5">ID: {{ $assessment->client_id }}</p>
                </div>

                <div>
                    <p class="text-[10px] font-black text-gray-400 uppercase tracking-widest mb-1">Service ID</p>
                    <p class="text-lg font-bold text-gray-800">{{ $assessment->client_service_id ?: '-' }}</p>
                </div>

                <div class="md:col-span-2">
                    <p class="text-[10px] font-black text-gray-400 uppercase tracking-widest mb-1">Diagnosis</p>
                    <div
                        class="p-4 rounded-xl bg-white/40 border border-white/20 text-gray-700 leading-relaxed font-medium">
                        {{ $assessment->diagnosis ?: 'Tidak ada diagnosis tercatat.' }}
                    </div>
                </div>
            </div>
        </div>

        <!-- SECTION 2: Penggunaan Peralatan -->
        <div class="glass p-8">
            <div class="flex items-center gap-4 mb-6">
                <div class="w-10 h-10 rounded-xl bg-blue-500/10 flex items-center justify-center text-blue-600">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 012-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10" />
                    </svg>
                </div>
                <h3 class="text-lg font-bold text-gray-800">Penggunaan Peralatan Saat Ini</h3>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-3 gap-8">
                <div>
                    <p class="text-[10px] font-black text-gray-400 uppercase tracking-widest mb-1">Di Sekolah</p>
                    <p class="text-base font-bold text-gray-800">{{ $assessment->equipment_school_use ?: '-' }}</p>
                </div>
                <div>
                    <p class="text-[10px] font-black text-gray-400 uppercase tracking-widest mb-1">Di Tempat Kerja</p>
                    <p class="text-base font-bold text-gray-800">{{ $assessment->equipment_workplace_use ?: '-' }}</p>
                </div>
                <div>
                    <p class="text-[10px] font-black text-gray-400 uppercase tracking-widest mb-1">Di Rumah</p>
                    <p class="text-base font-bold text-gray-800">{{ $assessment->equipment_home_use ?: '-' }}</p>
                </div>
                <div>
                    <p class="text-[10px] font-black text-gray-400 uppercase tracking-widest mb-1">Di Lingkungan</p>
                    <p class="text-base font-bold text-gray-800">{{ $assessment->equipment_neighborhood_use ?: '-' }}</p>
                </div>
                <div>
                    <p class="text-[10px] font-black text-gray-400 uppercase tracking-widest mb-1">Metode Transfer</p>
                    <p class="text-base font-bold text-gray-800">{{ $assessment->transfer ?: '-' }}</p>
                </div>
                <div>
                    <p class="text-[10px] font-black text-gray-400 uppercase tracking-widest mb-1">Kondisi Alat Lama</p>
                    <p class="text-base font-bold text-gray-800">{{ $assessment->old_equipment_goodness ?: '-' }}</p>
                </div>
            </div>
        </div>

        <!-- SECTION 3: Pengukuran Fisik -->
        <div class="glass p-8">
            <div class="flex items-center gap-4 mb-6">
                <div class="w-10 h-10 rounded-xl bg-purple-500/10 flex items-center justify-center text-purple-600">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M12 18h.01M8 21h8a2 2 0 002-2V5a2 2 0 00-2-2H8a2 2 0 00-2 2v14a2 2 0 002 2z" />
                    </svg>
                </div>
                <h3 class="text-lg font-bold text-gray-800">Data Pengukuran (cm)</h3>
            </div>

            <div class="grid grid-cols-2 md:grid-cols-4 gap-8">
                <div>
                    <p class="text-[10px] font-black text-gray-400 uppercase tracking-widest mb-1">Lebar Panggul</p>
                    <p class="text-xl font-black text-primary">{{ $assessment->pelvic_width ?: '0' }} <span
                            class="text-xs text-gray-400">cm</span></p>
                </div>
                <div>
                    <p class="text-[10px] font-black text-gray-400 uppercase tracking-widest mb-1">Lengan Atas (Kiri)</p>
                    <p class="text-xl font-black text-primary">{{ $assessment->left_upper_limb ?: '0' }} <span
                            class="text-xs text-gray-400">cm</span></p>
                </div>
                <div>
                    <p class="text-[10px] font-black text-gray-400 uppercase tracking-widest mb-1">Lengan Atas (Kanan)</p>
                    <p class="text-xl font-black text-primary">{{ $assessment->right_upper_limb ?: '0' }} <span
                            class="text-xs text-gray-400">cm</span></p>
                </div>
                <div>
                    <p class="text-[10px] font-black text-gray-400 uppercase tracking-widest mb-1">Lebar Kursi</p>
                    <p class="text-xl font-black text-blue-600">{{ $assessment->seat_width ?: '0' }} <span
                            class="text-xs text-gray-400">cm</span></p>
                </div>
                <div>
                    <p class="text-[10px] font-black text-gray-400 uppercase tracking-widest mb-1">Panjang Kursi</p>
                    <p class="text-xl font-black text-blue-600">{{ $assessment->seat_length ?: '0' }} <span
                            class="text-xs text-gray-400">cm</span></p>
                </div>
            </div>
        </div>

        <!-- SECTION 4: Spesifikasi -->
        <div class="glass p-8">
            <div class="flex items-center gap-4 mb-6">
                <div class="w-10 h-10 rounded-xl bg-accent/20 flex items-center justify-center text-yellow-700">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M13 10V3L4 14h7v7l9-11h-7z" />
                    </svg>
                </div>
                <h3 class="text-lg font-bold text-gray-800">Spesifikasi Tambahan</h3>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-3 gap-8">
                <div>
                    <p class="text-[10px] font-black text-gray-400 uppercase tracking-widest mb-1">Tipe Cushion</p>
                    <p class="text-base font-bold text-gray-800">{{ $assessment->cushion_type ?: '-' }}</p>
                </div>
                <div>
                    <p class="text-[10px] font-black text-gray-400 uppercase tracking-widest mb-1">Kebutuhan Kursi Aktif</p>
                    <span
                        class="px-3 py-1 rounded-full text-[10px] font-black uppercase tracking-wider {{ $assessment->active == 'ya' ? 'bg-green-100 text-green-700' : 'bg-gray-100 text-gray-400' }}">
                        {{ $assessment->active == 'ya' ? 'Ya' : 'Tidak' }}
                    </span>
                </div>
                <div>
                    <p class="text-[10px] font-black text-gray-400 uppercase tracking-widest mb-1">Kebutuhan Stroller</p>
                    <span
                        class="px-3 py-1 rounded-full text-[10px] font-black uppercase tracking-wider {{ $assessment->stroller == 'ya' ? 'bg-green-100 text-green-700' : 'bg-gray-100 text-gray-400' }}">
                        {{ $assessment->stroller == 'ya' ? 'Ya' : 'Tidak' }}
                    </span>
                </div>
                <div class="md:col-span-3">
                    <p class="text-[10px] font-black text-gray-400 uppercase tracking-widest mb-1">Deskripsi & Alasan
                        Tambahan</p>
                    <div class="p-4 rounded-xl bg-white/40 border border-white/20 text-gray-700 font-medium">
                        {{ $assessment->sp_description ?: '-' }}
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection