@extends('layouts.app')

@section('page-title', 'Input Data Klien')

@section('content')
    <div class="card max-w-4xl">
        <h2 class="text-2xl font-bold text-gray-900 mb-6">Formulir Data Klien Baru</h2>

        <form action="{{ route('client-bio.store') }}" method="POST">
            @csrf

            <!-- Data Pendaftaran -->
            <div class="border-b border-gray-200 pb-6 mb-6">
                <h3 class="font-bold text-gray-900 mb-4">Data Pendaftaran</h3>
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <div>
                        <label class="label">Tanggal Daftar</label>
                        <input type="date" name="tanggal_daftar" value="{{ old('tanggal_daftar', date('Y-m-d')) }}"
                            class="input @error('tanggal_daftar') input-error @enderror" required>
                        @error('tanggal_daftar')<p class="text-red-600 text-sm mt-1">{{ $message }}</p>@enderror
                    </div>
                    <div>
                        <label class="label">Source ID</label>
                        <input type="text" name="source_id" value="{{ old('source_id') }}"
                            class="input @error('source_id') input-error @enderror" required>
                        @error('source_id')<p class="text-red-600 text-sm mt-1">{{ $message }}</p>@enderror
                    </div>
                </div>
            </div>

            <!-- Data Pribadi -->
            <div class="border-b border-gray-200 pb-6 mb-6">
                <h3 class="font-bold text-gray-900 mb-4">Data Pribadi</h3>
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <div>
                        <label class="label">NIK</label>
                        <input type="text" name="nik" value="{{ old('nik') }}"
                            class="input @error('nik') input-error @enderror" maxlength="20" required>
                        @error('nik')<p class="text-red-600 text-sm mt-1">{{ $message }}</p>@enderror
                    </div>
                    <div>
                        <label class="label">Jenis Kelamin</label>
                        <select name="jenis_kelamin" class="input @error('jenis_kelamin') input-error @enderror" required>
                            <option value="">Pilih...</option>
                            <option value="laki-laki" {{ old('jenis_kelamin') == 'laki-laki' ? 'selected' : '' }}>Laki-laki
                            </option>
                            <option value="perempuan" {{ old('jenis_kelamin') == 'perempuan' ? 'selected' : '' }}>Perempuan
                            </option>
                        </select>
                        @error('jenis_kelamin')<p class="text-red-600 text-sm mt-1">{{ $message }}</p>@enderror
                    </div>
                    <div>
                        <label class="label">Nama Depan</label>
                        <input type="text" name="nama_depan" value="{{ old('nama_depan') }}"
                            class="input @error('nama_depan') input-error @enderror" required>
                    </div>
                    <div>
                        <label class="label">Nama Belakang</label>
                        <input type="text" name="nama_belakang" value="{{ old('nama_belakang') }}"
                            class="input @error('nama_belakang') input-error @enderror" required>
                    </div>
                    <div>
                        <label class="label">Nama Lengkap</label>
                        <input type="text" name="nama_lengkap" value="{{ old('nama_lengkap') }}"
                            class="input @error('nama_lengkap') input-error @enderror" required>
                    </div>
                    <div>
                        <label class="label">Nama Panggilan</label>
                        <input type="text" name="nama_panggilan" value="{{ old('nama_panggilan') }}"
                            class="input @error('nama_panggilan') input-error @enderror" required>
                    </div>
                    <div>
                        <label class="label">Tanggal Lahir</label>
                        <input type="date" name="tanggal_lahir" value="{{ old('tanggal_lahir') }}"
                            class="input @error('tanggal_lahir') input-error @enderror" required>
                    </div>
                </div>
            </div>

            <!-- Alamat -->
            <div class="border-b border-gray-200 pb-6 mb-6">
                <h3 class="font-bold text-gray-900 mb-4">Alamat</h3>
                <div class="mb-4">
                    <label class="label">Alamat Lengkap</label>
                    <textarea name="alamat" rows="3" class="input @error('alamat') input-error @enderror"
                        required>{{ old('alamat') }}</textarea>
                </div>
                <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-4">
                    <div>
                        <label class="label">Dusun</label>
                        <input type="text" name="dusun" value="{{ old('dusun') }}" class="input" required>
                    </div>
                    <div>
                        <label class="label">Kelurahan</label>
                        <input type="text" name="kelurahan" value="{{ old('kelurahan') }}" class="input" required>
                    </div>
                    <div>
                        <label class="label">Kecamatan</label>
                        <input type="text" name="kecamatan" value="{{ old('kecamatan') }}" class="input" required>
                    </div>
                    <div>
                        <label class="label">Provinsi</label>
                        <input type="text" name="provinsi" value="{{ old('provinsi') }}" class="input" required>
                    </div>
                </div>
            </div>

            <!-- Status -->
            <div class="border-b border-gray-200 pb-6 mb-6">
                <h3 class="font-bold text-gray-900 mb-4">Status & Disabilitas</h3>
                <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4">
                    <div>
                        <label class="label">Status Difabel</label>
                        <select name="status_difabel" class="input" required>
                            <option value="ya" {{ old('status_difabel') == 'ya' ? 'selected' : '' }}>Ya</option>
                            <option value="tidak" {{ old('status_difabel') == 'tidak' ? 'selected' : '' }}>Tidak</option>
                        </select>
                    </div>
                    <div>
                        <label class="label">Jenis Disabilitas</label>
                        <input type="text" name="jenis_disabilitas" value="{{ old('jenis_disabilitas') }}" class="input"
                            required>
                    </div>
                    <div>
                        <label class="label">Status Aktivitas</label>
                        <input type="text" name="status_aktivitas" value="{{ old('status_aktivitas') }}" class="input"
                            required>
                    </div>
                    <div>
                        <label class="label">Status Asuransi</label>
                        <select name="status_asuransi" class="input" required>
                            <option value="tidak" {{ old('status_asuransi') == 'tidak' ? 'selected' : '' }}>Tidak</option>
                            <option value="ya" {{ old('status_asuransi') == 'ya' ? 'selected' : '' }}>Ya</option>
                        </select>
                    </div>
                    <div>
                        <label class="label">Status BPJS</label>
                        <select name="status_bpjs" class="input" required>
                            <option value="tidak" {{ old('status_bpjs') == 'tidak' ? 'selected' : '' }}>Tidak</option>
                            <option value="ya" {{ old('status_bpjs') == 'ya' ? 'selected' : '' }}>Ya</option>
                        </select>
                    </div>
                </div>
            </div>

            <!-- Dokumen -->
            <div class="border-b border-gray-200 pb-6 mb-6">
                <h3 class="font-bold text-gray-900 mb-4">Kelengkapan Dokumen</h3>
                <div class="grid grid-cols-2 md:grid-cols-3 lg:grid-cols-5 gap-4">
                    @foreach(['ada_foto' => 'Ada Foto', 'salinan_kk' => 'Salinan KK', 'salinan_ktp' => 'Salinan KTP', 'salinan_tagihanlistrik' => 'Tagihan Listrik', 'salinan_slipgaji' => 'Slip Gaji'] as $field => $label)
                        <div>
                            <label class="label text-sm">{{ $label }}</label>
                            <select name="{{ $field }}" class="input" required>
                                <option value="tidak" {{ old($field) == 'tidak' ? 'selected' : '' }}>Tidak</option>
                                <option value="ya" {{ old($field) == 'ya' ? 'selected' : '' }}>Ya</option>
                            </select>
                        </div>
                    @endforeach
                </div>
            </div>

            <!-- Info Tambahan -->
            <div class="mb-6">
                <label class="label">Informasi Tambahan</label>
                <textarea name="info_tambahan" rows="3" class="input">{{ old('info_tambahan') }}</textarea>
            </div>

            <!-- Submit -->
            <div class="flex gap-4">
                <button type="submit" class="btn-primary">Simpan Data Klien</button>
                <a href="{{ route('client-bio.index') }}" class="btn-outline">Batal</a>
            </div>
        </form>
    </div>
@endsection