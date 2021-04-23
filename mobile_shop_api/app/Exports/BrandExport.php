<?php

namespace App\Exports;

use App\Models\Brand;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\FromQuery;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use Maatwebsite\Excel\Concerns\WithColumnWidths;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;
use Maatwebsite\Excel\Concerns\WithStyles;
use Maatwebsite\Excel\Concerns\WithTitle;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;

class BrandExport implements
FromCollection,
    ShouldAutoSize,
    WithMapping,
    WithHeadings,
    WithStyles,
    WithColumnWidths,
    WithTitle
{
    /**
     * Instantiate a new controller instance
     *
     * @param  array $condition
     * @param  int $page
     * 
     * @return void
     */
    public function __construct(array $condition, int $page)
    {
        $this->condition = $condition;
        $this->page = $page;
    }

    /**
     * Get brand collection with condition
     * 
     * @return \Illuminate\Support\Collection
     */
    public function collection() {
        return (new Brand)->getBrandWithOrder($this->condition);
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
            'Tên danh mục',
            'Ảnh',
            'Hiển thị',
            'Thứ tự',
            'Ngày tạo'
        ];
    }

    /**
     * Set data column base on heading
     * 
     * @param object $brand
     *  
     * @return array
     */
    public function map($brand): array
    {
        return [
            $brand->id,
            $brand->name,
            $brand->image,
            $brand->home,
            $brand->sort_no,
            $brand->created_at
        ];
    }

    /**
     * Set name of multiple sheets
     * 
     * @return string
     */
    public function title(): string
    {
        return 'Trang ' . $this->page;
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
            'C' => 50,
            'F' => 20,
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
