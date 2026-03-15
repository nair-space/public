<?php

declare(strict_types=1);

namespace App\Http\Controllers;

use App\Http\Requests\ClientBioRequest;
use App\Services\ClientBioService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class ClientBioController extends Controller
{
    public function __construct(
        private readonly ClientBioService $clientBioService
    ) {
    }

    /**
     * Display a listing of clients.
     */
    public function index(Request $request): View
    {
        $filters = $request->only(['provinsi', 'jenis_disabilitas', 'search']);

        return view('client-bio.index', [
            'clients' => $this->clientBioService->getPaginatedClients(15, $filters),
            'filters' => $filters,
        ]);
    }

    /**
     * Show the form for creating a new client.
     */
    public function create(): View
    {
        return view('client-bio.create');
    }

    /**
     * Store a newly created client.
     */
    public function store(ClientBioRequest $request): RedirectResponse
    {
        $this->clientBioService->createClient($request->validated());

        return redirect()
            ->route('client-bio.index')
            ->with('success', 'Data klien berhasil ditambahkan.');
    }

    /**
     * Display the specified client.
     */
    public function show(string $clientId): View
    {
        $client = $this->clientBioService->getClient($clientId);

        if (!$client) {
            abort(404);
        }

        return view('client-bio.show', compact('client'));
    }

    /**
     * Show the form for editing the specified client.
     */
    public function edit(string $clientId): View
    {
        $client = $this->clientBioService->getClient($clientId);

        if (!$client) {
            abort(404);
        }

        return view('client-bio.edit', compact('client'));
    }

    /**
     * Update the specified client.
     */
    public function update(ClientBioRequest $request, string $clientId): RedirectResponse
    {
        $this->clientBioService->updateClient($clientId, $request->validated());

        return redirect()
            ->route('client-bio.index')
            ->with('success', 'Data klien berhasil diperbarui.');
    }

    /**
     * Remove the specified client.
     */
    public function destroy(string $clientId): RedirectResponse
    {
        $this->clientBioService->deleteClient($clientId);

        return redirect()
            ->route('client-bio.index')
            ->with('success', 'Data klien berhasil dihapus.');
    }
}
