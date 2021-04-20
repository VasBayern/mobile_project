<?php

namespace App\Exports;

use App\Models\Category;
use Illuminate\Contracts\Support\Responsable;
use Maatwebsite\Excel\Concerns\Exportable;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\FromQuery;
use Maatwebsite\Excel\Concerns\WithCustomStartCell;
use Maatwebsite\Excel\Facades\Excel;

class CategoryExport implements FromCollection, WithCustomStartCell, Responsable
{
    use Exportable;

    /**
     * It's required to define the fileName within
     * the export class when making use of Responsable.
     */
    private $fileName = 'categories.xlsx';

    /**
     * Optional Writer Type
     */
    private $writerType = \Maatwebsite\Excel\Excel::XLSX;

    /**
     * Optional headers
     */
    private $headers = [
        'Content-Type' => 'text/csv',
    ];

    public function forYear(int $year = 2021)
    {
        $this->year = $year;
    }

    // public function query()
    // {
    //     return Category::query()->whereYear('created_at', $this->year);
    // }

    /**
     * @return \Illuminate\Support\Collection
     */
    public function collection()
    {
        return Category::all();
    }

    public function startCell(): string
    {
        return 'A3';
    }

    public function headings(): array
    {
        return [
            '#',
            'Name',
            'Home',
            'Created At'
        ];
    }

    public function map($category): array
    {
        return [
            $category->id,
            $category->name,
            $category->home,
            $category->created_at
        ];
    }
}
