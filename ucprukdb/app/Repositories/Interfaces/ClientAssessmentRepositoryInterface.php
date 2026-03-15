<?php

declare(strict_types=1);

namespace App\Repositories\Interfaces;

use App\Models\ClientAssessment;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Support\Collection;

interface ClientAssessmentRepositoryInterface
{
    public function findById(string $id): ?ClientAssessment;
    public function getAllPaginated(int $perPage = 15): LengthAwarePaginator;
    public function getByClientId(string $clientId): Collection;
    public function create(array $data): ClientAssessment;
    public function update(string $id, array $data): bool;
    public function delete(string $id): bool;
}
