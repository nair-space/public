<?php

declare(strict_types=1);

namespace App\Http\Controllers;

use App\Services\WheelchairService;
use App\Services\ClientBioService;
use App\Http\Requests\WheelchairRequest;
use Illuminate\Http\RedirectResponse;
use Illuminate\View\View;

class WheelchairController extends Controller
{
    public function __construct(
        private readonly WheelchairService $wheelchairService,
        private readonly ClientBioService $clientService
    ) {
    }

    public function index(): View
    {
        $wheelchairs = $this->wheelchairService->getAllWheelchairs();
        return view('wheelchairs.index', compact('wheelchairs'));
    }

    public function create(): View
    {
        // Get all clients for the dropdown (limiting to 100 for simplicity in this implementation)
        $clients = $this->clientService->getPaginatedClients(100);
        return view('wheelchairs.create', compact('clients'));
    }

    public function store(WheelchairRequest $request): RedirectResponse
    {
        $this->wheelchairService->createWheelchair($request->validated());
        return redirect()->route('wheelchairs.index')->with('success', 'Data kursi roda berhasil ditambahkan.');
    }

    public function show(string $id): View
    {
        $wheelchair = $this->wheelchairService->getWheelchair($id);
        if (!$wheelchair) {
            abort(404);
        }
        return view('wheelchairs.show', compact('wheelchair'));
    }

    public function edit(string $id): View
    {
        $wheelchair = $this->wheelchairService->getWheelchair($id);
        if (!$wheelchair) {
            abort(404);
        }
        $clients = $this->clientService->getPaginatedClients(100);
        return view('wheelchairs.edit', compact('wheelchair', 'clients'));
    }

    public function update(WheelchairRequest $request, string $id): RedirectResponse
    {
        $updated = $this->wheelchairService->updateWheelchair($id, $request->validated());
        if (!$updated) {
            return redirect()->back()->with('error', 'Gagal memperbarui data kursi roda.');
        }
        return redirect()->route('wheelchairs.index')->with('success', 'Data kursi roda berhasil diperbarui.');
    }

    public function destroy(string $id): RedirectResponse
    {
        $deleted = $this->wheelchairService->deleteWheelchair($id);
        if (!$deleted) {
            return redirect()->back()->with('error', 'Gagal menghapus data kursi roda.');
        }
        return redirect()->route('wheelchairs.index')->with('success', 'Data kursi roda berhasil dihapus.');
    }
}
