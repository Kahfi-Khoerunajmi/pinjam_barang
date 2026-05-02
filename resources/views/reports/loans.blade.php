@extends('layouts.app')

@section('content')
<div class="container-fluid py-4">
    <div class="row mb-4">
        <div class="col-12">
            <div class="d-flex justify-content-between align-items-center">
                <h1 class="mb-0">
                    <i class="fas fa-hand-holding-box"></i> Laporan Peminjaman
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
                    <div class="table-responsive">
                        <table class="table table-hover">
                            <thead class="table-light">
                                <tr>
                                    <th class="ps-0">Kode</th>
                                    <th>Peminjam</th>
                                    <th>Barang</th>
                                    <th>Tgl Peminjaman</th>
                                    <th>Tgl Pengembalian</th>
                                    <th>Status</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($loans as $loan)
                                <tr>
                                    <td class="ps-0">
                                        <strong>{{ $loan->kode_peminjaman }}</strong>
                                    </td>
                                    <td>{{ $loan->user->name ?? '-' }}</td>
                                    <td>{{ $loan->item->nama_barang ?? '-' }}</td>
                                    <td>{{ $loan->tanggal_peminjaman?->format('d/m/Y') }}</td>
                                    <td>{{ $loan->tanggal_pengembalian?->format('d/m/Y') ?? '-' }}</td>
                                    <td>
                                        <span class="badge bg-{{ $loan->status === 'returned' ? 'success' : ($loan->status === 'overdue' ? 'danger' : 'warning') }}">
                                            {{ ucfirst($loan->status) }}
                                        </span>
                                    </td>
                                </tr>
                                @empty
                                <tr>
                                    <td colspan="6" class="text-center py-4 text-muted">
                                        Tidak ada data peminjaman
                                    </td>
                                </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>

                    @if($loans instanceof \Illuminate\Pagination\Paginator)
                        <div class="mt-4">
                            {{ $loans->links() }}
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
