@extends('layouts.app')

@section('page-title', 'Database Klien')

@section('content')
    <!-- Filters & Search (Glass) -->
    <div class="glass p-6 mb-8">
        <form action="{{ route('client-bio.index') }}" method="GET" class="flex flex-wrap gap-6 items-end">
            <div class="flex-1 min-w-[280px]">
                <label class="label">Pencarian Cepat</label>
                <div class="relative">
                    <input type="text" name="search" value="{{ $filters['search'] ?? '' }}" class="input pl-10"
                        placeholder="Ketik nama klien...">
                    <svg class="w-5 h-5 text-gray-400 absolute left-3 top-3" fill="none" stroke="currentColor"
                        viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" />
                    </svg>
                </div>
            </div>
            <div class="w-56">
                <label class="label">Wilayah (Provinsi)</label>
                <input type="text" name="provinsi" value="{{ $filters['provinsi'] ?? '' }}" class="input"
                    placeholder="Semua wilayah">
            </div>
            <div class="flex gap-3">
                <button type="submit" class="btn-primary px-8">Filter</button>
                <a href="{{ route('client-bio.index') }}" class="btn-outline">Reset</a>
            </div>
        </form>
    </div>

    <!-- Actions Banner -->
    <div class="flex justify-between items-center mb-8 px-2">
        <div>
            <h2 class="text-2xl font-black text-gray-800 tracking-tight">Daftar Klien Terpusat</h2>
            <p class="text-sm text-gray-500 font-medium">Menampilkan {{ $clients->total() }} data ditemukan</p>
        </div>
        <a href="{{ route('client-bio.create') }}" class="btn-primary">
            <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4" />
            </svg>
            Klien Baru
        </a>
    </div>

    <!-- Results Table (Glass) -->
    <div class="glass p-8">
        <div class="overflow-x-auto">
            <table class="table-glass">
                <thead>
                    <tr>
                        <th class="table-header">ID</th>
                        <th class="table-header">Informasi Klien</th>
                        <th class="table-header">Lokasi</th>
                        <th class="table-header">Disabilitas</th>
                        <th class="table-header text-right">Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($clients as $client)
                        <tr class="table-row-glass group">
                            <td class="table-cell">
                                <span class="font-mono text-[10px] font-black py-1 px-2 rounded bg-gray-100 text-gray-400">
                                    {{ $client->client_id }}
                                </span>
                            </td>
                            <td class="table-cell">
                                <p class="font-bold text-gray-800 leading-tight">{{ $client->nama_lengkap }}</p>
                                <p class="text-xs text-gray-500 font-medium mt-0.5">Terdaftar
                                    {{ \Carbon\Carbon::parse($client->tanggal_daftar)->format('d M Y') }}</p>
                            </td>
                            <td class="table-cell">
                                <div class="flex items-center gap-2">
                                    <svg class="w-3 h-3 text-red-400" fill="currentColor" viewBox="0 0 20 20">
                                        <path fill-rule="evenodd"
                                            d="M5.05 4.05a7 7 0 119.9 9.9L10 18.9l-4.95-4.95a7 7 0 010-9.9zM10 11a2 2 0 100-4 2 2 0 000 4z"
                                            clip-rule="evenodd" />
                                    </svg>
                                    <span
                                        class="text-sm font-bold text-gray-700 capitalize">{{ strtolower($client->provinsi) }}</span>
                                </div>
                            </td>
                            <td class="table-cell">
                                <span
                                    class="px-3 py-1 rounded-lg bg-yellow-500/10 text-yellow-700 text-[10px] font-black uppercase tracking-wider">
                                    {{ $client->jenis_disabilitas }}
                                </span>
                            </td>
                            <td class="table-cell text-right">
                                <div class="flex justify-end gap-2 opacity-0 group-hover:opacity-100 transition-opacity">
                                    <a href="{{ route('client-bio.show', $client->client_id) }}"
                                        class="p-2 rounded-lg bg-blue-500/10 text-blue-600 hover:bg-blue-600 hover:text-white transition-all">
                                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z" />
                                        </svg>
                                    </a>
                                    <a href="{{ route('client-bio.edit', $client->client_id) }}"
                                        class="p-2 rounded-lg bg-accent/20 text-yellow-700 hover:bg-accent hover:text-yellow-900 transition-all">
                                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z" />
                                        </svg>
                                    </a>
                                    <form action="{{ route('client-bio.destroy', $client->client_id) }}" method="POST"
                                        class="inline" onsubmit="return confirmAction(this, 'Hapus Klien', 'Semua data terkait (asesmen & kursi roda) juga akan dihapus. Lanjutkan?')">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit"
                                            class="p-2 rounded-lg bg-red-500/10 text-red-600 hover:bg-red-600 hover:text-white transition-all">
                                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                    d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v2m3 3h-6" />
                                            </svg>
                                        </button>
                                    </form>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="5" class="table-cell text-center p-20">
                                <p class="text-gray-500 font-bold text-lg mb-2">Pencarian Tidak Ditemukan</p>
                                <p class="text-gray-400">Coba gunakan kata kunci atau filter lain.</p>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <!-- Pagination (Custom Glass) -->
        <div class="mt-10 px-4">
            {{ $clients->withQueryString()->links() }}
        </div>
    </div>
@endsection