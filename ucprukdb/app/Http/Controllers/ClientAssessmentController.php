<?php

declare(strict_types=1);

namespace App\Http\Controllers;

use App\Services\ClientAssessmentService;
use App\Services\ClientBioService;
use App\Http\Requests\ClientAssessmentRequest;
use Illuminate\Http\RedirectResponse;
use Illuminate\View\View;

class ClientAssessmentController extends Controller
{
    public function __construct(
        private readonly ClientAssessmentService $assessmentService,
        private readonly ClientBioService $clientService
    ) {
    }

    public function index(): View
    {
        $assessments = $this->assessmentService->getAllAssessments();
        return view('assessments.index', compact('assessments'));
    }

    public function create(): View
    {
        $clients = $this->clientService->getPaginatedClients(100);
        return view('assessments.create', compact('clients'));
    }

    public function store(ClientAssessmentRequest $request): RedirectResponse
    {
        $this->assessmentService->createAssessment($request->validated());
        return redirect()->route('client-assessments.index')->with('success', 'Data asesmen berhasil ditambahkan.');
    }

    public function show(string $id): View
    {
        $assessment = $this->assessmentService->getAssessment($id);
        if (!$assessment) {
            abort(404);
        }
        return view('assessments.show', compact('assessment'));
    }

    public function edit(string $id): View
    {
        $assessment = $this->assessmentService->getAssessment($id);
        if (!$assessment) {
            abort(404);
        }
        $clients = $this->clientService->getPaginatedClients(100);
        return view('assessments.edit', compact('assessment', 'clients'));
    }

    public function update(ClientAssessmentRequest $request, string $id): RedirectResponse
    {
        $updated = $this->assessmentService->updateAssessment($id, $request->validated());
        if (!$updated) {
            return redirect()->back()->with('error', 'Gagal memperbarui data asesmen.');
        }
        return redirect()->route('client-assessments.index')->with('success', 'Data asesmen berhasil diperbarui.');
    }

    public function destroy(string $id): RedirectResponse
    {
        $deleted = $this->assessmentService->deleteAssessment($id);
        if (!$deleted) {
            return redirect()->back()->with('error', 'Gagal menghapus data asesmen.');
        }
        return redirect()->route('client-assessments.index')->with('success', 'Data asesmen berhasil dihapus.');
    }
}
