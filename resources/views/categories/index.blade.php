@extends('layouts.app')

@section('content')
<div class="row mb-4 align-items-center">
    <div class="col-md-6">
        <h2 class="mb-0 text-primary-emphasis fw-bold">
            <i class="fas fa-tags me-2"></i>Kelola Kategori
        </h2>
    </div>
    <div class="col-md-6 text-end">
        <a href="{{ route('categories.create') }}" class="btn btn-primary px-4 py-2 rounded-pill shadow-sm">
            <i class="fas fa-plus me-1"></i> Tambah Kategori
        </a>
    </div>
</div>

<div class="custom-card border-0 shadow-sm overflow-hidden">
    <div class="card-header bg-white py-3 border-0">
        <div class="row align-items-center">
            <div class="col">
                <h5 class="card-title mb-0 fw-semibold text-muted">Daftar Kategori</h5>
            </div>
            <div class="col-md-4">
                <form action="{{ route('categories.index') }}" method="GET">
                    <div class="input-group input-group-sm">
                        <input type="text" name="search" class="form-control rounded-start-pill" placeholder="Cari kategori..." value="{{ request('search') }}">
                        <button class="btn btn-primary rounded-end-pill px-3" type="submit">
                            <i class="fas fa-search"></i>
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
    <div class="card-body p-0">
        <div class="table-responsive">
            <table class="table table-hover align-middle mb-0">
                <thead class="bg-light text-muted small text-uppercase">
                    <tr>
                        <th class="ps-4" style="width: 80px;">No</th>
                        <th>Nama Kategori</th>
                        <th>Deskripsi</th>
                        <th class="text-center">Total Barang</th>
                        <th class="text-end pe-4">Aksi</th>
                    </tr>
                </thead>
                <tbody class="border-top-0">
                    @forelse($categories as $index => $category)
                        <tr>
                            <td class="ps-4 fw-medium text-muted">{{ $categories->firstItem() + $index }}</td>
                            <td>
                                <span class="fw-bold text-dark">{{ $category->nama_kategori }}</span>
                            </td>
                            <td>
                                <span class="text-muted small">{{ Str::limit($category->deskripsi ?? '-', 50) }}</span>
                            </td>
                            <td class="text-center">
                                <span class="badge rounded-pill bg-info-subtle text-info px-3">
                                    {{ $category->items_count }}
                                </span>
                            </td>
                            <td class="text-end pe-4">
                                <div class="btn-group">
                                    <a href="{{ route('categories.edit', $category) }}" class="btn btn-sm btn-outline-warning border-0" title="Edit">
                                        <i class="fas fa-edit"></i>
                                    </a>
                                    <form action="{{ route('categories.destroy', $category) }}" method="POST" class="d-inline" onsubmit="return confirm('Apakah Anda yakin ingin menghapus kategori ini?')">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="btn btn-sm btn-outline-danger border-0" title="Hapus">
                                            <i class="fas fa-trash"></i>
                                        </button>
                                    </form>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="5" class="text-center py-5">
                                <div class="text-muted">
                                    <i class="fas fa-folder-open fa-3x mb-3 opacity-25"></i>
                                    <p class="mb-0">Belum ada data kategori.</p>
                                </div>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
    @if($categories->hasPages())
        <div class="card-footer bg-white py-3 border-0">
            {{ $categories->links() }}
        </div>
    @endif
</div>

<style>
    .bg-info-subtle {
        background-color: #e0f7fa !important;
        color: #00838f !important;
    }
    .table-hover tbody tr:hover {
        background-color: rgba(44, 152, 160, 0.03);
    }
    .btn-group .btn:hover {
        background-color: rgba(0,0,0,0.05);
    }
</style>
@endsection
