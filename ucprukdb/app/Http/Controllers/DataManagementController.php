<?php

declare(strict_types=1);

namespace App\Http\Controllers;

use App\Services\DataManagementService;
use Illuminate\Http\Request;
use Illuminate\Http\RedirectResponse;
use Illuminate\View\View;
use Symfony\Component\HttpFoundation\StreamedResponse;

class DataManagementController extends Controller
{
    public function __construct(
        private readonly DataManagementService $dataManagementService
    ) {
    }

    public function index(): View
    {
        return view('data-management.index');
    }

    public function exportCsv(Request $request): StreamedResponse
    {
        $table = $request->input('table', 'client_bio');
        return $this->dataManagementService->exportToCsv($table);
    }

    public function importCsv(Request $request): RedirectResponse
    {
        $request->validate([
            'table' => 'required|string|in:client_bio,wheelchair_client',
            'file' => 'required|file|mimes:csv,txt',
        ]);

        $path = $request->file('file')->getRealPath();
        $result = $this->dataManagementService->importFromCsv($request->input('table'), $path);

        if (!empty($result['errors'])) {
            return redirect()->back()->with('error', 'Beberapa baris gagal diimpor: ' . implode(', ', array_slice($result['errors'], 0, 3)));
        }

        return redirect()->back()->with('success', "{$result['count']} data berhasil diimpor.");
    }

    public function exportSql(): StreamedResponse
    {
        return $this->dataManagementService->exportToSql();
    }

    public function importSql(Request $request): RedirectResponse
    {
        $request->validate([
            'file' => 'required|file', // mimes:sql often fails due to mime detection issues
        ]);

        $path = $request->file('file')->getRealPath();

        try {
            $this->dataManagementService->importFromSql($path);
            return redirect()->back()->with('success', 'Database berhasil dipulihkan dari file SQL.');
        } catch (\Exception $e) {
            return redirect()->back()->with('error', 'Gagal impor SQL: ' . $e->getMessage());
        }
    }
}
