@extends('layouts.app')

@section('content')
    <div class="dashboard-header"
        style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 2rem;">
        <div>
            <h1 style="font-size: 2.5rem;">Trade Log</h1>
            <p class="text-muted">History of all your trading activities.</p>
        </div>
        @if(Auth::user()->is_admin)
            <a href="{{ route('trades.create') }}" class="btn">+ New Trade</a>
        @endif
    </div>

    <div class="card">
        <div class="table-container">
            <table>
                <thead>
                    <tr>
                        <th>Date</th>
                        <th>Pair</th>
                        <th>Type</th>
                        <th>Entry</th>
                        <th>Exit</th>
                        <th>Size</th>
                        <th>PnL</th>
                        <th>Status</th>
                        @if(Auth::user()->is_admin)
                            <th>Actions</th>
                        @endif
                    </tr>
                </thead>
                <tbody>
                    @foreach($trades as $trade)
                        <tr>
                            <td>{{ $trade->trade_date->format('M d, Y H:i') }}</td>
                            <td style="font-weight: 600;">{{ $trade->pair }}</td>
                            <td><span class="badge badge-{{ $trade->type }}">{{ $trade->type }}</span></td>
                            <td>{{ number_format($trade->entry_price, 2) }}</td>
                            <td>{{ $trade->exit_price ? number_format($trade->exit_price, 2) : '-' }}</td>
                            <td>{{ number_format($trade->size, 4) }}</td>
                            <td><span
                                    style="color: {{ $trade->pnl >= 0 ? 'var(--success)' : 'var(--error)' }}; font-weight: 600;">{{ $trade->pnl != 0 ? ($trade->pnl >= 0 ? '+' : '') . number_format($trade->pnl, 2) : '-' }}</span>
                            </td>
                            <td><span class="badge badge-{{ $trade->status }}">{{ $trade->status }}</span></td>
                            @if(Auth::user()->is_admin)
                                <td>
                                    <div style="display: flex; gap: 0.5rem;">
                                        <a href="{{ route('trades.edit', $trade) }}" class="btn-logout"
                                            style="text-decoration: none; color: var(--primary);">Edit</a>
                                        <form action="{{ route('trades.destroy', $trade) }}" method="POST"
                                            onsubmit="return confirm('Are you sure?')">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="btn-logout" style="color: var(--error);">Del</button>
                                        </form>
                                    </div>
                                </td>
                            @endif
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>

        <div style="margin-top: 2rem;">
            {{ $trades->links() }}
        </div>
    </div>
@endsection