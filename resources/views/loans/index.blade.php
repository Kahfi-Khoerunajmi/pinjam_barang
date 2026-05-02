@extends('layouts.app')

@section('content')
<div class="container-fluid">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h1>Daftar Peminjaman</h1>
        <a href="{{ route('items.index') }}" class="btn btn-primary">
            <i class="fas fa-plus"></i> Pinjam Barang Baru
        </a>
    </div>

    <!-- Filter -->
    <div class="card mb-4">
        <div class="card-body">
            <form method="GET" class="row g-3">
                <div class="col-md-4">
                    <input type="text" name="search" class="form-control" placeholder="Cari kode peminjaman..." value="{{ request('search') }}">
                </div>
                <div class="col-md-4">
                    <select name="status" class="form-select">
                        <option value="">Semua Status</option>
                        <option value="diajukan" @selected(request('status') == 'diajukan')>Menunggu Persetujuan</option>
                        <option value="dipinjam" @selected(request('status') == 'dipinjam')>Sedang Dipinjam</option>
                        <option value="dikembalikan" @selected(request('status') == 'dikembalikan')>Dikembalikan</option>
                        <option value="terlambat" @selected(request('status') == 'terlambat')>Terlambat</option>
                        <option value="ditolak" @selected(request('status') == 'ditolak')>Ditolak</option>
                    </select>
                </div>
                <div class="col-md-2">
                    <button type="submit" class="btn btn-info w-100">
                        <i class="fas fa-search"></i> Cari
                    </button>
                </div>
                @if(request()->filled('search') || request()->filled('status'))
                <div class="col-md-2">
                    <a href="{{ route('loans.index') }}" class="btn btn-secondary w-100">
                        <i class="fas fa-redo"></i> Reset
                    </a>
                </div>
                @endif
            </form>
        </div>
    </div>

    <!-- Loans Table -->
    <div class="card">
        <div class="table-responsive">
            <table class="table table-hover mb-0">
                <thead class="table-light">
                    <tr>
                        <th>Kode Peminjaman</th>
                        <th>Peminjam</th>
                        <th>Barang</th>
                        <th>Tanggal Pinjam</th>
                        <th>Tanggal Kembali</th>
                        <th>Status</th>
                        <th>Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($loans as $loan)
                    <tr>
                        <td>
                            <strong>{{ $loan->kode_peminjaman }}</strong>
                        </td>
                        <td>{{ $loan->user->name }}</td>
                        <td>{{ $loan->item->nama_barang }}</td>
                        <td>{{ $loan->tanggal_pinjam->format('d/m/Y') }}</td>
                        <td>
                            @if($loan->status === 'dipinjam')
                                @if($loan->isReturnSoon())
                                    <span class="badge badge-danger">{{ $loan->daysUntilReturn() }} hari</span>
                                @elseif($loan->isOverdue())
                                    <span class="badge badge-danger">TERLAMBAT {{ abs($loan->daysUntilReturn()) }} hari</span>
                                @else
                                    {{ $loan->tanggal_kembali_rencana->format('d/m/Y') }}
                                @endif
                            @else
                                {{ $loan->tanggal_kembali_aktual ? $loan->tanggal_kembali_aktual->format('d/m/Y') : '-' }}
                            @endif
                        </td>
                        <td>
                            <span class="badge {{ $loan->getStatusBadgeClass() }}">
                                <i class="{{ $loan->getStatusIcon() }}"></i> {{ $loan->getStatusLabel() }}
                            </span>
                        </td>
                        <td>
                            <div class="btn-group" role="group">
                                <a href="{{ route('loans.show', $loan) }}" class="btn btn-sm btn-info" title="Lihat Detail">
                                    <i class="fas fa-eye"></i>
                                </a>
                                @if($loan->status === 'diajukan' && auth()->user()->hasRole('admin'))
                                <form action="{{ route('loans.approve', $loan) }}" method="POST" style="display: contents;" onsubmit="return confirm('Apakah Anda yakin ingin menyetujui peminjaman ini?');">
                                    @csrf
                                    <button type="submit" class="btn btn-sm btn-primary" title="Setujui">
                                        <i class="fas fa-check"></i>
                                    </button>
                                </form>
                                <form action="{{ route('loans.reject', $loan) }}" method="POST" style="display: contents;" onsubmit="return confirm('Apakah Anda yakin ingin menolak peminjaman ini?');">
                                    @csrf
                                    <button type="submit" class="btn btn-sm btn-danger" title="Tolak">
                                        <i class="fas fa-times"></i>
                                    </button>
                                </form>
                                @endif
                                @if($loan->status === 'dipinjam' && (auth()->user()->hasRole('admin') || auth()->id() === $loan->user_id))
                                <a href="{{ route('loans.return-form', $loan) }}" class="btn btn-sm btn-success" title="Kembalikan">
                                    <i class="fas fa-undo"></i>
                                </a>
                                @endif
                                @can('delete', $loan)
                                @if($loan->status === 'dipinjam')
                                <form action="{{ route('loans.destroy', $loan) }}" method="POST" style="display: contents;" onsubmit="return confirm('Apakah Anda yakin ingin menghapus data peminjaman ini?');">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="btn btn-sm btn-danger" title="Hapus">
                                        <i class="fas fa-trash"></i>
                                    </button>
                                </form>
                                @endif
                                @endcan
                            </div>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="7" class="text-center text-muted py-4">
                            <i class="fas fa-inbox fa-2x"></i>
                            <p class="mt-2">Tidak ada peminjaman</p>
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

    <!-- Pagination -->
    <div class="d-flex justify-content-center mt-4">
        {{ $loans->links() }}
    </div>
</div>
@endsection
