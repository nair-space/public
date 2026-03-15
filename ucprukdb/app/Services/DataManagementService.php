<?php

declare(strict_types=1);

namespace App\Services;

use App\Models\ClientBio;
use App\Models\WheelchairClient;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\Storage;
use Symfony\Component\HttpFoundation\StreamedResponse;

class DataManagementService
{
    /**
     * Export table data to CSV.
     */
    public function exportToCsv(string $table): StreamedResponse
    {
        $columns = Schema::getColumnListing($table);
        $data = DB::table($table)->get();

        return new StreamedResponse(function () use ($columns, $data) {
            $handle = fopen('php://output', 'w');

            // Add BOM for Excel compatibility
            fprintf($handle, chr(0xEF) . chr(0xBB) . chr(0xBF));

            fputcsv($handle, $columns);

            foreach ($data as $row) {
                fputcsv($handle, (array) $row);
            }

            fclose($handle);
        }, 200, [
            'Content-Type' => 'text/csv',
            'Content-Disposition' => "attachment; filename=\"export_{$table}_" . date('Ymd_His') . ".csv\"",
        ]);
    }

    /**
     * Import data from CSV.
     */
    public function importFromCsv(string $table, string $filePath): array
    {
        $handle = fopen($filePath, 'r');
        $header = fgetcsv($handle);
        $count = 0;
        $errors = [];

        while (($row = fgetcsv($handle)) !== false) {
            try {
                $data = array_combine($header, $row);
                DB::table($table)->updateOrInsert(
                    [$this->getPrimaryKey($table) => $data[$this->getPrimaryKey($table)]],
                    $data
                );
                $count++;
            } catch (\Exception $e) {
                $errors[] = "Row " . ($count + 2) . ": " . $e->getMessage();
            }
        }

        fclose($handle);
        return ['count' => $count, 'errors' => $errors];
    }

    /**
     * Export full database to SQL.
     */
    public function exportToSql(): StreamedResponse
    {
        $dbName = config('database.connections.mariadb.database');
        $username = config('database.connections.mariadb.username');
        $password = config('database.connections.mariadb.password');
        $host = config('database.connections.mariadb.host');

        return new StreamedResponse(function () use ($host, $username, $password, $dbName) {
            $passParam = $password ? "-p{$password}" : "";
            $command = "mysqldump -h {$host} -u {$username} {$passParam} {$dbName}";

            passthru($command);
        }, 200, [
            'Content-Type' => 'application/sql',
            'Content-Disposition' => "attachment; filename=\"backup_{$dbName}_" . date('Ymd_His') . ".sql\"",
        ]);
    }

    /**
     * Import database from SQL file.
     */
    public function importFromSql(string $filePath): void
    {
        $dbName = config('database.connections.mariadb.database');
        $username = config('database.connections.mariadb.username');
        $password = config('database.connections.mariadb.password');
        $host = config('database.connections.mariadb.host');

        $passParam = $password ? "-p{$password}" : "";
        $command = "mysql -h {$host} -u {$username} {$passParam} {$dbName} < " . escapeshellarg($filePath);

        exec($command, $output, $resultCode);

        if ($resultCode !== 0) {
            throw new \Exception("Gagal mengimpor SQL: " . implode("\n", $output));
        }
    }

    private function getPrimaryKey(string $table): string
    {
        return match ($table) {
            'client_bio' => 'client_id',
            'wheelchair_client' => 'kursiroda_id',
            'users' => 'user_id',
            default => 'id',
        };
    }
}
