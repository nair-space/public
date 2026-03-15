@extends('layouts.app')

@section('page-title', 'Ekspor Data Klien')

@section('content')
    <div class="mb-8 px-2">
        <h2 class="text-2xl font-black text-gray-800 tracking-tight">Kustomisasi Ekspor Klien</h2>
        <p class="text-sm text-gray-500 font-medium">Pilih kolom yang ingin Anda sertakan dalam file Excel (.xlsx)</p>
    </div>

    <div class="glass p-8">
        <form action="{{ route('export.client.process') }}" method="POST">
            @csrf
            
            <!-- Date Filter Section -->
            <div class="p-6 rounded-2xl bg-white/40 border border-white/20 mb-8">
                <h4 class="text-sm font-black text-gray-800 uppercase tracking-wider mb-4">Urutkan Berdasarkan Tanggal Daftar</h4>
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <div>
                        <label for="start_date" class="label">Tanggal Mulai</label>
                        <input type="date" name="start_date" id="start_date" class="input">
                    </div>
                    <div>
                        <label for="end_date" class="label">Tanggal Akhir</label>
                        <input type="date" name="end_date" id="end_date" class="input">
                    </div>
                </div>
                <p class="text-xs text-gray-400 mt-3 font-medium">* Kosongkan untuk mengekspor semua periode</p>
            </div>

            <div class="flex items-center justify-between mb-8 pb-4 border-b border-gray-100">
                <div class="flex items-center gap-2">
                    <input type="checkbox" id="selectAll" class="w-5 h-5 rounded border-gray-300 text-primary focus:ring-primary">
                    <label for="selectAll" class="text-sm font-black text-gray-700 uppercase tracking-wider cursor-pointer">Pilih Semua Kolom</label>
                </div>
                <div class="text-xs text-gray-400 font-bold uppercase tracking-widest">
                    {{ count($columns) }} Kolom Tersedia
                </div>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4 mb-10">
                @foreach($columns as $column)
                    <div class="flex items-center gap-3 p-4 rounded-xl bg-white/40 border border-white/20 hover:bg-white/60 transition-colors">
                        <input type="checkbox" name="columns[]" value="{{ $column }}" id="col_{{ $column }}" 
                            class="column-checkbox w-5 h-5 rounded border-gray-300 text-primary focus:ring-primary"
                            {{ in_array($column, ['client_id', 'nama_lengkap', 'nik', 'jenis_disabilitas', 'provinsi']) ? 'checked' : '' }}>
                        <label for="col_{{ $column }}" class="text-sm font-semibold text-gray-700 capitalize cursor-pointer">
                            {{ str_replace('_', ' ', $column) }}
                        </label>
                    </div>
                @endforeach
            </div>

            <div class="flex gap-4">
                <button type="submit" class="btn-primary flex-1 py-4 text-lg shadow-xl shadow-primary/20">
                    <svg class="w-6 h-6 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                    </svg>
                    Unduh File Excel (.xlsx)
                </button>
                <a href="{{ route('data-management.index') }}" class="btn-outline px-8 py-4">Batal</a>
            </div>
        </form>
    </div>

    @push('scripts')
    <script>
        document.getElementById('selectAll').addEventListener('change', function() {
            const checkboxes = document.querySelectorAll('.column-checkbox');
            checkboxes.forEach(cb => cb.checked = this.checked);
        });

        // Update selectAll state based on individual checkboxes
        const checkboxes = document.querySelectorAll('.column-checkbox');
        checkboxes.forEach(cb => {
            cb.addEventListener('change', function() {
                const allChecked = Array.from(checkboxes).every(c => c.checked);
                document.getElementById('selectAll').checked = allChecked;
            });
        });
    </script>
    @endpush
@endsection
