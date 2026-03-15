@extends('layouts.app')

@section('page-title', 'Edit Data Klien')

@section('content')
    <div class="mb-6">
        <a href="{{ route('client-bio.index') }}" class="text-primary font-semibold hover:underline">
            ← Kembali ke Daftar Klien
        </a>
    </div>

    <div class="card max-w-4xl">
        <h2 class="text-2xl font-bold text-gray-900 mb-6">Edit: {{ $client->nama_lengkap }}</h2>

        <form action="{{ route('client-bio.update', $client->client_id) }}" method="POST" enctype="multipart/form-data">
            @csrf
            @method('PUT')

            <!-- Same form fields as create, with values from $client -->
            <div class="grid grid-cols-1 md:grid-cols-2 gap-4 mb-6">
                <div>
                    <label class="label">Nama Lengkap</label>
                    <input type="text" name="nama_lengkap" value="{{ old('nama_lengkap', $client->nama_lengkap) }}"
                        class="input" required>
                </div>
                <div>
                    <label class="label">Nama Panggilan</label>
                    <input type="text" name="nama_panggilan" value="{{ old('nama_panggilan', $client->nama_panggilan) }}"
                        class="input" required>
                </div>
                <div>
                    <label class="label">Nama Depan</label>
                    <input type="text" name="nama_depan" value="{{ old('nama_depan', $client->nama_depan) }}" class="input"
                        required>
                </div>
                <div>
                    <label class="label">Nama Belakang</label>
                    <input type="text" name="nama_belakang" value="{{ old('nama_belakang', $client->nama_belakang) }}"
                        class="input" required>
                </div>
                <div>
                    <label class="label">Tanggal Daftar</label>
                    <input type="date" name="tanggal_daftar"
                        value="{{ old('tanggal_daftar', $client->tanggal_daftar->format('Y-m-d')) }}" class="input"
                        required>
                </div>
                <div>
                    <label class="label">Source ID</label>
                    <input type="text" name="source_id" value="{{ old('source_id', $client->source_id) }}" class="input"
                        required>
                </div>
                <div>
                    <label class="label">NIK</label>
                    <input type="text" name="nik" value="{{ old('nik', $client->nik) }}" class="input" maxlength="20"
                        required>
                </div>
                <div>
                    <label class="label">Jenis Kelamin</label>
                    <select name="jenis_kelamin" class="input" required>
                        <option value="laki-laki" {{ old('jenis_kelamin', $client->jenis_kelamin) == 'laki-laki' ? 'selected' : '' }}>Laki-laki</option>
                        <option value="perempuan" {{ old('jenis_kelamin', $client->jenis_kelamin) == 'perempuan' ? 'selected' : '' }}>Perempuan</option>
                    </select>
                </div>
                <div>
                    <label class="label">Tanggal Lahir</label>
                    <input type="date" name="tanggal_lahir"
                        value="{{ old('tanggal_lahir', $client->tanggal_lahir->format('Y-m-d')) }}" class="input" required>
                </div>
                <div>
                    <label class="label">Provinsi</label>
                    <input type="text" name="provinsi" value="{{ old('provinsi', $client->provinsi) }}" class="input"
                        required>
                </div>
                <div>
                    <label class="label">Kecamatan</label>
                    <input type="text" name="kecamatan" value="{{ old('kecamatan', $client->kecamatan) }}" class="input"
                        required>
                </div>
                <div>
                    <label class="label">Kelurahan</label>
                    <input type="text" name="kelurahan" value="{{ old('kelurahan', $client->kelurahan) }}" class="input"
                        required>
                </div>
                <div>
                    <label class="label">Dusun</label>
                    <input type="text" name="dusun" value="{{ old('dusun', $client->dusun) }}" class="input" required>
                </div>
                <div>
                    <label class="label">Jenis Disabilitas</label>
                    <input type="text" name="jenis_disabilitas"
                        value="{{ old('jenis_disabilitas', $client->jenis_disabilitas) }}" class="input" required>
                </div>
                <div>
                    <label class="label">Status Aktivitas</label>
                    <input type="text" name="status_aktivitas"
                        value="{{ old('status_aktivitas', $client->status_aktivitas) }}" class="input" required>
                </div>
            </div>

            <div class="mb-6">
                <label class="label">Alamat</label>
                <textarea name="alamat" rows="3" class="input" required>{{ old('alamat', $client->alamat) }}</textarea>
            </div>

            <div class="mb-6">
                <label class="label">Foto Klien (WebP/JPEG/PNG, Max 5MB, Auto-compressed to < 50KB)</label>
                        <div class="flex items-center gap-4">
                            @if($client->photo_path)
                                <div class="w-[100px] h-[100px] rounded-2xl overflow-hidden border border-gray-200 shadow-sm">
                                    <img src="{{ $client->photo_url }}" alt="{{ $client->nama_lengkap }}"
                                        class="w-full h-full object-cover">
                                </div>
                            @endif
                            <input type="file" name="photo" class="input flex-1" accept="image/*">
                        </div>
                        @error('photo')
                            <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                        @enderror
            </div>

            <div class="grid grid-cols-2 md:grid-cols-5 gap-4 mb-6">
                @foreach(['status_difabel', 'status_asuransi', 'status_bpjs', 'ada_foto', 'salinan_kk', 'salinan_ktp', 'salinan_tagihanlistrik', 'salinan_slipgaji'] as $field)
                    <div>
                        <label class="label text-sm">{{ ucwords(str_replace('_', ' ', $field)) }}</label>
                        <select name="{{ $field }}" class="input" required>
                            <option value="tidak" {{ old($field, $client->$field) == 'tidak' ? 'selected' : '' }}>Tidak</option>
                            <option value="ya" {{ old($field, $client->$field) == 'ya' ? 'selected' : '' }}>Ya</option>
                        </select>
                    </div>
                @endforeach
            </div>

            <div class="flex gap-4">
                <button type="submit" class="btn-primary">Simpan Perubahan</button>
                <a href="{{ route('client-bio.index') }}" class="btn-outline">Batal</a>
            </div>
        </form>
    </div>
@endsection