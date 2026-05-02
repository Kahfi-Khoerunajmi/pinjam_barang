<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Laporan Pengguna</title>
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }
        
        body {
            font-family: Arial, sans-serif;
            color: #333;
            line-height: 1.6;
        }

        .container {
            width: 100%;
            max-width: 100%;
            padding: 20px;
        }

        .header {
            text-align: center;
            margin-bottom: 30px;
            border-bottom: 3px solid #333;
            padding-bottom: 20px;
        }

        .header h1 {
            font-size: 24px;
            margin-bottom: 5px;
        }

        .header p {
            font-size: 12px;
            color: #666;
        }

        .meta-info {
            background: #f5f5f5;
            padding: 10px 15px;
            margin-bottom: 20px;
            font-size: 12px;
            border-left: 4px solid #28a745;
        }

        table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 20px;
        }

        table thead {
            background: #28a745;
            color: white;
        }

        table th {
            padding: 10px;
            text-align: left;
            font-weight: bold;
            border: 1px solid #28a745;
        }

        table td {
            padding: 10px;
            border: 1px solid #ddd;
            font-size: 12px;
        }

        table tbody tr:nth-child(even) {
            background: #f9f9f9;
        }

        .badge {
            display: inline-block;
            padding: 4px 8px;
            border-radius: 3px;
            font-size: 11px;
            font-weight: bold;
            color: white;
        }

        .badge-info {
            background: #17a2b8;
        }

        .badge-danger {
            background: #dc3545;
        }

        .footer {
            margin-top: 30px;
            padding-top: 20px;
            border-top: 1px solid #ddd;
            text-align: center;
            font-size: 11px;
            color: #666;
        }
    </style>
</head>
<body>
    <div class="container">
        <div class="header">
            <h1>Laporan Pengguna</h1>
            <p>Sistem Manajemen Peminjaman Barang</p>
        </div>

        <div class="meta-info">
            <strong>Tanggal Cetak:</strong> {{ $generatedAt }} | 
            <strong>Total Pengguna:</strong> {{ count($data) }}
        </div>

        <table>
            <thead>
                <tr>
                    <th width="20%">Nama</th>
                    <th width="25%">Email</th>
                    <th width="15%">Jumlah Peminjaman</th>
                    <th width="15%">Peminjaman Terlambat</th>
                    <th width="15%">Terdaftar</th>
                </tr>
            </thead>
            <tbody>
                @forelse($data as $user)
                <tr>
                    <td><strong>{{ $user->name }}</strong></td>
                    <td>{{ $user->email }}</td>
                    <td>
                        <span class="badge badge-info">{{ $user->loans_count ?? 0 }}</span>
                    </td>
                    <td>
                        @if(($user->overdue_count ?? 0) > 0)
                            <span class="badge badge-danger">{{ $user->overdue_count }}</span>
                        @else
                            <span style="color: #999;">-</span>
                        @endif
                    </td>
                    <td>{{ $user->created_at?->format('d/m/Y') }}</td>
                </tr>
                @empty
                <tr>
                    <td colspan="5" style="text-align: center; color: #999;">Tidak ada data</td>
                </tr>
                @endforelse
            </tbody>
        </table>

        <div class="footer">
            <p>Dokumen ini digenerate otomatis oleh Sistem Manajemen Peminjaman Barang</p>
            <p>&copy; {{ now()->year }}. Semua hak dilindungi.</p>
        </div>
    </div>
</body>
</html>
