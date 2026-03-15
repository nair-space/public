<?php

declare(strict_types=1);

namespace App\Services;

use App\Repositories\Interfaces\UserRepositoryInterface;
use App\Models\User;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

class UserService
{
    public function __construct(
        private readonly UserRepositoryInterface $userRepository
    ) {
    }

    public function authenticate(string $username, string $password): ?User
    {
        $user = $this->userRepository->findByUsername($username);

        if (!$user || !$user->isActive()) {
            return null;
        }

        if (!Hash::check($password, $user->password)) {
            return null;
        }

        return $user;
    }

    public function getUser(string $userId): ?User
    {
        return $this->userRepository->findById($userId);
    }

    public function getPaginatedUsers(int $perPage = 15): LengthAwarePaginator
    {
        return $this->userRepository->getPaginated($perPage);
    }

    public function createUser(array $data): User
    {
        // Generate user_id if not provided
        if (empty($data['user_id'])) {
            $data['user_id'] = $this->generateUserId();
        }

        // Hash password with Argon2id
        $data['password'] = Hash::make($data['password'], ['driver' => 'argon2id']);

        return $this->userRepository->create($data);
    }

    public function updateUser(string $userId, array $data): bool
    {
        // Hash password if provided
        if (!empty($data['password'])) {
            $data['password'] = Hash::make($data['password'], ['driver' => 'argon2id']);
        }

        return $this->userRepository->update($userId, $data);
    }

    public function deleteUser(string $userId): bool
    {
        return $this->userRepository->delete($userId);
    }

    private function generateUserId(): string
    {
        return 'USR' . strtoupper(Str::random(7));
    }
}
