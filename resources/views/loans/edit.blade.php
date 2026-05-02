@extends('layouts.app')

@section('content')
<div class="container">
    <div class="row justify-content-center">
        <div class="col-md-8">
            <div class="card">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <h5 class="mb-0">Edit Peminjaman: {{ $loan->kode_peminjaman }}</h5>
                    <a href="{{ route('loans.show', $loan) }}" class="btn btn-secondary btn-sm">
                        <i class="fas fa-arrow-left"></i> Kembali
                    </a>
                </div>

                <div class="card-body">
                    <form action="{{ route('loans.update', $loan) }}" method="POST">
                        @csrf
                        @method('PUT')

                        <div class="mb-3">
                            <label class="form-label">Peminjam</label>
                            <input type="text" class="form-control" value="{{ $loan->user->name }}" readonly disabled>
                        </div>

                        <div class="mb-3">
                            <label class="form-label">Barang</label>
                            <input type="text" class="form-control" value="{{ $loan->item->nama_barang }}" readonly disabled>
                        </div>

                        <div class="mb-3">
                            <label class="form-label">Tanggal Pinjam</label>
                            <input type="text" class="form-control" value="{{ $loan->tanggal_pinjam->format('Y-m-d') }}" readonly disabled>
                        </div>

                        <div class="mb-3">
                            <label for="tanggal_kembali_rencana" class="form-label">Tanggal Kembali Rencana <span class="text-danger">*</span></label>
                            <input type="date" class="form-control @error('tanggal_kembali_rencana') is-invalid @enderror" id="tanggal_kembali_rencana" name="tanggal_kembali_rencana" value="{{ old('tanggal_kembali_rencana', $loan->tanggal_kembali_rencana->format('Y-m-d')) }}" required>
                            @error('tanggal_kembali_rencana')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="mb-3">
                            <label for="catatan" class="form-label">Catatan</label>
                            <textarea class="form-control @error('catatan') is-invalid @enderror" id="catatan" name="catatan" rows="3">{{ old('catatan', $loan->catatan) }}</textarea>
                            @error('catatan')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="d-flex justify-content-end mt-4">
                            <button type="submit" class="btn btn-primary">
                                <i class="fas fa-save"></i> Simpan Perubahan
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
