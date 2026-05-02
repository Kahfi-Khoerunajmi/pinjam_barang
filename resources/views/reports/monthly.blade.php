@extends('layouts.app')

@section('content')
<div class="container-fluid py-4">
    <div class="row mb-4">
        <div class="col-12">
            <div class="d-flex justify-content-between align-items-center">
                <h1 class="mb-0">
                    <i class="fas fa-chart-line"></i> Statistik Bulanan
                </h1>
                <a href="{{ route('reports.index') }}" class="btn btn-outline-secondary btn-sm">
                    <i class="fas fa-arrow-left"></i> Kembali
                </a>
            </div>
        </div>
    </div>

    <!-- Month/Year Selection -->
    <div class="row mb-4">
        <div class="col-12">
            <div class="card border-0 shadow-sm">
                <div class="card-body">
                    <form method="GET" class="d-flex gap-2 align-items-end">
                        <div class="flex-grow-1">
                            <label for="month" class="form-label">Bulan</label>
                            <select name="month" id="month" class="form-select">
                                @for($m = 1; $m <= 12; $m++)
                                    <option value="{{ $m }}" {{ $m == now()->month ? 'selected' : '' }}>
                                        {{ now()->setMonth($m)->format('F') }}
                                    </option>
                                @endfor
                            </select>
                        </div>
                        <div class="flex-grow-1">
                            <label for="year" class="form-label">Tahun</label>
                            <input type="number" name="year" id="year" class="form-control" 
                                   value="{{ request('year', now()->year) }}" min="2020">
                        </div>
                        <button type="submit" class="btn btn-primary">
                            <i class="fas fa-search"></i> Cari
                        </button>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <!-- Statistics Cards -->
    @if($stats)
    <div class="row mb-4">
        <div class="col-md-3">
            <div class="card border-0 shadow-sm">
                <div class="card-body">
                    <p class="text-muted small mb-1">Total Peminjaman</p>
                    <h3 class="mb-0">{{ $stats['total_loans'] ?? 0 }}</h3>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card border-0 shadow-sm">
                <div class="card-body">
                    <p class="text-muted small mb-1">Peminjaman Dikembalikan</p>
                    <h3 class="mb-0 text-success">{{ $stats['returned_loans'] ?? 0 }}</h3>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card border-0 shadow-sm">
                <div class="card-body">
                    <p class="text-muted small mb-1">Peminjaman Aktif</p>
                    <h3 class="mb-0 text-warning">{{ $stats['active_loans'] ?? 0 }}</h3>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card border-0 shadow-sm">
                <div class="card-body">
                    <p class="text-muted small mb-1">Peminjaman Terlambat</p>
                    <h3 class="mb-0 text-danger">{{ $stats['overdue_loans'] ?? 0 }}</h3>
                </div>
            </div>
        </div>
    </div>
    @endif

    <!-- Details -->
    <div class="row">
        <div class="col-12">
            <div class="card border-0 shadow-sm">
                <div class="card-header bg-white border-bottom">
                    <h5 class="mb-0">Detail Statistik</h5>
                </div>
                <div class="card-body">
                    @forelse($stats['details'] ?? [] as $detail)
                        <div class="row mb-3 pb-3 border-bottom">
                            <div class="col-md-6">
                                <p class="mb-1 text-muted small">{{ $detail['label'] ?? '' }}</p>
                                <h5 class="mb-0">{{ $detail['value'] ?? 0 }}</h5>
                            </div>
                        </div>
                    @empty
                        <div class="text-center py-4 text-muted">
                            Tidak ada data untuk periode ini
                        </div>
                    @endforelse
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
