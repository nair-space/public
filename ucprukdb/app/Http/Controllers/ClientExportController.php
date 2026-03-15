<?php

declare(strict_types=1);

namespace App\Http\Controllers;

use App\Exports\ClientBioExport;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Schema;
use Maatwebsite\Excel\Facades\Excel;
use Symfony\Component\HttpFoundation\BinaryFileResponse;
use Illuminate\View\View;

class ClientExportController extends Controller
{
    public function index(): View
    {
        $columns = Schema::getColumnListing('client_bio');

        // Remove timestamps from default view if needed, or keeping all
        // Excluding 'timestamps' and 'photo_path' if they aren't useful in excel
        $excludedColumns = ['created_at', 'updated_at'];
        $columns = array_filter($columns, fn($col) => !in_array($col, $excludedColumns));

        return view('data-management.export-client', compact('columns'));
    }

    public function export(Request $request): BinaryFileResponse
    {
        $request->validate([
            'columns' => 'required|array|min:1',
            'columns.*' => 'string',
            'start_date' => 'nullable|date',
            'end_date' => 'nullable|date|after_or_equal:start_date'
        ]);

        $selectedColumns = $request->input('columns');
        $startDate = $request->input('start_date');
        $endDate = $request->input('end_date');
        $fileName = 'clients_export_' . date('Ymd_His') . '.xlsx';

        return Excel::download(new ClientBioExport($selectedColumns, $startDate, $endDate), $fileName);
    }
}
