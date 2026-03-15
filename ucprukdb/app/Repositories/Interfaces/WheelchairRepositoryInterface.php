<?php

declare(strict_types=1);

namespace App\Repositories\Interfaces;

use App\Models\WheelchairClient;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Database\Eloquent\Collection;

interface WheelchairRepositoryInterface
{
    public function findById(string $id): ?WheelchairClient;

    public function getAllPaginated(int $perPage = 15): LengthAwarePaginator;

    public function getByClientId(string $clientId): Collection;

    public function create(array $data): WheelchairClient;

    public function update(string $id, array $data): bool;

    public function delete(string $id): bool;
}
