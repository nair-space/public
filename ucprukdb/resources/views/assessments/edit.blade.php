@extends('layouts.app')

@section('page-title', 'Edit Asesmen Klien')

@section('content')
    <div class="mb-8 flex items-center gap-4">
        <a href="{{ route('client-assessments.index') }}"
            class="p-3 rounded-2xl bg-white/60 text-gray-500 hover:text-primary transition-all glass">
            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18" />
            </svg>
        </a>
        <div>
            <h2 class="text-2xl font-black text-gray-800 tracking-tight">Perbarui Asesmen</h2>
            <p class="text-sm text-gray-500 font-medium">ID: {{ $assessment->client_basic_assessment_id }}</p>
        </div>
    </div>

    <form action="{{ route('client-assessments.update', $assessment->client_basic_assessment_id) }}" method="POST"
        class="space-y-8 pb-12">
        @csrf
        @method('PUT')

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

            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <div class="space-y-2">
                    <label class="label">Pilih Klien</label>
                    <select name="client_id" class="input @error('client_id') border-red-500 @enderror" required>
                        @foreach($clients as $client)
                            <option value="{{ $client->client_id }}" {{ (old('client_id', $assessment->client_id) == $client->client_id) ? 'selected' : '' }}>
                                {{ $client->nama_lengkap }} ({{ $client->client_id }})
                            </option>
                        @endforeach
                    </select>
                    @error('client_id') <p class="text-xs text-red-500 font-bold mt-1">{{ $message }}</p> @enderror
                </div>

                <div class="space-y-2">
                    <label class="label">Client Service ID</label>
                    <input type="text" name="client_service_id"
                        value="{{ old('client_service_id', $assessment->client_service_id) }}" class="input">
                </div>

                <div class="md:col-span-2 space-y-2">
                    <label class="label">Diagnosis</label>
                    <textarea name="diagnosis"
                        class="input min-h-[100px]">{{ old('diagnosis', $assessment->diagnosis) }}</textarea>
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

            <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                <div class="space-y-2">
                    <label class="label">Di Sekolah</label>
                    <input type="text" name="equipment_school_use"
                        value="{{ old('equipment_school_use', $assessment->equipment_school_use) }}" class="input">
                </div>
                <div class="space-y-2">
                    <label class="label">Di Tempat Kerja</label>
                    <input type="text" name="equipment_workplace_use"
                        value="{{ old('equipment_workplace_use', $assessment->equipment_workplace_use) }}" class="input">
                </div>
                <div class="space-y-2">
                    <label class="label">Di Rumah</label>
                    <input type="text" name="equipment_home_use"
                        value="{{ old('equipment_home_use', $assessment->equipment_home_use) }}" class="input">
                </div>
                <div class="space-y-2">
                    <label class="label">Di Lingkungan Sekitar</label>
                    <input type="text" name="equipment_neighborhood_use"
                        value="{{ old('equipment_neighborhood_use', $assessment->equipment_neighborhood_use) }}"
                        class="input">
                </div>
                <div class="space-y-2">
                    <label class="label">Transfer</label>
                    <input type="text" name="transfer" value="{{ old('transfer', $assessment->transfer) }}" class="input">
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
                <h3 class="text-lg font-bold text-gray-800">Pengukuran Fisik (cm)</h3>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-4 gap-6">
                <div class="space-y-2">
                    <label class="label">Lebar Panggul</label>
                    <input type="number" step="0.01" name="pelvic_width"
                        value="{{ old('pelvic_width', $assessment->pelvic_width) }}" class="input">
                </div>
                <div class="space-y-2">
                    <label class="label">Lengan Atas Kiri</label>
                    <input type="number" step="0.01" name="left_upper_limb"
                        value="{{ old('left_upper_limb', $assessment->left_upper_limb) }}" class="input">
                </div>
                <div class="space-y-2">
                    <label class="label">Lebar Kursi</label>
                    <input type="number" step="0.01" name="seat_width"
                        value="{{ old('seat_width', $assessment->seat_width) }}" class="input">
                </div>
                <div class="space-y-2">
                    <label class="label">Panjang Kursi</label>
                    <input type="number" step="0.01" name="seat_length"
                        value="{{ old('seat_length', $assessment->seat_length) }}" class="input">
                </div>
            </div>
        </div>

        <div class="flex justify-end gap-3 mt-8">
            <a href="{{ route('client-assessments.index') }}" class="btn-outline px-8 py-3">Batal</a>
            <button type="submit" class="btn-primary px-12 py-3 shadow-xl shadow-primary/20">Simpan Perubahan</button>
        </div>
    </form>
@endsection