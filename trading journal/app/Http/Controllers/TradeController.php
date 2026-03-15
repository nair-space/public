<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class TradeController extends Controller
{
    public function index()
    {
        $trades = \App\Models\Trade::orderBy('trade_date', 'desc')->paginate(10);
        return view('trades.index', compact('trades'));
    }

    public function create()
    {
        return view('trades.create');
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'pair' => 'required|string',
            'type' => 'required|in:buy,sell',
            'entry_price' => 'required|numeric',
            'exit_price' => 'nullable|numeric',
            'size' => 'required|numeric',
            'status' => 'required|in:open,closed',
            'trade_date' => 'required|date',
            'notes' => 'nullable|string',
        ]);

        if ($validated['status'] === 'closed' && $validated['exit_price']) {
            $validated['pnl'] = ($validated['exit_price'] - $validated['entry_price']) * $validated['size'];
        }

        \App\Models\Trade::create($validated);

        return redirect()->route('trades.index')->with('success', 'Trade recorded successfully.');
    }

    public function edit(\App\Models\Trade $trade)
    {
        return view('trades.edit', compact('trade'));
    }

    public function update(Request $request, \App\Models\Trade $trade)
    {
        $validated = $request->validate([
            'pair' => 'required|string',
            'type' => 'required|in:buy,sell',
            'entry_price' => 'required|numeric',
            'exit_price' => 'nullable|numeric',
            'size' => 'required|numeric',
            'status' => 'required|in:open,closed',
            'trade_date' => 'required|date',
            'notes' => 'nullable|string',
        ]);

        if ($validated['status'] === 'closed' && $validated['exit_price']) {
            $validated['pnl'] = ($validated['exit_price'] - $validated['entry_price']) * $validated['size'];
        } else {
            $validated['pnl'] = 0;
            $validated['exit_price'] = null;
        }

        $trade->update($validated);

        return redirect()->route('trades.index')->with('success', 'Trade updated successfully.');
    }

    public function destroy(\App\Models\Trade $trade)
    {
        $trade->delete();
        return redirect()->route('trades.index')->with('success', 'Trade deleted successfully.');
    }
}
