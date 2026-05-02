@extends('layouts.app')

@section('content')
<div class="container">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h3>Detail Peminjaman</h3>
        <a href="{{ route('loans.index') }}" class="btn btn-secondary">
            <i class="fas fa-arrow-left"></i> Kembali
        </a>
    </div>

    <div class="row">
        <div class="col-md-8">
            <div class="card mb-4">
                <div class="card-header">
                    <h5 class="mb-0">Informasi Peminjaman</h5>
                </div>
                <div class="card-body">
                    <div class="row mb-3">
                        <div class="col-md-6">
                            <label class="text-muted">Kode Peminjaman</label>
                            <p><strong>{{ $loan->kode_peminjaman }}</strong></p>
                        </div>
                        <div class="col-md-6">
                            <label class="text-muted">Status</label>
                            <p>
                                <span class="badge {{ $loan->getStatusBadgeClass() }} p-2">
                                    <i class="{{ $loan->getStatusIcon() }}"></i> {{ $loan->getStatusLabel() }}
                                </span>
                            </p>
                        </div>
                    </div>

                    <hr>

                    <div class="row mb-3">
                        <div class="col-md-6">
                            <label class="text-muted">Peminjam</label>
                            <p><strong>{{ $loan->user->name }}</strong></p>
                            <small class="text-muted">{{ $loan->user->email }}</small>
                        </div>
                        <div class="col-md-6">
                            <label class="text-muted">Barang</label>
                            <p><strong>{{ $loan->item->nama_barang }}</strong></p>
                            <small class="text-muted">{{ $loan->item->kode_barang }}</small>
                        </div>
                    </div>

                    <hr>

                    <div class="row mb-3">
                        <div class="col-md-4">
                            <label class="text-muted">Tanggal Pinjam</label>
                            <p><strong>{{ $loan->tanggal_pinjam->format('d/m/Y') }}</strong></p>
                        </div>
                        <div class="col-md-4">
                            <label class="text-muted">Tanggal Kembali Rencana</label>
                            <p><strong>{{ $loan->tanggal_kembali_rencana->format('d/m/Y') }}</strong></p>
                        </div>
                        <div class="col-md-4">
                            <label class="text-muted">Tanggal Kembali Aktual</label>
                            <p><strong>{{ $loan->tanggal_kembali_aktual ? $loan->tanggal_kembali_aktual->format('d/m/Y') : '-' }}</strong></p>
                        </div>
                    </div>

                    @if($loan->catatan)
                    <hr>
                    <div class="mb-3">
                        <label class="text-muted">Catatan</label>
                        <p>{{ $loan->catatan }}</p>
                    </div>
                    @endif

                    @if($loan->admin)
                    <hr>
                    <div class="mb-0">
                        <label class="text-muted">Dikonfirmasi Oleh</label>
                        <p><strong>{{ $loan->admin->name }}</strong></p>
                    </div>
                    @endif
                </div>
            </div>
        </div>

        <div class="col-md-4">
            <!-- Actions -->
            <div class="card mb-4">
                <div class="card-header">
                    <h5 class="mb-0">Aksi</h5>
                </div>
                <div class="card-body">
                    @if($loan->status === 'diajukan' && auth()->user()->hasRole('admin'))
                    <form action="{{ route('loans.approve', $loan) }}" method="POST" class="mb-2">
                        @csrf
                        <button type="submit" class="btn btn-primary w-100" onclick="return confirm('Setujui peminjaman ini?')">
                            <i class="fas fa-check"></i> Setujui Pengajuan
                        </button>
                    </form>
                    <form action="{{ route('loans.reject', $loan) }}" method="POST" class="mb-2">
                        @csrf
                        <button type="submit" class="btn btn-danger w-100" onclick="return confirm('Tolak peminjaman ini?')">
                            <i class="fas fa-times"></i> Tolak Pengajuan
                        </button>
                    </form>
                    @endif

                    @if($loan->status === 'dipinjam' && (auth()->user()->hasRole('admin') || auth()->id() === $loan->user_id))
                    <form action="{{ route('loans.return', $loan) }}" method="POST" class="mb-2">
                        @csrf
                        <button type="submit" class="btn btn-success w-100" onclick="return confirm('Kembalikan barang?')">
                            <i class="fas fa-undo"></i> Kembalikan Barang
                        </button>
                    </form>
                    @endif

                    @if(auth()->user()->hasRole('admin') && $loan->status !== 'dikembalikan')
                    <a href="{{ route('loans.edit', $loan) }}" class="btn btn-warning w-100 mb-2">
                        <i class="fas fa-edit"></i> Edit
                    </a>
                    @endif

                    <a href="{{ route('items.show', $loan->item) }}" class="btn btn-info w-100">
                        <i class="fas fa-box"></i> Lihat Barang
                    </a>
                </div>
            </div>

            <!-- Status Timeline -->
            <div class="card">
                <div class="card-header">
                    <h5 class="mb-0">Timeline</h5>
                </div>
                <div class="card-body">
                    <div class="timeline">
                        <div class="timeline-item">
                            <div class="timeline-marker bg-success"></div>
                            <div class="timeline-content">
                                <h6 class="mb-0">Peminjaman Dibuat</h6>
                                <small class="text-muted">{{ $loan->created_at->format('d/m/Y H:i') }}</small>
                            </div>
                        </div>

                        @if($loan->tanggal_kembali_aktual)
                        <div class="timeline-item">
                            <div class="timeline-marker bg-info"></div>
                            <div class="timeline-content">
                                <h6 class="mb-0">Barang Dikembalikan</h6>
                                <small class="text-muted">{{ $loan->tanggal_kembali_aktual->format('d/m/Y') }}</small>
                            </div>
                        </div>
                        @endif
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<style>
.timeline {
    position: relative;
}
.timeline::before {
    content: '';
    position: absolute;
    left: 8px;
    top: 0;
    bottom: 0;
    width: 2px;
    background: #e9ecef;
}
.timeline-item {
    display: flex;
    margin-bottom: 20px;
    position: relative;
}
.timeline-marker {
    width: 18px;
    height: 18px;
    border-radius: 50%;
    margin-right: 15px;
    flex-shrink: 0;
}
</style>
@endsection
