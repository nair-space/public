@extends('layouts.app')

@section('content')
    <div class="auth-container" style="max-width: 600px;">
        <div class="card">
            <h2 style="margin-bottom: 1.5rem;">Edit Trade</h2>

            <form action="{{ route('trades.update', $trade) }}" method="POST">
                @csrf
                @method('PUT')

                <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 1rem;">
                    <div class="form-group">
                        <label for="pair">Trading Pair</label>
                        <input type="text" id="pair" name="pair" placeholder="e.g. BTC/USDT" required
                            value="{{ old('pair', $trade->pair) }}">
                    </div>

                    <div class="form-group">
                        <label for="type">Type</label>
                        <select id="type" name="type" required>
                            <option value="buy" {{ old('type', $trade->type) == 'buy' ? 'selected' : '' }}>Buy (Long)</option>
                            <option value="sell" {{ old('type', $trade->type) == 'sell' ? 'selected' : '' }}>Sell (Short)
                            </option>
                        </select>
                    </div>
                </div>

                <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 1rem;">
                    <div class="form-group">
                        <label for="entry_price">Entry Price</label>
                        <input type="number" step="any" id="entry_price" name="entry_price" placeholder="0.00" required
                            value="{{ old('entry_price', $trade->entry_price) }}">
                    </div>

                    <div class="form-group">
                        <label for="size">Position Size</label>
                        <input type="number" step="any" id="size" name="size" placeholder="0.00" required
                            value="{{ old('size', $trade->size) }}">
                    </div>
                </div>

                <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 1rem;">
                    <div class="form-group">
                        <label for="status">Status</label>
                        <select id="status" name="status" required onchange="toggleExitPrice(this.value)">
                            <option value="open" {{ old('status', $trade->status) == 'open' ? 'selected' : '' }}>Open</option>
                            <option value="closed" {{ old('status', $trade->status) == 'closed' ? 'selected' : '' }}>Closed
                            </option>
                        </select>
                    </div>

                    <div class="form-group" id="exit_price_group">
                        <label for="exit_price">Exit Price</label>
                        <input type="number" step="any" id="exit_price" name="exit_price" placeholder="0.00"
                            value="{{ old('exit_price', $trade->exit_price) }}">
                    </div>
                </div>

                <div class="form-group">
                    <label for="trade_date">Trade Date & Time</label>
                    <input type="datetime-local" id="trade_date" name="trade_date" required
                        value="{{ old('trade_date', $trade->trade_date->format('Y-m-d\TH:i')) }}">
                </div>

                <div class="form-group">
                    <label for="notes">Notes/Reasoning</label>
                    <textarea id="notes" name="notes" rows="4"
                        placeholder="Why did you take this trade?">{{ old('notes', $trade->notes) }}</textarea>
                </div>

                <div style="display: flex; gap: 1rem; margin-top: 1rem;">
                    <button type="submit" class="btn" style="flex: 1;">Update Trade</button>
                    <a href="{{ route('trades.index') }}" class="btn btn-secondary"
                        style="flex: 1; text-align: center;">Cancel</a>
                </div>
            </form>
        </div>
    </div>

    <script>
        function toggleExitPrice(status) {
            const group = document.getElementById('exit_price_group');
            const input = document.getElementById('exit_price');
            if (status === 'open') {
                group.style.opacity = '0.5';
                input.disabled = true;
                if (!input.value) input.value = '';
            } else {
                group.style.opacity = '1';
                input.disabled = false;
            }
        }

        // Run on load
        toggleExitPrice(document.getElementById('status').value);
    </script>
@endsection