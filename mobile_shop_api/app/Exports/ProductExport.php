<?php

namespace App\Exports;

use App\Models\Product;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\FromQuery;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use Maatwebsite\Excel\Concerns\WithColumnWidths;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;
use Maatwebsite\Excel\Concerns\WithStyles;
use Maatwebsite\Excel\Concerns\WithTitle;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;

class ProductExport implements
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
     * Get product collection with condition
     * 
     * @return \Illuminate\Support\Collection
     */
    public function collection() {
        return (new Product)->getProductWithOrder($this->condition);
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
            'Tên sản phẩm',
            'Danh mục',
            'Hãng',
            'Giá gốc',
            'Giá bán',
            'Thứ tự',
            'Ảnh',
            'Ngày tạo'
        ];
    }

    /**
     * Set data column base on heading
     * 
     * @param object $product
     *  
     * @return array
     */
    public function map($product): array
    {
        return [
            $product->id,
            $product->name,
            $product->category->name,
            $product->brand->name,
            $product->price_core,
            $product->price,
            $product->sort_no,
            $product->image,
            $product->created_at,
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
            'C' => 20,
            'D' => 20,
            'E' => 20,
            'F' => 20,
            'H' => 50,
            'I' => 20,
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
        $sheet->getStyle('A1:I1')->getAlignment()->setHorizontal('center');
    }
}
