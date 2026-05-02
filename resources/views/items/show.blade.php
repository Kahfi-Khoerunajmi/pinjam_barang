@extends('layouts.app')

@section('content')
<div class="container-fluid">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h1>Detail Barang</h1>
        <a href="{{ route('items.index') }}" class="btn btn-secondary">
            <i class="fas fa-arrow-left"></i> Kembali
        </a>
    </div>

    <div class="row">
        <div class="col-md-4">
            @if($item->gambar)
            <div class="card mb-4">
                <img src="{{ asset('storage/' . $item->gambar) }}" class="card-img-top" alt="{{ $item->nama_barang }}" style="height: 300px; object-fit: cover;">
            </div>
            @else
            <div class="card mb-4 d-flex align-items-center justify-content-center bg-light" style="height: 300px;">
                <i class="fas fa-image text-muted fa-5x"></i>
            </div>
            @endif

            @can('borrow', $item)
            <a href="{{ route('loans.create') }}?item_id={{ $item->id }}" class="btn btn-success btn-lg w-100 mb-3">
                <i class="fas fa-handshake"></i> Pinjam Barang
            </a>
            @endcan

            @can('update', $item)
            <a href="{{ route('items.edit', $item) }}" class="btn btn-warning w-100">
                <i class="fas fa-edit"></i> Edit
            </a>
            @endcan
        </div>

        <div class="col-md-8">
            <div class="card mb-4">
                <div class="card-header">
                    <h4 class="mb-0">{{ $item->nama_barang }}</h4>
                </div>
                <div class="card-body">
                    <div class="row mb-3">
                        <div class="col-md-6">
                            <label class="text-muted small">Kode Barang</label>
                            <p><strong>{{ $item->kode_barang }}</strong></p>
                        </div>
                        <div class="col-md-6">
                            <label class="text-muted small">Status</label>
                            <p>
                                <span class="badge {{ $item->getStatusBadgeClass() }} p-2">
                                    <i class="{{ $item->getStatusIcon() }}"></i> {{ $item->getStatusLabel() }}
                                </span>
                            </p>
                        </div>
                    </div>

                    @if($item->category)
                    <div class="row mb-3">
                        <div class="col-md-6">
                            <label class="text-muted small">Kategori</label>
                            <p>
                                <a href="{{ route('items.index', ['category' => $item->category->id]) }}">
                                    {{ $item->category->nama_kategori }}
                                </a>
                            </p>
                        </div>
                        <div class="col-md-6">
                            <label class="text-muted small">Lokasi</label>
                            <p>{{ $item->lokasi ?? '-' }}</p>
                        </div>
                    </div>
                    @endif

                    @if($item->deskripsi)
                    <div class="mb-3">
                        <label class="text-muted small">Deskripsi</label>
                        <p>{{ $item->deskripsi }}</p>
                    </div>
                    @endif

                    <hr>

                    <div class="row">
                        <div class="col-md-6">
                            <label class="text-muted small">Dibuat Oleh</label>
                            <p>{{ $item->creator->name ?? '-' }}</p>
                        </div>
                        <div class="col-md-6">
                            <label class="text-muted small">Tanggal Dibuat</label>
                            <p>{{ $item->created_at->format('d/m/Y H:i') }}</p>
                        </div>
                    </div>
                </div>
            </div>

            @if($item->activeLoan())
            <div class="card card-warning mb-4">
                <div class="card-header bg-warning">
                    <h5 class="mb-0 text-white">Sedang Dipinjam</h5>
                </div>
                <div class="card-body">
                    @php $activeLoan = $item->activeLoan() @endphp
                    <div class="row">
                        <div class="col-md-6">
                            <label class="text-muted small">Peminjam</label>
                            <p><strong>{{ $activeLoan->user->name }}</strong></p>
                        </div>
                        <div class="col-md-6">
                            <label class="text-muted small">Kode Peminjaman</label>
                            <p><a href="{{ route('loans.show', $activeLoan) }}">{{ $activeLoan->kode_peminjaman }}</a></p>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-md-6">
                            <label class="text-muted small">Tanggal Pinjam</label>
                            <p>{{ $activeLoan->tanggal_pinjam->format('d/m/Y') }}</p>
                        </div>
                        <div class="col-md-6">
                            <label class="text-muted small">Akan Dikembalikan</label>
                            <p>
                                @if($activeLoan->isReturnSoon())
                                <span class="badge badge-danger">{{ $activeLoan->daysUntilReturn() }} hari</span>
                                @elseif($activeLoan->isOverdue())
                                <span class="badge badge-danger">TERLAMBAT {{ abs($activeLoan->daysUntilReturn()) }} hari</span>
                                @else
                                {{ $activeLoan->tanggal_kembali_rencana->format('d/m/Y') }}
                                @endif
                            </p>
                        </div>
                    </div>
                </div>
            </div>
            @endif
        </div>
    </div>

    <!-- Loan History -->
    @if($loanHistory->isNotEmpty())
    <div class="card">
        <div class="card-header">
            <h5 class="mb-0">Riwayat Peminjaman</h5>
        </div>
        <div class="table-responsive">
            <table class="table table-sm table-hover mb-0">
                <thead class="table-light">
                    <tr>
                        <th>Kode</th>
                        <th>Peminjam</th>
                        <th>Tanggal Pinjam</th>
                        <th>Tanggal Kembali</th>
                        <th>Status</th>
                        <th>Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($loanHistory as $loan)
                    <tr>
                        <td><strong>{{ $loan->kode_peminjaman }}</strong></td>
                        <td>{{ $loan->user->name }}</td>
                        <td>{{ $loan->tanggal_pinjam->format('d/m/Y') }}</td>
                        <td>{{ $loan->tanggal_kembali_aktual ? $loan->tanggal_kembali_aktual->format('d/m/Y') : '-' }}</td>
                        <td>
                            <span class="badge {{ $loan->getStatusBadgeClass() }}">
                                {{ $loan->getStatusLabel() }}
                            </span>
                        </td>
                        <td>
                            <a href="{{ route('loans.show', $loan) }}" class="btn btn-sm btn-info">
                                <i class="fas fa-eye"></i>
                            </a>
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>
    @endif
</div>
@endsection
