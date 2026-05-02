@extends('layouts.app')

@section('content')
<div class="container-fluid">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h1>Daftar Barang</h1>
        @can('create', App\Models\Item::class)
        <a href="{{ route('items.create') }}" class="btn btn-primary">
            <i class="fas fa-plus"></i> Tambah Barang
        </a>
        @endcan
    </div>

    <!-- Filter -->
    <div class="card mb-4">
        <div class="card-body">
            <form method="GET" class="row g-3">
                <div class="col-md-4">
                    <input type="text" name="search" class="form-control" placeholder="Cari barang..." value="{{ request('search') }}">
                </div>
                <div class="col-md-3">
                    <select name="category" class="form-select">
                        <option value="">Semua Kategori</option>
                        @foreach($categories as $category)
                        <option value="{{ $category->id }}" @selected(request('category') == $category->id)>
                            {{ $category->nama_kategori }}
                        </option>
                        @endforeach
                    </select>
                </div>
                <div class="col-md-3">
                    <select name="status" class="form-select">
                        <option value="">Semua Status</option>
                        <option value="tersedia" @selected(request('status') == 'tersedia')>Tersedia</option>
                        <option value="dipinjam" @selected(request('status') == 'dipinjam')>Sedang Dipinjam</option>
                        <option value="perbaikan" @selected(request('status') == 'perbaikan')>Dalam Perbaikan</option>
                        <option value="hilang" @selected(request('status') == 'hilang')>Hilang</option>
                    </select>
                </div>
                <div class="col-md-2">
                    <button type="submit" class="btn btn-info w-100">
                        <i class="fas fa-search"></i> Cari
                    </button>
                </div>
            </form>
        </div>
    </div>

    <!-- Items List -->
    <div class="row">
        @forelse($items as $item)
        <div class="col-md-4 mb-4">
            <div class="card h-100">
                @if($item->gambar)
                <div style="height: 200px; overflow: hidden;">
                    <img src="{{ asset('storage/' . $item->gambar) }}" class="card-img-top" style="object-fit: cover; width: 100%; height: 100%;" alt="{{ $item->nama_barang }}">
                </div>
                @else
                <div style="height: 200px; background-color: #f0f0f0; display: flex; align-items: center; justify-content: center;">
                    <i class="fas fa-image text-muted fa-3x"></i>
                </div>
                @endif
                
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-start mb-2">
                        <div>
                            <h5 class="card-title">{{ $item->nama_barang }}</h5>
                            <p class="text-muted small mb-0">{{ $item->kode_barang }}</p>
                        </div>
                        <span class="badge {{ $item->getStatusBadgeClass() }}">
                            <i class="{{ $item->getStatusIcon() }}"></i> {{ $item->getStatusLabel() }}
                        </span>
                    </div>
                    
                    @if($item->category)
                    <p class="text-muted small mb-2">
                        <i class="fas fa-tag"></i> {{ $item->category->nama_kategori }}
                    </p>
                    @endif
                    
                    @if($item->lokasi)
                    <p class="text-muted small mb-2">
                        <i class="fas fa-map-marker-alt"></i> {{ $item->lokasi }}
                    </p>
                    @endif
                    
                    <p class="card-text small">{{ Str::limit($item->deskripsi, 100) }}</p>
                </div>
                
                <div class="card-footer bg-transparent">
                    <div class="btn-group w-100" role="group">
                        <a href="{{ route('items.show', $item) }}" class="btn btn-sm btn-info">
                            <i class="fas fa-eye"></i> Detail
                        </a>
                        @can('borrow', $item)
                        <a href="{{ route('loans.create', $item) }}" class="btn btn-sm btn-success">
                            <i class="fas fa-handshake"></i> Pinjam
                        </a>
                        @endcan
                        @can('update', $item)
                        <a href="{{ route('items.edit', $item) }}" class="btn btn-sm btn-warning">
                            <i class="fas fa-edit"></i> Edit
                        </a>
                        @endcan
                        @can('delete', $item)
                        <form action="{{ route('items.destroy', $item) }}" method="POST" style="display: contents;" onsubmit="return confirm('Apakah Anda yakin ingin menghapus barang ini?');">
                            @csrf
                            @method('DELETE')
                            <button type="submit" class="btn btn-sm btn-danger" style="border-top-left-radius: 0; border-bottom-left-radius: 0;">
                                <i class="fas fa-trash"></i> Hapus
                            </button>
                        </form>
                        @endcan
                    </div>
                </div>
            </div>
        </div>
        @empty
        <div class="col-12">
            <div class="alert alert-info text-center">
                <i class="fas fa-inbox fa-2x"></i>
                <p class="mt-2">Tidak ada barang yang ditemukan</p>
            </div>
        </div>
        @endforelse
    </div>

    <!-- Pagination -->
    <div class="d-flex justify-content-center">
        {{ $items->links() }}
    </div>
</div>
@endsection
