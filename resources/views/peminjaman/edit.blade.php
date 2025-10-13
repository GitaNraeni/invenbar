@extends('layouts.main', ['titlePage' => 'Edit Peminjaman'])

@section('content')
<div class="card">
    <div class="card-header bg-warning text-dark fw-bold">
        Edit Data Peminjaman
    </div>

    <div class="card-body">
        <form action="{{ route('peminjaman.update', $peminjaman->id) }}" method="POST">
            @csrf
            @method('PUT')

            <div class="mb-3">
                <label for="nama_peminjam" class="form-label">Nama Peminjam</label>
                <input type="text" name="nama_peminjam" id="nama_peminjam"
                    class="form-control @error('nama_peminjam') is-invalid @enderror"
                    value="{{ old('nama_peminjam', $peminjaman->nama_peminjam) }}" required>
                @error('nama_peminjam')
                    <div class="invalid-feedback">{{ $message }}</div>
                @enderror
            </div>

            <div class="mb-3">
                <label for="barang_id" class="form-label">Barang</label>
                <select name="barang_id" id="barang_id"
                    class="form-select @error('barang_id') is-invalid @enderror" required>
                    <option value="">-- Pilih Barang --</option>
                    @foreach ($barangs as $barang)
                        <option value="{{ $barang->id }}"
                            {{ old('barang_id', $peminjaman->barang_id) == $barang->id ? 'selected' : '' }}>
                            {{ $barang->nama_barang }}
                        </option>
                    @endforeach
                </select>
                @error('barang_id')
                    <div class="invalid-feedback">{{ $message }}</div>
                @enderror
            </div>

            <div class="mb-3">
                <label for="jumlah" class="form-label">Jumlah</label>
                <input type="number" name="jumlah" id="jumlah" min="1"
                    class="form-control @error('jumlah') is-invalid @enderror"
                    value="{{ old('jumlah', $peminjaman->jumlah) }}" required>
                @error('jumlah')
                    <div class="invalid-feedback">{{ $message }}</div>
                @enderror
            </div>

            <div class="mb-3">
                <label for="tgl_pinjam" class="form-label">Tanggal Pinjam</label>
                <input type="date" name="tgl_pinjam" id="tgl_pinjam"
                    class="form-control @error('tgl_pinjam') is-invalid @enderror"
                    value="{{ old('tgl_pinjam', $peminjaman->tgl_pinjam) }}" required>
                @error('tgl_pinjam')
                    <div class="invalid-feedback">{{ $message }}</div>
                @enderror
            </div>

            {{-- kalau mau ubah status manual --}}
            <div class="mb-3">
                <label for="status" class="form-label">Status</label>
                <select name="status" id="status" class="form-select">
                    <option value="dipinjam" {{ $peminjaman->status == 'dipinjam' ? 'selected' : '' }}>Dipinjam</option>
                    <option value="dikembalikan" {{ $peminjaman->status == 'dikembalikan' ? 'selected' : '' }}>Dikembalikan</option>
                </select>
            </div>

            <div class="d-flex justify-content-between">
                <a href="{{ route('peminjaman.index') }}" class="btn btn-secondary">Kembali</a>
                <button type="submit" class="btn btn-warning fw-bold text-dark">Update</button>
            </div>
        </form>
    </div>
</div>
@endsection