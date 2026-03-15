<?php

declare(strict_types=1);

namespace App\Services;

use App\Models\ClientAssessment;
use App\Repositories\Interfaces\ClientAssessmentRepositoryInterface;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Support\Facades\Cache;

class ClientAssessmentService
{
    public function __construct(
        private readonly ClientAssessmentRepositoryInterface $assessmentRepository
    ) {
    }

    public function getAllAssessments(int $perPage = 15): LengthAwarePaginator
    {
        return $this->assessmentRepository->getAllPaginated($perPage);
    }

    public function getAssessment(string $id): ?ClientAssessment
    {
        return $this->assessmentRepository->findById($id);
    }

    public function createAssessment(array $data): ClientAssessment
    {
        if (empty($data['client_basic_assessment_id'])) {
            $lastId = ClientAssessment::orderBy('client_basic_assessment_id', 'desc')->first()?->client_basic_assessment_id;
            $number = $lastId ? (int) substr($lastId, 3) + 1 : 1;
            $data['client_basic_assessment_id'] = 'ASM' . str_pad((string) $number, 7, '0', STR_PAD_LEFT);
        }

        $assessment = $this->assessmentRepository->create($data);
        Cache::forget('dashboard_stats');

        return $assessment;
    }

    public function updateAssessment(string $id, array $data): bool
    {
        $updated = $this->assessmentRepository->update($id, $data);
        if ($updated) {
            Cache::forget('dashboard_stats');
        }
        return $updated;
    }

    public function deleteAssessment(string $id): bool
    {
        $deleted = $this->assessmentRepository->delete($id);
        if ($deleted) {
            Cache::forget('dashboard_stats');
        }
        return $deleted;
    }
}
