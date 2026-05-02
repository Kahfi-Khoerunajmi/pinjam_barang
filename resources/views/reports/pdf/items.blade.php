<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Laporan Barang</title>
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

        .badge {
            display: inline-block;
            padding: 4px 8px;
            border-radius: 3px;
            font-size: 11px;
            font-weight: bold;
            color: white;
        }

        .badge-success {
            background: #28a745;
        }

        .badge-warning {
            background: #ffc107;
            color: #333;
        }

        .badge-danger {
            background: #dc3545;
        }

        .badge-primary {
            background: #007bff;
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
            <h1>Laporan Barang</h1>
            <p>Sistem Manajemen Peminjaman Barang</p>
        </div>

        <div class="meta-info">
            <strong>Tanggal Cetak:</strong> {{ $generatedAt }} | 
            <strong>Total Barang:</strong> {{ count($data) }}
        </div>

        <table>
            <thead>
                <tr>
                    <th width="12%">Kode</th>
                    <th width="18%">Nama Barang</th>
                    <th width="15%">Kategori</th>
                    <th width="12%">Kondisi</th>
                    <th width="15%">Jumlah Peminjaman</th>
                    <th width="12%">Status</th>
                </tr>
            </thead>
            <tbody>
                @forelse($data as $item)
                <tr>
                    <td><strong>{{ $item->kode_barang }}</strong></td>
                    <td>{{ $item->nama_barang }}</td>
                    <td>{{ $item->category?->nama_kategori ?? '-' }}</td>
                    <td>
                        <span class="badge badge-{{ $item->kondisi === 'baik' ? 'success' : ($item->kondisi === 'rusak' ? 'danger' : 'warning') }}">
                            {{ ucfirst($item->kondisi) }}
                        </span>
                    </td>
                    <td>
                        <span class="badge badge-primary">{{ $item->loans_count ?? 0 }} kali</span>
                    </td>
                    <td>
                        <span class="badge badge-{{ $item->status === 'tersedia' ? 'success' : ($item->status === 'dipinjam' ? 'warning' : 'danger') }}">
                            {{ ucfirst($item->status) }}
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
