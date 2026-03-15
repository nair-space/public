@extends('layouts.app')

@section('page-title', 'Utama')

@section('content')
    <!-- Stats Cards -->
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6 mb-10">
        <div class="glass-card">
            <div class="flex items-center gap-4">
                <div class="w-12 h-12 rounded-2xl bg-blue-500/10 flex items-center justify-center text-blue-600">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0z" />
                    </svg>
                </div>
                <div>
                    <p class="text-[10px] font-black text-gray-400 uppercase tracking-widest">Total Klien</p>
                    <p class="text-3xl font-extrabold text-gray-800">{{ number_format($stats['total_clients']) }}</p>
                </div>
            </div>
        </div>

        <div class="glass-card">
            <div class="flex items-center gap-4">
                <div class="w-12 h-12 rounded-2xl bg-purple-500/10 flex items-center justify-center text-purple-600">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                    </svg>
                </div>
                <div>
                    <p class="text-[10px] font-black text-gray-400 uppercase tracking-widest">Assesmen</p>
                    <p class="text-3xl font-extrabold text-gray-800">{{ number_format($stats['total_assessments']) }}</p>
                </div>
            </div>
        </div>

        <div class="glass-card">
            <div class="flex items-center gap-4">
                <div class="w-12 h-12 rounded-2xl bg-yellow-500/10 flex items-center justify-center text-yellow-600">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 012-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10" />
                    </svg>
                </div>
                <div>
                    <p class="text-[10px] font-black text-gray-400 uppercase tracking-widest">Kursi Roda</p>
                    <p class="text-3xl font-extrabold text-gray-800">{{ number_format($stats['total_wheelchairs']) }}</p>
                </div>
            </div>
        </div>

        <div class="glass-card">
            <div class="flex items-center gap-4">
                <div class="w-12 h-12 rounded-2xl bg-green-500/10 flex items-center justify-center text-green-600">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M13 7h8m0 0v8m0-8l-8 8-4-4-6 6" />
                    </svg>
                </div>
                <div>
                    <p class="text-[10px] font-black text-gray-400 uppercase tracking-widest">Tren Baru</p>
                    <p class="text-3xl font-extrabold text-gray-800">+{{ number_format($stats['clients_this_month']) }}</p>
                </div>
            </div>
        </div>
    </div>

    <!-- Recent Clients -->
    <div class="glass p-8">
        <div class="flex justify-between items-center mb-8">
            <div>
                <h2 class="text-2xl font-black text-gray-800 tracking-tight">Klien Terbaru</h2>
                <p class="text-sm text-gray-500 font-medium">Data pendaftaran 5 klien terakhir</p>
            </div>
            <a href="{{ route('client-bio.index') }}" class="btn-outline text-sm">
                Lihat Semua
            </a>
        </div>

        <div class="overflow-x-auto">
            <table class="table-glass">
                <thead>
                    <tr>
                        <th class="table-header">ID Klien</th>
                        <th class="table-header">Nama Lengkap</th>
                        <th class="table-header">Tanggal</th>
                        <th class="table-header">Provinsi</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($recentClients as $client)
                        <tr class="table-row-glass">
                            <td class="table-cell font-mono text-xs text-gray-500">{{ $client->client_id }}</td>
                            <td class="table-cell font-bold text-gray-800">{{ $client->nama_lengkap }}</td>
                            <td class="table-cell text-gray-600">
                                {{ \Carbon\Carbon::parse($client->tanggal_daftar)->format('d M Y') }}</td>
                            <td class="table-cell">
                                <span
                                    class="px-3 py-1 rounded-full bg-blue-500/10 text-blue-600 text-[10px] font-black uppercase tracking-wider">
                                    {{ $client->provinsi }}
                                </span>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="4" class="table-cell text-center p-12">
                                <div class="flex flex-col items-center gap-2">
                                    <div
                                        class="w-12 h-12 rounded-full bg-gray-100 flex items-center justify-center text-gray-400">
                                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                d="M20 13V6a2 2 0 00-2-2H6a2 2 0 00-2 2v7m16 0v5a2 2 0 01-2 2H6a2 2 0 01-2-2v-5m16 0h-2.586a1 1 0 00-.707.293l-2.414 2.414a1 1 0 01-.707.293h-3.172a1 1 0 01-.707-.293l-2.414-2.414A1 1 0 006.586 13H4" />
                                        </svg>
                                    </div>
                                    <p class="text-gray-500 font-medium italic">Belum ada data klien.</p>
                                </div>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
@endsection