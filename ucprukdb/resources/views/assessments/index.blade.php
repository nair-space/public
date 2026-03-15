@extends('layouts.app')

@section('page-title', 'Asesmen Klien')

@section('content')
    <div class="flex flex-col md:flex-row md:items-center justify-between gap-4 mb-8">
        <div>
            <h2 class="text-3xl font-black text-gray-800 tracking-tight">Daftar Asesmen</h2>
            <p class="text-sm text-gray-500 font-medium">Manajemen data penilaian teknis dan fisik klien</p>
        </div>
        <a href="{{ route('client-assessments.create') }}" class="btn-primary flex items-center gap-2 self-start">
            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4" />
            </svg>
            <span>Tambah Asesmen</span>
        </a>
    </div>

    <div class="glass overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full text-left border-collapse">
                <thead>
                    <tr class="bg-gray-50/50">
                        <th class="table-header">ID Asesmen</th>
                        <th class="table-header">Nama Klien</th>
                        <th class="table-header">Diagnosis</th>
                        <th class="table-header">Tanggal Dibuat</th>
                        <th class="table-header text-right">Aksi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-100/50">
                    @forelse($assessments as $assessment)
                        <tr class="table-row-glass group">
                            <td class="table-cell">
                                <span class="font-mono text-[10px] font-black py-1 px-2 rounded bg-gray-100 text-gray-400">
                                    {{ $assessment->client_basic_assessment_id }}
                                </span>
                            </td>
                            <td class="table-cell">
                                <p class="font-bold text-gray-700 leading-tight">
                                    {{ $assessment->client->nama_lengkap ?? 'Unknown' }}
                                </p>
                                <p class="text-[10px] text-gray-400 font-black mt-0.5">{{ $assessment->client_id }}</p>
                            </td>
                            <td class="table-cell">
                                <span class="text-sm text-gray-600 line-clamp-1">{{ $assessment->diagnosis ?: '-' }}</span>
                            </td>
                            <td class="table-cell text-sm text-gray-500 font-medium">
                                {{ $assessment->created_at->format('d M Y') }}
                            </td>
                            <td class="table-cell text-right">
                                <div class="flex justify-end gap-2 opacity-0 group-hover:opacity-100 transition-opacity">
                                    <a href="{{ route('client-assessments.show', $assessment->client_basic_assessment_id) }}"
                                        class="p-2 rounded-lg bg-blue-500/10 text-blue-600 hover:bg-blue-600 hover:text-white transition-all">
                                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z" />
                                        </svg>
                                    </a>
                                    <a href="{{ route('client-assessments.edit', $assessment->client_basic_assessment_id) }}"
                                        class="p-2 rounded-lg bg-accent/20 text-yellow-700 hover:bg-accent hover:text-yellow-900 transition-all">
                                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z" />
                                        </svg>
                                    </a>
                                    <form
                                        action="{{ route('client-assessments.destroy', $assessment->client_basic_assessment_id) }}"
                                        method="POST" class="inline"
                                        onsubmit="return confirmAction(this, 'Hapus Asesmen', 'Apakah Anda yakin ingin menghapus data asesmen ini?')">
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
                                <div class="mb-4 text-gray-200">
                                    <svg class="w-16 h-16 mx-auto" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                            d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                                    </svg>
                                </div>
                                <p class="text-gray-500 font-bold text-lg mb-2">Belum ada data asesmen</p>
                                <p class="text-gray-400 text-sm">Klik tombol "Tambah Asesmen" untuk memulai.</p>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        @if($assessments->hasPages())
            <div class="px-8 py-6 bg-gray-50/50 border-t border-gray-100/50">
                {{ $assessments->links() }}
            </div>
        @endif
    </div>
@endsection