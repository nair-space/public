<?php

declare(strict_types=1);

namespace App\Http\Controllers;

use App\Services\DashboardService;
use Illuminate\View\View;

class DashboardController extends Controller
{
    public function __construct(
        private readonly DashboardService $dashboardService
    ) {
    }

    /**
     * Show main dashboard.
     */
    public function index(): View
    {
        return view('dashboard.index', [
            'stats' => $this->dashboardService->getStats(),
            'recentClients' => $this->dashboardService->getRecentClients(),
        ]);
    }

    /**
     * Show data distribution charts.
     */
    public function sebaranData(): View
    {
        return view('dashboard.sebaran-data', [
            'byProvinsi' => $this->dashboardService->getClientsByProvinsi(),
            'byDisabilitas' => $this->dashboardService->getClientsByDisabilitas(),
        ]);
    }
}
