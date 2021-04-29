<?php

namespace App\Exports;

use App\Models\Ram;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use Maatwebsite\Excel\Concerns\WithColumnWidths;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;
use Maatwebsite\Excel\Concerns\WithStyles;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;

class RamExport implements
    FromCollection,
    ShouldAutoSize,
    WithMapping,
    WithHeadings,
    WithStyles,
    WithColumnWidths
{
    /**
     * Instantiate a new controller instance
     *
     * @param  array $condition
     * @param  int $page
     * 
     * @return void
     */
    public function __construct(array $condition)
    {
        $this->condition = $condition;
    }

    /**
     * Get ram collection with condition
     * 
     * @return \Illuminate\Support\Collection
     */
    public function collection()
    {
        return (new Ram())->getRamWithOrder($this->condition);
    }

    /**
     * Set heading column
     * 
     * @return array
     */
    public function headings(): array
    {
        return [
            'ID',
            'Bộ nhớ trong (GB)',
        ];
    }

    /**
     * Set data column base on heading
     * 
     * @param object $ram
     *  
     * @return array
     */
    public function map($ram): array
    {
        return [
            $ram->id,
            $ram->name,
        ];
    }

    /**
     * Set width of column (auto size is not working)
     * 
     * @return array
     */
    public function columnWidths(): array
    {
        return [
            'B' => 20,
        ];
    }

    /**
     * Set style for column
     * 
     * @param \PhpOffice\PhpSpreadsheet\Worksheet\Worksheet $sheet
     */
    public function styles(Worksheet $sheet)
    {
        $sheet->getStyle('1')->getFont()->setBold(true);
        $sheet->getStyle('A1:F1')->getAlignment()->setHorizontal('center');
    }
}
