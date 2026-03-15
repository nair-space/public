@extends('layouts.app')

@section('page-title', 'Edit User')

@section('content')
    <div class="mb-6">
        <a href="{{ route('users.index') }}" class="text-primary font-semibold hover:underline">
            ← Kembali ke Daftar User
        </a>
    </div>

    <div class="card max-w-2xl">
        <h2 class="text-2xl font-bold text-gray-900 mb-6">Edit: {{ $user->nama_lengkap }}</h2>

        <form action="{{ route('users.update', $user->user_id) }}" method="POST">
            @csrf
            @method('PUT')

            <div class="space-y-4">
                <div>
                    <label class="label">Username</label>
                    <input type="text" name="username" value="{{ old('username', $user->username) }}" class="input"
                        required>
                </div>

                <div>
                    <label class="label">Nama Lengkap</label>
                    <input type="text" name="nama_lengkap" value="{{ old('nama_lengkap', $user->nama_lengkap) }}"
                        class="input" required>
                </div>

                <div>
                    <label class="label">Jabatan</label>
                    <select name="jabatan" class="input" required>
                        <option value="administrator" {{ old('jabatan', $user->jabatan) == 'administrator' ? 'selected' : '' }}>Administrator</option>
                        <option value="staff" {{ old('jabatan', $user->jabatan) == 'staff' ? 'selected' : '' }}>Staff</option>
                        <option value="volunteer" {{ old('jabatan', $user->jabatan) == 'volunteer' ? 'selected' : '' }}>
                            Volunteer</option>
                    </select>
                </div>

                <div>
                    <label class="label">Email</label>
                    <input type="email" name="email" value="{{ old('email', $user->email) }}" class="input" required>
                </div>

                <div>
                    <label class="label">Nomor Telepon</label>
                    <input type="text" name="nomor_telfon" value="{{ old('nomor_telfon', $user->nomor_telfon) }}"
                        class="input" maxlength="16" required>
                </div>

                <div>
                    <label class="label">Password Baru (kosongkan jika tidak diubah)</label>
                    <input type="password" name="password" class="input">
                </div>

                <div>
                    <label class="label">Konfirmasi Password Baru</label>
                    <input type="password" name="password_confirmation" class="input">
                </div>

                <div>
                    <label class="label">Status</label>
                    <select name="status" class="input" required>
                        <option value="aktif" {{ old('status', $user->status) == 'aktif' ? 'selected' : '' }}>Aktif</option>
                        <option value="tidak aktif" {{ old('status', $user->status) == 'tidak aktif' ? 'selected' : '' }}>
                            Tidak Aktif</option>
                    </select>
                </div>
            </div>

            <div class="flex gap-4 mt-6">
                <button type="submit" class="btn-primary">Simpan Perubahan</button>
                <a href="{{ route('users.index') }}" class="btn-outline">Batal</a>
            </div>
        </form>
    </div>
@endsection