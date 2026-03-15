<?php

declare(strict_types=1);

namespace App\Repositories;

use App\Models\WheelchairClient;
use App\Repositories\Interfaces\WheelchairRepositoryInterface;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Database\Eloquent\Collection;

class WheelchairRepository implements WheelchairRepositoryInterface
{
    public function findById(string $id): ?WheelchairClient
    {
        return WheelchairClient::with('client')->find($id);
    }

    public function getAllPaginated(int $perPage = 15): LengthAwarePaginator
    {
        return WheelchairClient::with('client')
            ->orderBy('created_at', 'desc')
            ->paginate($perPage);
    }

    public function getByClientId(string $clientId): Collection
    {
        return WheelchairClient::where('client_id', $clientId)->get();
    }

    public function create(array $data): WheelchairClient
    {
        return WheelchairClient::create($data);
    }

    public function update(string $id, array $data): bool
    {
        $wheelchair = WheelchairClient::find($id);
        if (!$wheelchair) {
            return false;
        }
        return $wheelchair->update($data);
    }

    public function delete(string $id): bool
    {
        $wheelchair = WheelchairClient::find($id);
        if (!$wheelchair) {
            return false;
        }
        return $wheelchair->delete();
    }
}
