<?php

declare(strict_types=1);

namespace App\Services;

use App\Repositories\Interfaces\ClientBioRepositoryInterface;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;

class DashboardService
{
    private const CACHE_TTL = 3600;

    public function __construct(
        private readonly ClientBioRepositoryInterface $clientBioRepository
    ) {
    }

    public function getStats(): array
    {
        return Cache::remember('dashboard_stats', self::CACHE_TTL, function () {
            return [
                'total_clients' => DB::table('client_bio')->count(),
                'total_assessments' => DB::table('client_assessment')->count(),
                'total_wheelchairs' => DB::table('wheelchair_client')->count(),
                'clients_this_month' => DB::table('client_bio')
                    ->whereMonth('tanggal_daftar', now()->month)
                    ->whereYear('tanggal_daftar', now()->year)
                    ->count(),
            ];
        });
    }

    public function getClientsByProvinsi(): array
    {
        return $this->clientBioRepository->getCountByProvinsi();
    }

    public function getClientsByDisabilitas(): array
    {
        return $this->clientBioRepository->getCountByDisabilitas();
    }

    public function getRecentClients(int $limit = 5): array
    {
        return DB::table('client_bio')
            ->select(['client_id', 'nama_lengkap', 'tanggal_daftar', 'provinsi'])
            ->orderBy('tanggal_daftar', 'desc')
            ->limit($limit)
            ->get()
            ->toArray();
    }
}
