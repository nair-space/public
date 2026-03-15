<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class DashboardController extends Controller
{
    public function index()
    {
        $trades = \App\Models\Trade::orderBy('trade_date', 'desc')->get();

        $totalPnl = $trades->sum('pnl');
        $winRate = $trades->where('pnl', '>', 0)->count() / ($trades->where('status', 'closed')->count() ?: 1) * 100;
        $totalTrades = $trades->count();

        // Data for chart (cumulative PnL over time)
        $chartData = $trades->where('status', 'closed')->sortBy('trade_date')->values()->map(function ($trade, $index) use ($trades) {
            static $cumulativePnl = 0;
            $cumulativePnl += $trade->pnl;
            return [
                'date' => $trade->trade_date->format('Y-m-d'),
                'pnl' => $cumulativePnl
            ];
        });

        return view('dashboard.index', compact('trades', 'totalPnl', 'winRate', 'totalTrades', 'chartData'));
    }
}
