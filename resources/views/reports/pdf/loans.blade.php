<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Laporan Peminjaman Barang</title>
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
            border-left: 4px solid #007bff;
        }

        table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 20px;
        }

        table thead {
            background: #007bff;
            color: white;
        }

        table th {
            padding: 10px;
            text-align: left;
            font-weight: bold;
            border: 1px solid #007bff;
        }

        table td {
            padding: 10px;
            border: 1px solid #ddd;
            font-size: 12px;
        }

        table tbody tr:nth-child(even) {
            background: #f9f9f9;
        }

        .status-badge {
            display: inline-block;
            padding: 4px 8px;
            border-radius: 3px;
            font-size: 11px;
            font-weight: bold;
            color: white;
        }

        .status-returned {
            background: #28a745;
        }

        .status-active {
            background: #ffc107;
        }

        .status-overdue {
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

        .summary {
            background: #f5f5f5;
            padding: 15px;
            margin-bottom: 20px;
            border-radius: 4px;
        }

        .summary-item {
            display: inline-block;
            margin-right: 30px;
            margin-bottom: 10px;
        }

        .summary-label {
            font-weight: bold;
            color: #333;
        }

        .summary-value {
            color: #007bff;
            font-size: 18px;
        }
    </style>
</head>
<body>
    <div class="container">
        <div class="header">
            <h1>Laporan Peminjaman Barang</h1>
            <p>Sistem Manajemen Peminjaman Barang</p>
        </div>

        <div class="meta-info">
            <strong>Tanggal Cetak:</strong> {{ $generatedAt }} | 
            <strong>Total Entri:</strong> {{ count($data) }}
        </div>

        <table>
            <thead>
                <tr>
                    <th width="12%">Kode Peminjaman</th>
                    <th width="15%">Peminjam</th>
                    <th width="18%">Barang</th>
                    <th width="12%">Tgl Peminjaman</th>
                    <th width="12%">Tgl Pengembalian</th>
                    <th width="15%">Status</th>
                </tr>
            </thead>
            <tbody>
                @forelse($data as $loan)
                <tr>
                    <td><strong>{{ $loan->kode_peminjaman }}</strong></td>
                    <td>{{ $loan->user->name ?? '-' }}</td>
                    <td>{{ $loan->item->nama_barang ?? '-' }}</td>
                    <td>{{ $loan->tanggal_peminjaman?->format('d/m/Y') }}</td>
                    <td>{{ $loan->tanggal_pengembalian?->format('d/m/Y') ?? '-' }}</td>
                    <td>
                        <span class="status-badge status-{{ $loan->status === 'returned' ? 'returned' : ($loan->status === 'overdue' ? 'overdue' : 'active') }}">
                            {{ ucfirst($loan->status) }}
                        </span>
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="6" style="text-align: center; color: #999;">Tidak ada data</td>
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
