<?php

namespace App\Exports;

use Maatwebsite\Excel\Concerns\FromArray;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithStyles;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;

class ItemsExport implements FromArray, WithHeadings, WithStyles
{
    private $items;

    public function __construct($items)
    {
        $this->items = $items;
    }

    public function array(): array
    {
        return $this->items->map(function ($item) {
            return [
                'Nama Barang' => $item->nama_barang,
                'Kode Barang' => $item->kode_barang,
                'Kategori' => $item->category->nama_kategori ?? '-',
                'Lokasi' => $item->lokasi ?? '-',
                'Status' => $item->getStatusLabel(),
                'Total Peminjaman' => $item->loans_count ?? 0,
            ];
        })->toArray();
    }

    public function headings(): array
    {
        return [
            'Nama Barang',
            'Kode Barang',
            'Kategori',
            'Lokasi',
            'Status',
            'Total Peminjaman',
        ];
    }

    public function styles(Worksheet $sheet)
    {
        return [
            1 => [
                'font' => ['bold' => true, 'color' => ['rgb' => 'FFFFFF']],
                'fill' => ['fillType' => 'solid', 'startColor' => ['rgb' => '366092']],
            ],
        ];
    }
}
