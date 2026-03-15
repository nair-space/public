@extends('layouts.app')

@section('page-title', 'Tambah User')

@section('content')
    <div class="mb-6">
        <a href="{{ route('users.index') }}" class="text-primary font-semibold hover:underline">
            ← Kembali ke Daftar User
        </a>
    </div>

    <div class="card max-w-2xl">
        <h2 class="text-2xl font-bold text-gray-900 mb-6">Tambah User Baru</h2>

        <form action="{{ route('users.store') }}" method="POST">
            @csrf

            <div class="space-y-4">
                <div>
                    <label class="label">Username</label>
                    <input type="text" name="username" value="{{ old('username') }}"
                        class="input @error('username') input-error @enderror" required>
                    @error('username')<p class="text-red-600 text-sm mt-1">{{ $message }}</p>@enderror
                </div>

                <div>
                    <label class="label">Nama Lengkap</label>
                    <input type="text" name="nama_lengkap" value="{{ old('nama_lengkap') }}"
                        class="input @error('nama_lengkap') input-error @enderror" required>
                </div>

                <div>
                    <label class="label">Jabatan</label>
                    <select name="jabatan" class="input @error('jabatan') input-error @enderror" required>
                        <option value="">Pilih jabatan...</option>
                        <option value="administrator" {{ old('jabatan') == 'administrator' ? 'selected' : '' }}>Administrator
                        </option>
                        <option value="staff" {{ old('jabatan') == 'staff' ? 'selected' : '' }}>Staff</option>
                        <option value="volunteer" {{ old('jabatan') == 'volunteer' ? 'selected' : '' }}>Volunteer</option>
                    </select>
                </div>

                <div>
                    <label class="label">Email</label>
                    <input type="email" name="email" value="{{ old('email') }}"
                        class="input @error('email') input-error @enderror" required>
                </div>

                <div>
                    <label class="label">Nomor Telepon</label>
                    <input type="text" name="nomor_telfon" value="{{ old('nomor_telfon') }}"
                        class="input @error('nomor_telfon') input-error @enderror" maxlength="16" required>
                </div>

                <div>
                    <label class="label">Password</label>
                    <input type="password" name="password" class="input @error('password') input-error @enderror" required>
                    @error('password')<p class="text-red-600 text-sm mt-1">{{ $message }}</p>@enderror
                </div>

                <div>
                    <label class="label">Konfirmasi Password</label>
                    <input type="password" name="password_confirmation" class="input" required>
                </div>

                <div>
                    <label class="label">Status</label>
                    <select name="status" class="input" required>
                        <option value="aktif" {{ old('status') == 'aktif' ? 'selected' : '' }}>Aktif</option>
                        <option value="tidak aktif" {{ old('status') == 'tidak aktif' ? 'selected' : '' }}>Tidak Aktif
                        </option>
                    </select>
                </div>
            </div>

            <div class="flex gap-4 mt-6">
                <button type="submit" class="btn-primary">Simpan User</button>
                <a href="{{ route('users.index') }}" class="btn-outline">Batal</a>
            </div>
        </form>
    </div>
@endsection