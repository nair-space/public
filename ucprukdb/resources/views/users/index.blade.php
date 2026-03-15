@extends('layouts.app')

@section('page-title', 'Manajemen User')

@section('content')
    <div class="flex justify-between items-center mb-6">
        <h2 class="text-xl font-bold text-gray-900">Daftar User</h2>
        <a href="{{ route('users.create') }}" class="btn-primary">+ Tambah User</a>
    </div>

    <div class="card">
        <div class="overflow-x-auto">
            <table class="w-full">
                <thead>
                    <tr>
                        <th class="table-header">ID</th>
                        <th class="table-header">Username</th>
                        <th class="table-header">Nama Lengkap</th>
                        <th class="table-header">Jabatan</th>
                        <th class="table-header">Email</th>
                        <th class="table-header">Status</th>
                        <th class="table-header">Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($users as $user)
                        <tr class="table-row">
                            <td class="table-cell font-mono text-sm">{{ $user->user_id }}</td>
                            <td class="table-cell font-semibold">{{ $user->username }}</td>
                            <td class="table-cell">{{ $user->nama_lengkap }}</td>
                            <td class="table-cell capitalize">{{ $user->jabatan }}</td>
                            <td class="table-cell">{{ $user->email }}</td>
                            <td class="table-cell">
                                <span
                                    class="px-2 py-1 text-xs font-bold {{ $user->status == 'aktif' ? 'bg-green-100 text-green-700' : 'bg-red-100 text-red-700' }}">
                                    {{ ucfirst($user->status) }}
                                </span>
                            </td>
                            <td class="table-cell">
                                <a href="{{ route('users.edit', $user->user_id) }}"
                                    class="text-primary font-semibold hover:underline">Edit</a>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="7" class="table-cell text-center text-gray-500 py-8">
                                Belum ada user.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <div class="mt-6">
            {{ $users->links() }}
        </div>
    </div>
@endsection