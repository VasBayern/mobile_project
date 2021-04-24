<?php

namespace App\Exports;

use App\Models\Rom;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use Maatwebsite\Excel\Concerns\WithColumnWidths;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;
use Maatwebsite\Excel\Concerns\WithStyles;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;

class RomExport implements
    FromCollection,
    ShouldAutoSize,
    WithMapping,
    WithHeadings,
    WithStyles,
    WithColumnWidths
{
    /**
     * Get rom collection with condition
     * 
     * @return \Illuminate\Support\Collection
     */
    public function collection()
    {
        return Rom::all();
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
            'Dung lượng (GB)',
        ];
    }

    /**
     * Set data column base on heading
     * 
     * @param object $rom
     *  
     * @return array
     */
    public function map($rom): array
    {
        return [
            $rom->id,
            $rom->name,
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
