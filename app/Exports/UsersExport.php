<?php

namespace App\Exports;

use Maatwebsite\Excel\Concerns\FromArray;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithStyles;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;

class UsersExport implements FromArray, WithHeadings, WithStyles
{
    private $users;

    public function __construct($users)
    {
        $this->users = $users;
    }

    public function array(): array
    {
        return $this->users->map(function ($user) {
            return [
                'Nama' => $user->name,
                'Email' => $user->email,
                'Total Peminjaman' => $user->loans_count ?? 0,
            ];
        })->toArray();
    }

    public function headings(): array
    {
        return [
            'Nama Pengguna',
            'Email',
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
