@extends('layouts.app')

@section('content')
<div class="container-fluid py-4">
    <div class="row mb-4">
        <div class="col-12">
            <div class="d-flex justify-content-between align-items-center">
                <h1 class="mb-0">
                    <i class="fas fa-exclamation-triangle text-danger"></i> Peminjaman Terlambat
                </h1>
                <a href="{{ route('reports.index') }}" class="btn btn-outline-secondary btn-sm">
                    <i class="fas fa-arrow-left"></i> Kembali
                </a>
            </div>
        </div>
    </div>

    <div class="row">
        <div class="col-12">
            <div class="card border-0 shadow-sm">
                <div class="card-body">
                    @if($overdueLoans && $overdueLoans->count() > 0)
                        <div class="table-responsive">
                            <table class="table table-hover">
                                <thead class="table-light">
                                    <tr>
                                        <th class="ps-0">Kode Peminjaman</th>
                                        <th>Peminjam</th>
                                        <th>Barang</th>
                                        <th>Tgl Peminjaman</th>
                                        <th>Tgl Seharusnya Dikembalikan</th>
                                        <th>Terlambat (Hari)</th>
                                        <th class="pe-0">Aksi</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($overdueLoans as $loan)
                                    <tr class="table-danger">
                                        <td class="ps-0">
                                            <strong>{{ $loan->kode_peminjaman }}</strong>
                                        </td>
                                        <td>{{ $loan->user->name ?? '-' }}</td>
                                        <td>{{ $loan->item->nama_barang ?? '-' }}</td>
                                        <td>{{ $loan->tanggal_peminjaman?->format('d/m/Y') }}</td>
                                        <td>{{ $loan->tanggal_kembali_rencana?->format('d/m/Y') }}</td>
                                        <td>
                                            <span class="badge bg-danger">
                                                {{ $loan->overduedays ?? 0 }} hari
                                            </span>
                                        </td>
                                        <td class="pe-0">
                                            <a href="{{ route('loans.show', $loan) }}" class="btn btn-sm btn-primary">
                                                <i class="fas fa-eye"></i> Lihat
                                            </a>
                                        </td>
                                    </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    @else
                        <div class="text-center py-5">
                            <i class="fas fa-check-circle text-success" style="font-size: 3rem;"></i>
                            <p class="text-muted mt-3">Tidak ada peminjaman yang terlambat</p>
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
