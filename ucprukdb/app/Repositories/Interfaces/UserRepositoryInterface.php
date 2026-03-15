<?php

declare(strict_types=1);

namespace App\Repositories\Interfaces;

use App\Models\User;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;

interface UserRepositoryInterface
{
    public function findById(string $userId): ?User;

    public function findByUsername(string $username): ?User;

    public function getPaginated(int $perPage = 15): LengthAwarePaginator;

    public function create(array $data): User;

    public function update(string $userId, array $data): bool;

    public function delete(string $userId): bool;

    public function getActiveUsers(): \Illuminate\Database\Eloquent\Collection;
}
