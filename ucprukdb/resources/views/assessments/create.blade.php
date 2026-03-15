@extends('layouts.app')

@section('page-title', 'Tambah Asesmen Klien')

@section('content')
    <div class="mb-8 flex items-center gap-4">
        <a href="{{ route('client-assessments.index') }}" class="p-3 rounded-2xl bg-white/60 text-gray-500 hover:text-primary transition-all glass">
            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"/></svg>
        </a>
        <div>
            <h2 class="text-2xl font-black text-gray-800 tracking-tight">Input Asesmen Baru</h2>
            <p class="text-sm text-gray-500 font-medium">Lengkapi formulir penilaian teknis dan fisik klien</p>
        </div>
    </div>

    <form action="{{ route('client-assessments.store') }}" method="POST" class="space-y-8 pb-12">
        @csrf

        <!-- SECTION 1: Klien & Diagnosis -->
        <div class="glass p-8">
            <div class="flex items-center gap-4 mb-6">
                <div class="w-10 h-10 rounded-xl bg-primary/10 flex items-center justify-center text-primary">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/></svg>
                </div>
                <h3 class="text-lg font-bold text-gray-800">Identitas & Diagnosis</h3>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <div class="space-y-2">
                    <label class="label">Pilih Klien</label>
                    <select name="client_id" class="input @error('client_id') border-red-500 @enderror" required>
                        <option value="">-- Pilih Klien --</option>
                        @foreach($clients as $client)
                            <option value="{{ $client->client_id }}" {{ old('client_id') == $client->client_id ? 'selected' : '' }}>
                                {{ $client->nama_lengkap }} ({{ $client->client_id }})
                            </option>
                        @endforeach
                    </select>
                    @error('client_id') <p class="text-xs text-red-500 font-bold mt-1">{{ $message }}</p> @enderror
                </div>

                <div class="space-y-2">
                    <label class="label">Client Service ID</label>
                    <input type="text" name="client_service_id" value="{{ old('client_service_id') }}" class="input" placeholder="Contoh: SRV001">
                </div>

                <div class="md:col-span-2 space-y-2">
                    <label class="label">Diagnosis</label>
                    <textarea name="diagnosis" class="input min-h-[100px]" placeholder="Masukkan diagnosis medis klien...">{{ old('diagnosis') }}</textarea>
                </div>
            </div>
        </div>

        <!-- SECTION 2: Penggunaan Peralatan -->
        <div class="glass p-8">
            <div class="flex items-center gap-4 mb-6">
                <div class="w-10 h-10 rounded-xl bg-blue-500/10 flex items-center justify-center text-blue-600">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 012-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10"/></svg>
                </div>
                <h3 class="text-lg font-bold text-gray-800">Penggunaan Peralatan Saat Ini</h3>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                <div class="space-y-2">
                    <label class="label">Di Sekolah</label>
                    <input type="text" name="equipment_school_use" value="{{ old('equipment_school_use') }}" class="input">
                </div>
                <div class="space-y-2">
                    <label class="label">Di Tempat Kerja</label>
                    <input type="text" name="equipment_workplace_use" value="{{ old('equipment_workplace_use') }}" class="input">
                </div>
                <div class="space-y-2">
                    <label class="label">Di Rumah</label>
                    <input type="text" name="equipment_home_use" value="{{ old('equipment_home_use') }}" class="input">
                </div>
                <div class="space-y-2">
                    <label class="label">Di Lingkungan Sekitar</label>
                    <input type="text" name="equipment_neighborhood_use" value="{{ old('equipment_neighborhood_use') }}" class="input">
                </div>
                <div class="space-y-2">
                    <label class="label">Transfer</label>
                    <input type="text" name="transfer" value="{{ old('transfer') }}" class="input" placeholder="e.g. Mandiri, Bantuan">
                </div>
                <div class="space-y-2">
                    <label class="label">Kondisi Alat Lama</label>
                    <input type="text" name="old_equipment_goodness" value="{{ old('old_equipment_goodness') }}" class="input">
                </div>
            </div>
        </div>

        <!-- SECTION 3: Pengukuran Fisik -->
        <div class="glass p-8">
            <div class="flex items-center gap-4 mb-6">
                <div class="w-10 h-10 rounded-xl bg-purple-500/10 flex items-center justify-center text-purple-600">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 18h.01M8 21h8a2 2 0 002-2V5a2 2 0 00-2-2H8a2 2 0 00-2 2v14a2 2 0 002 2z"/></svg>
                </div>
                <h3 class="text-lg font-bold text-gray-800">Pengukuran Fisik (cm)</h3>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-4 gap-6">
                <div class="space-y-2">
                    <label class="label">Lebar Panggul</label>
                    <input type="number" step="0.01" name="pelvic_width" value="{{ old('pelvic_width') }}" class="input">
                </div>
                <div class="space-y-2">
                    <label class="label">Lengan Atas Kiri</label>
                    <input type="number" step="0.01" name="left_upper_limb" value="{{ old('left_upper_limb') }}" class="input">
                </div>
                <div class="space-y-2">
                    <label class="label">Lengan Atas Kanan</label>
                    <input type="number" step="0.01" name="right_upper_limb" value="{{ old('right_upper_limb') }}" class="input">
                </div>
                <div class="space-y-2">
                    <label class="label">Tungkai Bawah Kiri</label>
                    <input type="number" step="0.01" name="left_lower_limb" value="{{ old('left_lower_limb') }}" class="input">
                </div>
                <div class="space-y-2">
                    <label class="label">Tungkai Bawah Kanan</label>
                    <input type="number" step="0.01" name="right_lower_limb" value="{{ old('right_lower_limb') }}" class="input">
                </div>
                <div class="space-y-2">
                    <label class="label">Lebar Kursi</label>
                    <input type="number" step="0.01" name="seat_width" value="{{ old('seat_width') }}" class="input">
                </div>
                <div class="space-y-2">
                    <label class="label">Panjang Kursi</label>
                    <input type="number" step="0.01" name="seat_length" value="{{ old('seat_length') }}" class="input">
                </div>
            </div>
        </div>

        <!-- SECTION 4: Kebutuhan Spesifik -->
        <div class="glass p-8">
            <div class="flex items-center gap-4 mb-6">
                <div class="w-10 h-10 rounded-xl bg-accent/20 flex items-center justify-center text-yellow-700">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z"/></svg>
                </div>
                <h3 class="text-lg font-bold text-gray-800">Spesifikasi & Kebutuhan</h3>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
                <div class="space-y-2">
                    <label class="label">Cushion Type</label>
                    <input type="text" name="cushion_type" value="{{ old('cushion_type') }}" class="input">
                </div>
                <div class="space-y-4 pt-4">
                    <div class="flex items-center gap-3">
                        <input type="hidden" name="active" value="tidak">
                        <input type="checkbox" name="active" value="ya" class="w-5 h-5 accent-primary" {{ old('active') == 'ya' ? 'checked' : '' }}>
                        <span class="text-sm font-bold text-gray-700 uppercase tracking-tight">Kursi Roda Aktif</span>
                    </div>
                    <div class="flex items-center gap-3">
                        <input type="hidden" name="stroller" value="tidak">
                        <input type="checkbox" name="stroller" value="ya" class="w-5 h-5 accent-primary" {{ old('stroller') == 'ya' ? 'checked' : '' }}>
                        <span class="text-sm font-bold text-gray-700 uppercase tracking-tight">Stroller</span>
                    </div>
                </div>
                <div class="md:col-span-2 lg:col-span-3 space-y-2">
                    <label class="label">Alasan & Deskripsi Lanjutan</label>
                    <textarea name="sp_description" class="input min-h-[100px]">{{ old('sp_description') }}</textarea>
                </div>
            </div>
        </div>

        <div class="flex justify-end gap-3 mt-8">
            <a href="{{ route('client-assessments.index') }}" class="btn-outline px-8 py-3">Batal</a>
            <button type="submit" class="btn-primary px-12 py-3 shadow-xl shadow-primary/20">Simpan Asesmen</button>
        </div>
    </form>
@endsection
