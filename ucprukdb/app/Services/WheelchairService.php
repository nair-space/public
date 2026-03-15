<?php

declare(strict_types=1);

namespace App\Services;

use App\Repositories\Interfaces\WheelchairRepositoryInterface;
use App\Repositories\Interfaces\ClientBioRepositoryInterface;
use App\Models\WheelchairClient;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Support\Facades\Cache;

class WheelchairService
{
    public function __construct(
        private readonly WheelchairRepositoryInterface $wheelchairRepository,
        private readonly ClientBioRepositoryInterface $clientRepository
    ) {
    }

    public function getAllWheelchairs(int $perPage = 15): LengthAwarePaginator
    {
        return $this->wheelchairRepository->getAllPaginated($perPage);
    }

    public function getWheelchair(string $id): ?WheelchairClient
    {
        return $this->wheelchairRepository->findById($id);
    }

    public function createWheelchair(array $data): WheelchairClient
    {
        // Generate custom ID if not provided (WCH0000001 format)
        if (empty($data['kursiroda_id'])) {
            $lastId = WheelchairClient::orderBy('kursiroda_id', 'desc')->first()?->kursiroda_id;
            $number = $lastId ? (int) substr($lastId, 3) + 1 : 1;
            $data['kursiroda_id'] = 'WCH' . str_pad((string) $number, 7, '0', STR_PAD_LEFT);
        }

        // Auto-fill client details from client_bio if missing
        if (!empty($data['client_id'])) {
            $client = $this->clientRepository->findById($data['client_id']);
            if ($client) {
                $data['nik'] = $client->nik;
                $data['nama_lengkap'] = $client->nama_lengkap;
            }
        }

        $wheelchair = $this->wheelchairRepository->create($data);
        Cache::forget('dashboard_stats');

        return $wheelchair;
    }

    public function updateWheelchair(string $id, array $data): bool
    {
        $updated = $this->wheelchairRepository->update($id, $data);
        if ($updated) {
            Cache::forget('dashboard_stats');
        }
        return $updated;
    }

    public function deleteWheelchair(string $id): bool
    {
        $deleted = $this->wheelchairRepository->delete($id);
        if ($deleted) {
            Cache::forget('dashboard_stats');
        }
        return $deleted;
    }
}
