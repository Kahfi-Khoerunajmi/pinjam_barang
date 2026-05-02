@extends('layouts.app')

@section('content')
<div class="container-fluid py-4">
    <div class="row mb-4">
        <div class="col-12">
            <h1 class="mb-0">
                <i class="fas fa-chart-bar"></i> Laporan dan Analitik
            </h1>
            <p class="text-muted mt-2">Dashboard pelaporan peminjaman barang</p>
        </div>
    </div>

    <!-- Statistics Cards -->
    <div class="row mb-4">
        <div class="col-md-3 mb-3">
            <div class="card border-0 shadow-sm">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-start">
                        <div>
                            <p class="text-muted small mb-1">Total Barang</p>
                            <h3 class="mb-0">{{ $stats['total_items'] ?? 0 }}</h3>
                        </div>
                        <i class="fas fa-box text-primary fa-2x opacity-75"></i>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-md-3 mb-3">
            <div class="card border-0 shadow-sm">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-start">
                        <div>
                            <p class="text-muted small mb-1">Peminjaman Aktif</p>
                            <h3 class="mb-0">{{ $stats['active_loans'] ?? 0 }}</h3>
                        </div>
                        <i class="fas fa-hand-holding-box text-warning fa-2x opacity-75"></i>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-md-3 mb-3">
            <div class="card border-0 shadow-sm">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-start">
                        <div>
                            <p class="text-muted small mb-1">Peminjaman Terlambat</p>
                            <h3 class="mb-0 text-danger">{{ $stats['overdue_loans'] ?? 0 }}</h3>
                        </div>
                        <i class="fas fa-clock text-danger fa-2x opacity-75"></i>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-md-3 mb-3">
            <div class="card border-0 shadow-sm">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-start">
                        <div>
                            <p class="text-muted small mb-1">Total Pengguna</p>
                            <h3 class="mb-0">{{ $stats['total_users'] ?? 0 }}</h3>
                        </div>
                        <i class="fas fa-users text-success fa-2x opacity-75"></i>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Reports Section -->
    <div class="row mb-4">
        <div class="col-12">
            <div class="card border-0 shadow-sm">
                <div class="card-header bg-white border-bottom">
                    <h5 class="mb-0">
                        <i class="fas fa-file-export"></i> Buat Laporan
                    </h5>
                </div>
                <div class="card-body">
                    <div class="row">
                        <!-- Loans Report -->
                        <div class="col-md-4 mb-3">
                            <div class="card h-100 border">
                                <div class="card-body">
                                    <h6 class="card-title">
                                        <i class="fas fa-hand-holding-box text-warning"></i> Laporan Peminjaman
                                    </h6>
                                    <p class="card-text small text-muted">Laporan detail semua peminjaman barang dalam periode tertentu</p>
                                    <form action="{{ route('reports.generate-loans') }}" method="POST">
                                        @csrf
                                        <div class="mb-2">
                                            <input type="date" name="start_date" class="form-control form-control-sm" required>
                                        </div>
                                        <div class="mb-2">
                                            <input type="date" name="end_date" class="form-control form-control-sm" required>
                                        </div>
                                        <div class="btn-group w-100" role="group">
                                            <button type="submit" name="format" value="pdf" class="btn btn-sm btn-outline-primary">
                                                <i class="fas fa-file-pdf"></i> PDF
                                            </button>
                                            <button type="submit" name="format" value="excel" class="btn btn-sm btn-outline-success">
                                                <i class="fas fa-file-excel"></i> Excel
                                            </button>
                                        </div>
                                    </form>
                                </div>
                            </div>
                        </div>

                        <!-- Users Report -->
                        <div class="col-md-4 mb-3">
                            <div class="card h-100 border">
                                <div class="card-body">
                                    <h6 class="card-title">
                                        <i class="fas fa-users text-success"></i> Laporan Pengguna
                                    </h6>
                                    <p class="card-text small text-muted">Laporan aktivitas dan statistik pengguna peminjaman</p>
                                    <form action="{{ route('reports.generate-users') }}" method="POST">
                                        @csrf
                                        <div class="btn-group w-100" role="group">
                                            <button type="submit" name="format" value="pdf" class="btn btn-sm btn-outline-primary">
                                                <i class="fas fa-file-pdf"></i> PDF
                                            </button>
                                            <button type="submit" name="format" value="excel" class="btn btn-sm btn-outline-success">
                                                <i class="fas fa-file-excel"></i> Excel
                                            </button>
                                        </div>
                                    </form>
                                </div>
                            </div>
                        </div>

                        <!-- Items Report -->
                        <div class="col-md-4 mb-3">
                            <div class="card h-100 border">
                                <div class="card-body">
                                    <h6 class="card-title">
                                        <i class="fas fa-box text-primary"></i> Laporan Barang
                                    </h6>
                                    <p class="card-text small text-muted">Laporan barang yang paling sering dipinjam</p>
                                    <form action="{{ route('reports.generate-items') }}" method="POST">
                                        @csrf
                                        <div class="btn-group w-100" role="group">
                                            <button type="submit" name="format" value="pdf" class="btn btn-sm btn-outline-primary">
                                                <i class="fas fa-file-pdf"></i> PDF
                                            </button>
                                            <button type="submit" name="format" value="excel" class="btn btn-sm btn-outline-success">
                                                <i class="fas fa-file-excel"></i> Excel
                                            </button>
                                        </div>
                                    </form>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Most Borrowed Items -->
    @if($mostBorrowed && $mostBorrowed->count() > 0)
    <div class="row mb-4">
        <div class="col-12">
            <div class="card border-0 shadow-sm">
                <div class="card-header bg-white border-bottom">
                    <h5 class="mb-0">
                        <i class="fas fa-trophy"></i> Barang Paling Sering Dipinjam
                    </h5>
                </div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table table-hover mb-0">
                            <thead class="table-light">
                                <tr>
                                    <th class="ps-0">Barang</th>
                                    <th>Kategori</th>
                                    <th>Jumlah Peminjaman</th>
                                    <th class="pe-0">Status</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($mostBorrowed as $item)
                                <tr>
                                    <td class="ps-0">
                                        <strong>{{ $item->nama_barang }}</strong>
                                        <br>
                                        <small class="text-muted">{{ $item->kode_barang }}</small>
                                    </td>
                                    <td>{{ $item->category->nama_kategori ?? '-' }}</td>
                                    <td>
                                        <span class="badge bg-primary">{{ $item->loans_count ?? 0 }} kali</span>
                                    </td>
                                    <td class="pe-0">
                                        <span class="badge bg-{{ $item->status === 'tersedia' ? 'success' : ($item->status === 'dipinjam' ? 'warning' : 'danger') }}">
                                            {{ ucfirst($item->status) }}
                                        </span>
                                    </td>
                                </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
    @endif

    <!-- Most Active Users -->
    @if($mostActiveUsers && $mostActiveUsers->count() > 0)
    <div class="row">
        <div class="col-12">
            <div class="card border-0 shadow-sm">
                <div class="card-header bg-white border-bottom">
                    <h5 class="mb-0">
                        <i class="fas fa-star"></i> Pengguna Paling Aktif
                    </h5>
                </div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table table-hover mb-0">
                            <thead class="table-light">
                                <tr>
                                    <th class="ps-0">Pengguna</th>
                                    <th>Email</th>
                                    <th>Jumlah Peminjaman</th>
                                    <th class="pe-0">Terlambat</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($mostActiveUsers as $user)
                                <tr>
                                    <td class="ps-0"><strong>{{ $user->name }}</strong></td>
                                    <td>{{ $user->email }}</td>
                                    <td>
                                        <span class="badge bg-info">{{ $user->loans_count ?? 0 }} peminjaman</span>
                                    </td>
                                    <td class="pe-0">
                                        @if(($user->overdue_count ?? 0) > 0)
                                            <span class="badge bg-danger">{{ $user->overdue_count }} terlambat</span>
                                        @else
                                            <span class="text-muted">-</span>
                                        @endif
                                    </td>
                                </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
    @endif
</div>
@endsection
