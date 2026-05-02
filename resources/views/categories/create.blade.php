@extends('layouts.app')

@section('content')
<div class="row mb-4">
    <div class="col-md-8 mx-auto">
        <a href="{{ route('categories.index') }}" class="btn btn-link text-decoration-none text-muted p-0 mb-3">
            <i class="fas fa-arrow-left me-1"></i> Kembali ke Daftar
        </a>
        <h2 class="mb-0 text-primary-emphasis fw-bold">Tambah Kategori Baru</h2>
        <p class="text-muted">Buat kategori baru untuk mengelompokkan barang-barang inventaris.</p>
    </div>
</div>

<div class="row">
    <div class="col-md-8 mx-auto">
        <div class="custom-card border-0 shadow-sm">
            <div class="card-body p-4">
                <form action="{{ route('categories.store') }}" method="POST">
                    @csrf
                    
                    <div class="mb-4">
                        <label for="nama_kategori" class="form-label fw-semibold">Nama Kategori <span class="text-danger">*</span></label>
                        <input type="text" class="form-control @error('nama_kategori') is-invalid @enderror" 
                               id="nama_kategori" name="nama_kategori" value="{{ old('nama_kategori') }}" 
                               placeholder="Contoh: Elektronik, Alat Tulis, Perabotan" required>
                        @error('nama_kategori')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="mb-4">
                        <label for="deskripsi" class="form-label fw-semibold">Deskripsi</label>
                        <textarea class="form-control @error('deskripsi') is-invalid @enderror" 
                                  id="deskripsi" name="deskripsi" rows="4" 
                                  placeholder="Berikan deskripsi singkat tentang kategori ini...">{{ old('deskripsi') }}</textarea>
                        <div class="form-text">Maksimal 500 karakter.</div>
                        @error('deskripsi')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="d-grid gap-2 d-md-flex justify-content-md-end border-top pt-4">
                        <a href="{{ route('categories.index') }}" class="btn btn-light px-4">Batal</a>
                        <button type="submit" class="btn btn-primary px-5 rounded-pill">
                            <i class="fas fa-save me-1"></i> Simpan Kategori
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection
