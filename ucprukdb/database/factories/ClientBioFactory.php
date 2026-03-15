<?php

namespace Database\Factories;

use App\Models\ClientBio;
use Illuminate\Database\Eloquent\Factories\Factory;

class ClientBioFactory extends Factory
{
    protected $model = ClientBio::class;

    public function definition(): array
    {
        $firstName = $this->faker->firstName();
        $lastName = $this->faker->lastName();

        return [
            'client_id' => 'CLN' . strtoupper($this->faker->unique()->bothify('??#####')),
            'tanggal_daftar' => $this->faker->dateTimeBetween('-2 years', 'now'),
            'source_id' => $this->faker->bothify('SRC###'),
            'nik' => $this->faker->numerify('################'),
            'nama_depan' => $firstName,
            'nama_belakang' => $lastName,
            'nama_lengkap' => "$firstName $lastName",
            'nama_panggilan' => $firstName,
            'jenis_kelamin' => $this->faker->randomElement(['laki-laki', 'perempuan']),
            'tanggal_lahir' => $this->faker->dateTimeBetween('-60 years', '-5 years'),
            'alamat' => $this->faker->address(),
            'dusun' => $this->faker->streetName(),
            'kecamatan' => $this->faker->city(),
            'kelurahan' => $this->faker->cityPrefix(),
            'provinsi' => $this->faker->randomElement(['Bali', 'Jawa Barat', 'Jawa Tengah', 'Jawa Timur', 'DIY Yogyakarta']),
            'status_asuransi' => $this->faker->randomElement(['ya', 'tidak']),
            'nama_asuransi' => $this->faker->company(),
            'nomor_asuransi' => $this->faker->numerify('##########'),
            'status_bpjs' => $this->faker->randomElement(['ya', 'tidak']),
            'status_difabel' => 'ya',
            'dari_klinik' => $this->faker->company(),
            'jenis_disabilitas' => $this->faker->randomElement(['Cerebral Palsy', 'Paraplegia', 'Amputasi', 'Spina Bifida']),
            'status_aktivitas' => $this->faker->randomElement(['Sekolah', 'Bekerja', 'Di Rumah']),
            'jenis_sekolah' => $this->faker->randomElement(['SLB', 'Inklusi', 'Tidak Sekolah']),
            'ada_foto' => $this->faker->randomElement(['ya', 'tidak']),
            'salinan_kk' => $this->faker->randomElement(['ya', 'tidak']),
            'salinan_ktp' => $this->faker->randomElement(['ya', 'tidak']),
            'salinan_tagihanlistrik' => $this->faker->randomElement(['ya', 'tidak']),
            'salinan_slipgaji' => $this->faker->randomElement(['ya', 'tidak']),
            'info_tambahan' => $this->faker->sentence(),
        ];
    }
}
