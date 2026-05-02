@extends('layouts.app')

@section('content')
<div class="container">
    <div class="row justify-content-center">
        <div class="col-md-8">
            <div class="card">
                <div class="card-header">
                    <h4 class="mb-0">Pinjam Barang</h4>
                </div>
                <div class="card-body">
                    <div class="alert alert-info">
                        <h5>{{ $item->nama_barang }}</h5>
                        <p class="mb-0">Kode: <strong>{{ $item->kode_barang }}</strong></p>
                        <p class="mb-0">Status: <span class="badge {{ $item->getStatusBadgeClass() }}">{{ $item->getStatusLabel() }}</span></p>
                    </div>

                    <form action="{{ route('loans.store') }}" method="POST">
                        @csrf
                        <input type="hidden" name="item_id" value="{{ $item->id }}">

                        <div class="mb-3">
                            <label for="tanggal_kembali_rencana" class="form-label">Tanggal Pengembalian Rencana *</label>
                            <input type="date" class="form-control @error('tanggal_kembali_rencana') is-invalid @enderror" 
                                   id="tanggal_kembali_rencana" name="tanggal_kembali_rencana" 
                                   value="{{ old('tanggal_kembali_rencana') }}" 
                                   min="{{ date('Y-m-d', strtotime('+1 day')) }}" required>
                            <small class="form-text text-muted">Minimal 1 hari dari sekarang</small>
                            @error('tanggal_kembali_rencana')
                            <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="mb-3">
                            <label for="catatan" class="form-label">Catatan (Opsional)</label>
                            <textarea class="form-control @error('catatan') is-invalid @enderror" 
                                      id="catatan" name="catatan" rows="3" placeholder="Kondisi barang, kebutuhan khusus, dll...">{{ old('catatan') }}</textarea>
                            @error('catatan')
                            <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="alert alert-warning" role="alert">
                            <h6>Perhatian</h6>
                            <ul class="mb-0 small">
                                <li>Pastikan kondisi barang sebelum membawa pulang</li>
                                <li>Barang harus dikembalikan sesuai tanggal yang ditentukan</li>
                                <li>Keterlambatan akan tercatat dalam sistem</li>
                            </ul>
                        </div>

                        <div class="d-flex gap-2">
                            <button type="submit" class="btn btn-primary">
                                <i class="fas fa-handshake"></i> Pinjam Barang
                            </button>
                            <a href="{{ route('items.show', $item) }}" class="btn btn-secondary">
                                <i class="fas fa-arrow-left"></i> Kembali
                            </a>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
