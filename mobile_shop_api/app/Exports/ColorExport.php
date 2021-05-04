<?php

namespace App\Exports;

use App\Models\Color;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use Maatwebsite\Excel\Concerns\WithColumnWidths;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;
use Maatwebsite\Excel\Concerns\WithStyles;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;

class ColorExport implements
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
     * 
     * @return void
     */
    public function __construct(array $condition)
    {
        $this->condition = $condition;
    }

    /**
     * Get color collection with condition
     * 
     * @return \Illuminate\Support\Collection
     */
    public function collection()
    {
        return (new Color())->getColorWithOrder($this->condition);
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
            'Tên màu',
            'Mã màu',
        ];
    }

    /**
     * Set data column base on heading
     * 
     * @param object $color
     *  
     * @return array
     */
    public function map($color): array
    {
        return [
            $color->id,
            $color->name,
            $color->code,
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
            'C' => 20,
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
        $sheet->getStyle('A1:C1')->getAlignment()->setHorizontal('center');
    }
}
