@extends('layouts.main', ['titlePage' => 'Peminjaman'])

@section('content')
<div class="row mb-3">
    <div class="col">
        <x-tombol-tambah label="Tambah Peminjaman" href="{{ route('peminjaman.create') }}" />
        <x-tombol-cetak label="Cetak Laporan Peminjaman" href="{{ route('peminjaman.laporan') }}" />
    </div>
    <div class="col">
        <x-form-search placeholder="Cari nama/kode pinjam..." />
    </div>
</div>

@if (session('success'))
    <div class="alert alert-success">{{ session('success') }}</div>
@endif
@if (session('error'))
    <div class="alert alert-danger">{{ session('error') }}</div>
@endif

<table class="table table-bordered table-hover align-middle">
    <thead class="table-light">
        <tr class="text-center">
            <th style="width: 50px;">#</th>
            <th>Kode Pinjam</th>
            <th>Nama Peminjam</th>
            <th>Barang</th>
            <th>Jumlah</th>
            <th>Tgl Pinjam</th>
            <th>Tgl Kembali</th>
            <th>Status</th>
            <th style="width: 160px;">Aksi</th>
        </tr>
    </thead>
    <tbody>
        @forelse ($peminjamans as $peminjaman)
            <tr class="text-center">
                <td>{{ $loop->iteration }}</td>
                <td>{{ $peminjaman->kode_pinjam }}</td>
                <td>{{ $peminjaman->nama_peminjam }}</td>
                <td>{{ $peminjaman->barang->nama_barang ?? '-' }}</td>
                <td>{{ $peminjaman->jumlah }}</td>

                {{-- Tanggal pinjam --}}
                <td>{{ \Carbon\Carbon::parse($peminjaman->tgl_pinjam)->format('Y-m-d') }}</td>

                {{-- Tanggal kembali --}}
                <td>
                    @if ($peminjaman->tgl_kembali)
                        {{ \Carbon\Carbon::parse($peminjaman->tgl_kembali)->format('Y-m-d') }}
                    @else
                        <span class="text-muted">Belum dikembalikan</span>
                    @endif
                </td>

                {{-- Status --}}
                <td>
                    @if ($peminjaman->status == 'dipinjam')
                        <span class="badge bg-warning text-dark">Dipinjam</span>
                    @else
                        <span class="badge bg-success">Dikembalikan</span>
                    @endif
                </td>

                {{-- Aksi --}}
                <td>
                    <div class="d-flex justify-content-center gap-2">
                        {{-- Tombol Detail --}}
                        <a href="{{ route('peminjaman.show', $peminjaman->id) }}" 
                           class="btn btn-info btn-sm" title="Detail">
                            <i class="bi bi-card-text"></i>
                        </a>

                        {{-- Tombol Edit --}}
                        <a href="{{ route('peminjaman.edit', $peminjaman->id) }}" 
                           class="btn btn-warning btn-sm" title="Edit">
                            <i class="bi bi-pencil"></i>
                        </a>

                        {{-- Tombol Hapus pakai SweetAlert --}}
                        <form id="form-delete-{{ $peminjaman->id }}" 
                              action="{{ route('peminjaman.destroy', $peminjaman->id) }}" 
                              method="POST">
                            @csrf
                            @method('DELETE')
                            <button type="button" 
                                    class="btn btn-danger btn-sm btn-delete" 
                                    data-id="{{ $peminjaman->id }}" 
                                    title="Hapus">
                                <i class="bi bi-x-circle"></i>
                            </button>
                        </form>
                    </div>
                </td>
            </tr>
        @empty
            <tr>
                <td colspan="9" class="text-center text-muted py-3">
                    Belum ada data peminjaman
                </td>
            </tr>
        @endforelse
    </tbody>
</table>

{{-- SweetAlert2 --}}
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

<script>
document.addEventListener('DOMContentLoaded', function () {
    const deleteButtons = document.querySelectorAll('.btn-delete');

    deleteButtons.forEach(button => {
        button.addEventListener('click', function () {
            const id = this.getAttribute('data-id');

            Swal.fire({
                title: 'Konfirmasi Hapus',
                text: 'Apakah Anda yakin ingin menghapus data ini?',
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#d33',
                cancelButtonColor: '#6c757d',
                confirmButtonText: 'Ya, Hapus!',
                cancelButtonText: 'Batal',
                reverseButtons: true
            }).then((result) => {
                if (result.isConfirmed) {
                    document.getElementById(`form-delete-${id}`).submit();
                }
            });
        });
    });
});
</script>
@endsection
