<?php

declare(strict_types=1);

namespace App\Repositories;

use App\Models\User;
use App\Repositories\Interfaces\UserRepositoryInterface;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Database\Eloquent\Collection;

class UserRepository implements UserRepositoryInterface
{
    public function __construct(
        private readonly User $model
    ) {
    }

    public function findById(string $userId): ?User
    {
        return $this->model->find($userId);
    }

    public function findByUsername(string $username): ?User
    {
        return $this->model->where('username', $username)->first();
    }

    public function getPaginated(int $perPage = 15): LengthAwarePaginator
    {
        return $this->model
            ->select(['user_id', 'username', 'nama_lengkap', 'jabatan', 'email', 'status'])
            ->orderBy('nama_lengkap')
            ->paginate($perPage);
    }

    public function create(array $data): User
    {
        return $this->model->create($data);
    }

    public function update(string $userId, array $data): bool
    {
        return $this->model->where('user_id', $userId)->update($data) > 0;
    }

    public function delete(string $userId): bool
    {
        return $this->model->where('user_id', $userId)->delete() > 0;
    }

    public function getActiveUsers(): Collection
    {
        return $this->model->where('status', 'aktif')->get();
    }
}
