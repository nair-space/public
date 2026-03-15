<?php

namespace App\Exports;

use App\Models\ClientBio;
use Maatwebsite\Excel\Concerns\FromQuery;
use Maatwebsite\Excel\Concerns\Exportable;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;

class ClientBioExport implements FromQuery, WithHeadings, WithMapping
{
    use Exportable;

    protected $columns;
    protected $startDate;
    protected $endDate;

    public function __construct(array $columns, ?string $startDate = null, ?string $endDate = null)
    {
        $this->columns = $columns;
        $this->startDate = $startDate;
        $this->endDate = $endDate;
    }

    public function query()
    {
        $query = ClientBio::query()->select($this->columns);

        if ($this->startDate) {
            $query->where('tanggal_daftar', '>=', $this->startDate);
        }

        if ($this->endDate) {
            $query->where('tanggal_daftar', '<=', $this->endDate);
        }

        return $query;
    }

    public function headings(): array
    {
        return array_map(function ($column) {
            return str_replace('_', ' ', ucwords($column, '_'));
        }, $this->columns);
    }

    public function map($client): array
    {
        $mappedData = [];
        foreach ($this->columns as $column) {
            $mappedData[] = $client->{$column};
        }
        return $mappedData;
    }
}
