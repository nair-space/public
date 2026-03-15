<?php

declare(strict_types=1);

namespace App\Repositories\Interfaces;

use App\Models\ClientBio;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Database\Eloquent\Collection;

interface ClientBioRepositoryInterface
{
    public function findById(string $clientId): ?ClientBio;

    public function findByNik(string $nik): ?ClientBio;

    public function getPaginated(int $perPage = 15, array $filters = []): LengthAwarePaginator;

    public function getAll(): Collection;

    public function create(array $data): ClientBio;

    public function update(string $clientId, array $data): bool;

    public function delete(string $clientId): bool;

    public function getCountByProvinsi(): array;

    public function getCountByDisabilitas(): array;
}
