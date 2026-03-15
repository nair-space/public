<?php

declare(strict_types=1);

namespace App\Repositories;

use App\Models\ClientAssessment;
use App\Repositories\Interfaces\ClientAssessmentRepositoryInterface;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Support\Collection;

class ClientAssessmentRepository implements ClientAssessmentRepositoryInterface
{
    public function findById(string $id): ?ClientAssessment
    {
        return ClientAssessment::with('client')->find($id);
    }

    public function getAllPaginated(int $perPage = 15): LengthAwarePaginator
    {
        return ClientAssessment::with('client')
            ->orderBy('created_at', 'desc')
            ->paginate($perPage);
    }

    public function getByClientId(string $clientId): Collection
    {
        return ClientAssessment::where('client_id', $clientId)
            ->orderBy('created_at', 'desc')
            ->get();
    }

    public function create(array $data): ClientAssessment
    {
        return ClientAssessment::create($data);
    }

    public function update(string $id, array $data): bool
    {
        $assessment = ClientAssessment::find($id);
        if (!$assessment) {
            return false;
        }
        return $assessment->update($data);
    }

    public function delete(string $id): bool
    {
        $assessment = ClientAssessment::find($id);
        if (!$assessment) {
            return false;
        }
        return $assessment->delete();
    }
}
