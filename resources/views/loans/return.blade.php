@extends('layouts.app')

@section('title', 'Kembalikan Barang')

@section('content')
<div class="container">
    <h1>Kembalikan Barang</h1>
    <div class="card mb-3">
        <div class="card-body">
            <h5 class="card-title">Kode Peminjaman: {{ $loan->kode_peminjaman }}</h5>
            <p class="card-text">Nama Barang: {{ $loan->item->nama_barang ?? '-' }}</p>
            <p class="card-text">Peminjam: {{ $loan->user->name ?? '-' }}</p>
            <p class="card-text">Tanggal Pinjam: {{ $loan->tanggal_pinjam->format('d/m/Y') }}</p>
            <p class="card-text">Tanggal Kembali Rencana: {{ $loan->tanggal_kembali_rencana->format('d/m/Y') }}</p>
        </div>
    </div>
    <form method="POST" action="{{ route('loans.return', $loan) }}">
        @csrf
        <div class="mb-3">
            <label for="catatan" class="form-label">Catatan Pengembalian (opsional)</label>
            <textarea name="catatan" id="catatan" class="form-control">{{ old('catatan', $loan->catatan) }}</textarea>
        </div>
        <button type="submit" class="btn btn-success">Konfirmasi Pengembalian</button>
        <a href="{{ route('loans.index') }}" class="btn btn-secondary">Batal</a>
    </form>
</div>
@endsection
