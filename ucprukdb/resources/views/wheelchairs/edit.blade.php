@extends('layouts.app')

@section('page-title', 'Edit Kursi Roda')

@section('content')
    <div class="mb-6">
        <a href="{{ route('wheelchairs.index') }}" class="text-primary font-bold hover:underline flex items-center gap-2">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18" />
            </svg>
            Kembali ke Inventaris
        </a>
    </div>

    <div class="max-w-2xl">
        <div class="glass p-10">
            <h2 class="text-2xl font-black text-gray-800 mb-8 tracking-tight">Edit Data: {{ $wheelchair->kursiroda_id }}
            </h2>

            <form action="{{ route('wheelchairs.update', $wheelchair->kursiroda_id) }}" method="POST" class="space-y-6">
                @csrf
                @method('PUT')

                <!-- Client Display (Read Only) -->
                <div>
                    <label class="label">Klien (Tidak dapat diubah)</label>
                    <input type="text" value="{{ $wheelchair->nama_lengkap }} ({{ $wheelchair->client_id }})"
                        class="input bg-gray-50/50 cursor-not-allowed" readonly>
                    <input type="hidden" name="client_id" value="{{ $wheelchair->client_id }}">
                </div>

                <!-- Wheelchair Type -->
                <div>
                    <label class="label">Jenis Kursi Roda</label>
                    <input type="text" name="jenis_kursiroda"
                        value="{{ old('jenis_kursiroda', $wheelchair->jenis_kursiroda) }}"
                        class="input @error('jenis_kursiroda') border-red-300 @enderror"
                        placeholder="Misal: Active Rigit, Folding Standard, dll." required>
                    @error('jenis_kursiroda') <p class="text-red-500 text-xs font-bold mt-1">{{ $message }}</p> @enderror
                </div>

                <div class="pt-4">
                    <button type="submit" class="btn-primary w-full py-4 text-lg">
                        Perbarui Data Inventaris
                    </button>
                </div>
            </form>
        </div>
    </div>
@endsection