@extends('layouts.app')

@section('page-title', 'Detail Klien')

@section('content')
    <div class="mb-8 flex items-center justify-between gap-4">
        <div class="flex items-center gap-4">
            <a href="{{ route('client-bio.index') }}"
                class="p-2 rounded-xl bg-white/50 border border-white hover:bg-white transition-all text-gray-500 hover:text-primary">
                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18" />
                </svg>
            </a>
            <div>
                <h2 class="text-3xl font-black text-gray-800 tracking-tight">Profil Biodata Klien</h2>
                <p class="text-sm text-gray-500 font-mono font-medium italic">{{ $client->client_id }}</p>
            </div>
        </div>
        <div class="flex gap-3">
            <a href="{{ route('client-bio.edit', $client->client_id) }}" class="btn-primary flex items-center gap-2">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                        d="M15.232 5.232l3.536 3.536m-2.036-5.036a2.5 2.5 0 113.536 3.536L6.5 21.036H3v-3.572L16.732 3.732z" />
                </svg>
                Edit Biodata
            </a>
        </div>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
        <!-- Sidebar: Profil Ringkas -->
        <div class="lg:col-span-1 space-y-8">
            <div class="glass p-8 text-center">
                <div
                    class="w-[150px] h-[150px] rounded-3xl bg-primary/10 flex items-center justify-center text-primary mx-auto mb-6 border-4 border-white shadow-lg overflow-hidden">
                    @if($client->photo_path)
                        <img src="{{ $client->photo_url }}" alt="{{ $client->nama_lengkap }}"
                            class="w-full h-full object-cover">
                    @else
                        <svg class="w-20 h-20" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z" />
                        </svg>
                    @endif
                </div>
                <h3 class="text-2xl font-black text-gray-800 leading-tight mb-1">{{ $client->nama_lengkap }}</h3>
                <p class="text-sm text-gray-400 font-bold mb-6 italic">"{{ $client->nama_panggilan ?? '-' }}"</p>

                <div class="flex flex-wrap justify-center gap-2 mb-8">
                    <span
                        class="px-3 py-1 rounded-full bg-blue-100 text-blue-700 text-[10px] font-black uppercase tracking-wider">
                        {{ $client->jenis_kelamin }}
                    </span>
                    <span
                        class="px-3 py-1 rounded-full bg-purple-100 text-purple-700 text-[10px] font-black uppercase tracking-wider">
                        {{ $client->jenis_disabilitas }}
                    </span>
                </div>

                <div class="space-y-4 text-left">
                    <div class="p-4 rounded-2xl bg-white/40 border border-white/60">
                        <p class="text-[10px] text-gray-400 font-black uppercase mb-1 tracking-widest">NIK</p>
                        <p class="font-mono text-sm font-bold text-gray-700 tracking-tighter">{{ $client->nik }}</p>
                    </div>
                </div>
            </div>

            <!-- Checklist Dokumen -->
            <div class="glass p-8">
                <h4 class="text-lg font-black text-gray-800 mb-6 flex items-center gap-2">
                    <svg class="w-5 h-5 text-primary" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
                    </svg>
                    Checklist Dokumen
                </h4>
                <div class="space-y-3">
                    @php
                        $docs = [
                            'Pas Foto' => $client->ada_foto,
                            'Salinan KK' => $client->salinan_kk,
                            'Salinan KTP' => $client->salinan_ktp,
                            'Tagihan Listrik' => $client->salinan_tagihanlistrik,
                            'Slip Gaji' => $client->salinan_slipgaji,
                        ];
                    @endphp
                    @foreach($docs as $name => $status)
                        <div class="flex items-center justify-between p-3 rounded-xl bg-white/30 border border-white/50">
                            <span class="text-sm font-bold text-gray-600">{{ $name }}</span>
                            @if(strtolower($status) === 'ya')
                                <span class="bg-green-500/10 text-green-600 p-1 rounded-lg">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="3" d="M5 13l4 4L19 7" />
                                    </svg>
                                </span>
                            @else
                                <span class="bg-red-500/10 text-red-600 p-1 rounded-lg">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="3"
                                            d="M6 18L18 6M6 6l12 12" />
                                    </svg>
                                </span>
                            @endif
                        </div>
                    @endforeach
                </div>
            </div>
        </div>

        <!-- Main Info: Detail Data -->
        <div class="lg:col-span-2 space-y-8">
            <!-- Data Pribadi & Alamat -->
            <div class="glass p-10">
                <h4 class="text-xl font-black text-gray-800 mb-8 pb-4 border-b border-gray-100 flex items-center gap-3">
                    <span class="w-2 h-8 bg-primary rounded-full"></span>
                    Informasi Pribadi & Identitas
                </h4>
                <div class="grid grid-cols-1 md:grid-cols-2 gap-8">
                    <div>
                        <p class="text-[10px] text-gray-400 font-black uppercase mb-1 tracking-widest">Nama Depan</p>
                        <p class="font-bold text-gray-800">{{ $client->nama_depan }}</p>
                    </div>
                    <div>
                        <p class="text-[10px] text-gray-400 font-black uppercase mb-1 tracking-widest">Nama Belakang</p>
                        <p class="font-bold text-gray-800">{{ $client->nama_belakang }}</p>
                    </div>
                    <div>
                        <p class="text-[10px] text-gray-400 font-black uppercase mb-1 tracking-widest">Tempat & Tanggal
                            Lahir</p>
                        <p class="font-bold text-gray-800">
                            {{ $client->tanggal_lahir ? $client->tanggal_lahir->format('d F Y') : '-' }}
                        </p>
                    </div>
                    <div>
                        <p class="text-[10px] text-gray-400 font-black uppercase mb-1 tracking-widest">Status Disabilitas
                        </p>
                        <p class="font-bold text-gray-800 capitalize">{{ $client->status_difabel }}</p>
                    </div>
                </div>

                <h4
                    class="text-xl font-black text-gray-800 mt-12 mb-8 pb-4 border-b border-gray-100 flex items-center gap-3">
                    <span class="w-2 h-8 bg-yellow-400 rounded-full"></span>
                    Domisili & Alamat
                </h4>
                <div class="grid grid-cols-1 md:grid-cols-2 gap-8">
                    <div class="md:col-span-2">
                        <p class="text-[10px] text-gray-400 font-black uppercase mb-1 tracking-widest">Alamat Lengkap</p>
                        <p class="font-bold text-gray-800 leading-relaxed">{{ $client->alamat }}</p>
                    </div>
                    <div>
                        <p class="text-[10px] text-gray-400 font-black uppercase mb-1 tracking-widest">Dusun</p>
                        <p class="font-bold text-gray-800">{{ $client->dusun ?? '-' }}</p>
                    </div>
                    <div>
                        <p class="text-[10px] text-gray-400 font-black uppercase mb-1 tracking-widest">Kelurahan</p>
                        <p class="font-bold text-gray-800">{{ $client->kelurahan ?? '-' }}</p>
                    </div>
                    <div>
                        <p class="text-[10px] text-gray-400 font-black uppercase mb-1 tracking-widest">Kecamatan</p>
                        <p class="font-bold text-gray-800">{{ $client->kecamatan ?? '-' }}</p>
                    </div>
                    <div>
                        <p class="text-[10px] text-gray-400 font-black uppercase mb-1 tracking-widest">Provinsi</p>
                        <p class="font-bold text-gray-800">{{ $client->provinsi ?? '-' }}</p>
                    </div>
                </div>
            </div>

            <!-- Administrasi & Asuransi -->
            <div class="glass p-10">
                <h4 class="text-xl font-black text-gray-800 mb-8 pb-4 border-b border-gray-100 flex items-center gap-3">
                    <span class="w-2 h-8 bg-green-500 rounded-full"></span>
                    Kesehatan & Asuransi
                </h4>
                <div class="grid grid-cols-1 md:grid-cols-2 gap-8 mb-12">
                    <div>
                        <p class="text-[10px] text-gray-400 font-black uppercase mb-1 tracking-widest">Satus BPJS</p>
                        <p @class([
                            'font-black uppercase text-sm',
                            'text-green-600' => strtolower($client->status_bpjs) === 'ya',
                            'text-red-500' => strtolower($client->status_bpjs) !== 'ya',
                        ])>{{ $client->status_bpjs }}</p>
                    </div>
                    <div>
                        <p class="text-[10px] text-gray-400 font-black uppercase mb-1 tracking-widest">Asuransi Lainnya</p>
                        <p @class([
                            'font-black uppercase text-sm',
                            'text-green-600' => strtolower($client->status_asuransi) === 'ya',
                            'text-red-500' => strtolower($client->status_asuransi) !== 'ya',
                        ])>{{ $client->status_asuransi }}</p>
                    </div>
                    @if(strtolower($client->status_asuransi) === 'ya')
                        <div>
                            <p class="text-[10px] text-gray-400 font-black uppercase mb-1 tracking-widest">Nama Asuransi</p>
                            <p class="font-bold text-gray-800">{{ $client->nama_asuransi }}</p>
                        </div>
                        <div>
                            <p class="text-[10px] text-gray-400 font-black uppercase mb-1 tracking-widest">Nomor Asuransi</p>
                            <p class="font-bold text-gray-800">{{ $client->nomor_asuransi }}</p>
                        </div>
                    @endif
                </div>

                <h4 class="text-xl font-black text-gray-800 mb-8 pb-4 border-b border-gray-100 flex items-center gap-3">
                    <span class="w-2 h-8 bg-indigo-500 rounded-full"></span>
                    Aktivitas & Administrasi
                </h4>
                <div class="grid grid-cols-1 md:grid-cols-2 gap-8">
                    <div>
                        <p class="text-[10px] text-gray-400 font-black uppercase mb-1 tracking-widest">Status Aktivitas</p>
                        <p class="font-bold text-gray-800">{{ $client->status_aktivitas ?? '-' }}</p>
                    </div>
                    <div>
                        <p class="text-[10px] text-gray-400 font-black uppercase mb-1 tracking-widest">Jenis Sekolah</p>
                        <p class="font-bold text-gray-800">{{ $client->jenis_sekolah ?? '-' }}</p>
                    </div>
                    <div>
                        <p class="text-[10px] text-gray-400 font-black uppercase mb-1 tracking-widest">Terdaftar Dari Klinik
                        </p>
                        <p class="font-bold text-gray-800">{{ $client->dari_klinik ?? '-' }}</p>
                    </div>
                    <div>
                        <p class="text-[10px] text-gray-400 font-black uppercase mb-1 tracking-widest">Source ID</p>
                        <p class="font-mono text-xs font-bold text-gray-500">{{ $client->source_id ?? '-' }}</p>
                    </div>
                    <div>
                        <p class="text-[10px] text-gray-400 font-black uppercase mb-1 tracking-widest">Tanggal Daftar</p>
                        <p class="font-bold text-gray-800">
                            {{ $client->tanggal_daftar ? $client->tanggal_daftar->format('d M Y') : '-' }}
                        </p>
                    </div>
                </div>
            </div>

            <!-- Info Tambahan -->
            <div class="glass p-10 bg-gray-900/5">
                <h4 class="text-xl font-black text-gray-800 mb-6 flex items-center gap-3">
                    <svg class="w-6 h-6 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                    </svg>
                    Informasi Tambahan & Catatan
                </h4>
                <div
                    class="p-6 rounded-3xl bg-white/50 border border-white italic text-gray-600 leading-relaxed shadow-inner">
                    {{ $client->info_tambahan ?? 'Tidak ada informasi tambahan yang dicatat.' }}
                </div>
            </div>
        </div>
    </div>
@endsection