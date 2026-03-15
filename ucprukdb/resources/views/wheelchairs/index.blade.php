@extends('layouts.app')

@section('page-title', 'Manajemen Kursi Roda')

@section('content')
    <div class="flex justify-between items-center mb-8 px-2">
        <div>
            <h2 class="text-2xl font-black text-gray-800 tracking-tight">Database Kursi Roda</h2>
            <p class="text-sm text-gray-500 font-medium">Pengelolaan inventaris kursi roda klien</p>
        </div>
        <a href="{{ route('wheelchairs.create') }}" class="btn-primary">
            <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4" />
            </svg>
            Tambah Kursi Roda
        </a>
    </div>

    <!-- Inventory Table (Glass) -->
    <div class="glass p-8">
        <div class="overflow-x-auto">
            <table class="table-glass">
                <thead>
                    <tr>
                        <th class="table-header">ID</th>
                        <th class="table-header">Jenis Kursi Roda</th>
                        <th class="table-header">Informasi Klien</th>
                        <th class="table-header text-right">Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($wheelchairs as $wheelchair)
                        <tr class="table-row-glass group">
                            <td class="table-cell">
                                <span class="font-mono text-[10px] font-black py-1 px-2 rounded bg-gray-100 text-gray-400">
                                    {{ $wheelchair->kursiroda_id }}
                                </span>
                            </td>
                            <td class="table-cell font-bold text-gray-800">
                                {{ $wheelchair->jenis_kursiroda }}
                            </td>
                            <td class="table-cell">
                                <p class="font-bold text-gray-700 leading-tight">{{ $wheelchair->nama_lengkap }}</p>
                                <p class="text-[10px] text-gray-400 font-black mt-0.5">{{ $wheelchair->nik }}</p>
                            </td>
                            <td class="table-cell text-right">
                                <div class="flex justify-end gap-2 opacity-0 group-hover:opacity-100 transition-opacity">
                                    <a href="{{ route('wheelchairs.show', $wheelchair->kursiroda_id) }}"
                                        class="p-2 rounded-lg bg-blue-500/10 text-blue-600 hover:bg-blue-600 hover:text-white transition-all">
                                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z" />
                                        </svg>
                                    </a>
                                    <a href="{{ route('wheelchairs.edit', $wheelchair->kursiroda_id) }}"
                                        class="p-2 rounded-lg bg-accent/20 text-yellow-700 hover:bg-accent hover:text-yellow-900 transition-all">
                                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z" />
                                        </svg>
                                    </a>
                                    <form action="{{ route('wheelchairs.destroy', $wheelchair->kursiroda_id) }}" method="POST"
                                        class="inline"
                                        onsubmit="return confirmAction(this, 'Hapus Kursi Roda', 'Apakah Anda yakin ingin menghapus data kursi roda ini?')">
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
                            <td colspan="4" class="table-cell text-center p-20">
                                <p class="text-gray-500 font-bold text-lg mb-2">Belum ada data kursi roda</p>
                                <a href="{{ route('wheelchairs.create') }}" class="text-primary font-bold hover:underline">Klik
                                    di sini untuk menambah data pertama</a>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <div class="mt-8 px-4">
            {{ $wheelchairs->links() }}
        </div>
    </div>
@endsection