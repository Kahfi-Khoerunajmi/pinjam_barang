<?php

namespace App\Exports;

use Maatwebsite\Excel\Concerns\FromArray;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithStyles;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;

class LoansExport implements FromArray, WithHeadings, WithStyles
{
    private $loans;

    public function __construct($loans)
    {
        $this->loans = $loans;
    }

    public function array(): array
    {
        return $this->loans->map(function ($loan) {
            return [
                'Kode' => $loan->kode_peminjaman,
                'Peminjam' => $loan->user->name,
                'Email' => $loan->user->email,
                'Barang' => $loan->item->nama_barang,
                'Kode Barang' => $loan->item->kode_barang,
                'Tanggal Pinjam' => $loan->tanggal_pinjam->format('d-m-Y'),
                'Tanggal Kembali Rencana' => $loan->tanggal_kembali_rencana->format('d-m-Y'),
                'Tanggal Kembali Aktual' => $loan->tanggal_kembali_aktual ? $loan->tanggal_kembali_aktual->format('d-m-Y') : '-',
                'Status' => $loan->getStatusLabel(),
                'Catatan' => $loan->catatan ?? '-',
            ];
        })->toArray();
    }

    public function headings(): array
    {
        return [
            'Kode Peminjaman',
            'Peminjam',
            'Email',
            'Barang',
            'Kode Barang',
            'Tanggal Pinjam',
            'Tanggal Kembali Rencana',
            'Tanggal Kembali Aktual',
            'Status',
            'Catatan',
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
