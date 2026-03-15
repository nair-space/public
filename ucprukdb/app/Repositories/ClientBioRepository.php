<?php

declare(strict_types=1);

namespace App\Repositories;

use App\Models\ClientBio;
use App\Repositories\Interfaces\ClientBioRepositoryInterface;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Support\Facades\DB;

class ClientBioRepository implements ClientBioRepositoryInterface
{
    public function __construct(
        private readonly ClientBio $model
    ) {
    }

    public function findById(string $clientId): ?ClientBio
    {
        return $this->model
            ->with(['assessments', 'wheelchairs'])
            ->find($clientId);
    }

    public function findByNik(string $nik): ?ClientBio
    {
        return $this->model->where('nik', $nik)->first();
    }

    public function getPaginated(int $perPage = 15, array $filters = []): LengthAwarePaginator
    {
        $query = $this->model
            ->select([
                'client_id',
                'nama_lengkap',
                'tanggal_daftar',
                'provinsi',
                'kecamatan',
                'jenis_disabilitas',
                'status_difabel',
            ]);

        if (!empty($filters['provinsi'])) {
            $query->where('provinsi', $filters['provinsi']);
        }

        if (!empty($filters['jenis_disabilitas'])) {
            $query->where('jenis_disabilitas', $filters['jenis_disabilitas']);
        }

        if (!empty($filters['search'])) {
            $query->where('nama_lengkap', 'like', '%' . $filters['search'] . '%');
        }

        return $query->orderBy('tanggal_daftar', 'desc')->paginate($perPage);
    }

    public function getAll(): Collection
    {
        return $this->model->all();
    }

    public function create(array $data): ClientBio
    {
        return $this->model->create($data);
    }

    public function update(string $clientId, array $data): bool
    {
        return $this->model->where('client_id', $clientId)->update($data) > 0;
    }

    public function delete(string $clientId): bool
    {
        return $this->model->where('client_id', $clientId)->delete() > 0;
    }

    public function getCountByProvinsi(): array
    {
        return $this->model
            ->select('provinsi', DB::raw('COUNT(*) as total'))
            ->groupBy('provinsi')
            ->orderBy('total', 'desc')
            ->get()
            ->toArray();
    }

    public function getCountByDisabilitas(): array
    {
        return $this->model
            ->select('jenis_disabilitas', DB::raw('COUNT(*) as total'))
            ->groupBy('jenis_disabilitas')
            ->orderBy('total', 'desc')
            ->get()
            ->toArray();
    }
}
