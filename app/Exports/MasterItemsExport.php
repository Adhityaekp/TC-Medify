<?php

namespace App\Exports;

use App\Models\MasterItem;
use Maatwebsite\Excel\Concerns\FromQuery;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;
use Maatwebsite\Excel\Concerns\WithStyles;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;

class MasterItemsExport implements FromQuery, WithHeadings, WithMapping, ShouldAutoSize, WithStyles
{
    private int $no = 0;

    public function query()
    {
        return MasterItem::query()->with('kategoris:id,nama')->orderBy('id');
    }

    public function headings(): array
    {
        return ['No', 'Nama Kategori', 'Nama Items', 'Nama Supplier', 'Harga', 'Laba (%)', 'Harga Jual'];
    }

    public function map($item): array
    {
        $harga_jual = round($item->harga_beli + $item->harga_beli * $item->laba / 100);

        return [
            ++$this->no,
            $item->kategoris->pluck('nama')->implode(', '),
            $item->nama,
            $item->supplier,
            $item->harga_beli,
            $item->laba,
            $harga_jual,
        ];
    }

    public function styles(Worksheet $sheet)
    {
        return [1 => ['font' => ['bold' => true]]];
    }
}