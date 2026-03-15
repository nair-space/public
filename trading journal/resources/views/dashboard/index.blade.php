@extends('layouts.app')

@section('content')
<div class="dashboard-header" style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 2rem;">
    <div>
        <h1 style="font-size: 2.5rem;">Performance Dashboard</h1>
        <p class="text-muted">Welcome back, {{ Auth::user()->name }}. Here's how your trades are performing.</p>
    </div>
    @if(Auth::user()->is_admin)
        <a href="{{ route('trades.create') }}" class="btn">+ Log New Trade</a>
    @endif
</div>

<div class="stats-grid">
    <div class="stat-card">
        <div class="label">Total Profit/Loss</div>
        <div class="value {{ $totalPnl >= 0 ? 'positive' : 'negative' }}">${{ number_format($totalPnl, 2) }}</div>
    </div>
    <div class="stat-card">
        <div class="label">Win Rate</div>
        <div class="value">{{ number_format($winRate, 1) }}%</div>
    </div>
    <div class="stat-card">
        <div class="label">Total Trades</div>
        <div class="value">{{ $totalTrades }}</div>
    </div>
    <div class="stat-card">
        <div class="label">Account Status</div>
        <div class="value" style="color: var(--primary)">{{ Auth::user()->is_admin ? 'Admin' : 'Read-only' }}</div>
    </div>
</div>

<div class="card">
    <h3 style="margin-bottom: 1.5rem;">Equity Growth</h3>
    <div class="chart-container">
        <canvas id="performanceChart"></canvas>
    </div>
</div>

<div class="card">
    <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 1.5rem;">
        <h3>Recent Trades</h3>
        <a href="{{ route('trades.index') }}" style="color: var(--primary); text-decoration: none; font-size: 0.9rem;">View All Trades &rarr;</a>
    </div>
    
    <div class="table-container">
        <table>
            <thead>
                <tr>
                    <th>Date</th>
                    <th>Pair</th>
                    <th>Type</th>
                    <th>Entry</th>
                    <th>Exit</th>
                    <th>PnL</th>
                    <th>Status</th>
                </tr>
            </thead>
            <tbody>
                @foreach($trades->take(5) as $trade)
                <tr>
                    <td>{{ $trade->trade_date->format('M d, Y') }}</td>
                    <td style="font-weight: 600;">{{ $trade->pair }}</td>
                    <td><span class="badge badge-{{ $trade->type }}">{{ $trade->type }}</span></td>
                    <td>{{ number_format($trade->entry_price, 2) }}</td>
                    <td>{{ $trade->exit_price ? number_format($trade->exit_price, 2) : '-' }}</td>
                    <td><span style="color: {{ $trade->pnl >= 0 ? 'var(--success)' : 'var(--error)' }}; font-weight: 600;">{{ $trade->pnl != 0 ? ($trade->pnl >= 0 ? '+' : '') . number_format($trade->pnl, 2) : '-' }}</span></td>
                    <td><span class="badge badge-{{ $trade->status }}">{{ $trade->status }}</span></td>
                </tr>
                @endforeach
            </tbody>
        </table>
    </div>
</div>

<script>
    document.addEventListener('DOMContentLoaded', function() {
        const ctx = document.getElementById('performanceChart').getContext('2d');
        const chartData = @json($chartData);
        
        if (typeof Chart === 'undefined') {
            console.error('Chart.js not loaded!');
            return;
        }

        new Chart(ctx, {
            type: 'line',
            data: {
                labels: chartData.map(d => d.date),
                datasets: [{
                    label: 'Cumulative PnL',
                    data: chartData.map(d => d.pnl),
                    borderColor: '#6366f1',
                    backgroundColor: 'rgba(99, 102, 241, 0.1)',
                    borderWidth: 3,
                    tension: 0.4,
                    fill: true,
                    pointBackgroundColor: '#6366f1',
                    pointBorderColor: '#fff',
                    pointHoverRadius: 6
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                plugins: {
                    legend: {
                        display: false
                    },
                    tooltip: {
                        mode: 'index',
                        intersect: false,
                        backgroundColor: '#1e293b',
                        titleColor: '#94a3b8',
                        bodyColor: '#f8fafc',
                        borderColor: 'rgba(255,255,255,0.1)',
                        borderWidth: 1,
                        padding: 12,
                        displayColors: false,
                        callbacks: {
                            label: function(context) {
                                return '$ ' + context.parsed.y.toLocaleString();
                            }
                        }
                    }
                },
                scales: {
                    x: {
                        grid: {
                            display: false,
                            drawBorder: false
                        },
                        ticks: {
                            color: '#64748b',
                            maxRotation: 0,
                            autoSkip: true,
                            maxTicksLimit: 10
                        }
                    },
                    y: {
                        grid: {
                            color: 'rgba(255,255,255,0.05)',
                            drawBorder: false
                        },
                        ticks: {
                            color: '#64748b',
                            callback: function(value) {
                                return '$' + value;
                            }
                        }
                    }
                }
            }
        });
    });
</script>
@endsection