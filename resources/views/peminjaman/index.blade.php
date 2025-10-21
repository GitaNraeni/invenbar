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
            <th style="width: 50px;">No</th>
            <th>Kode Pinjam</th>
            <th>Nama Peminjam</th>
            <th>No Telepon</th>
            <th>Barang</th>
            <th>Jumlah</th>
            <th>Tgl Pinjam</th>
            <th>Tgl Kembali</th>
            <th>Status</th>
            <th style="width: 200px;">Aksi</th>
        </tr>
    </thead>
    <tbody>
                @forelse ($peminjamans as $kode => $items)
            @php
                $first = $items->first();
                $namaBarang = $items->pluck('barang.nama_barang')->implode(', ');
                $totalJumlah = $items->sum('jumlah');
            @endphp
            <tr class="text-center">
                <td>{{ $loop->iteration }}</td>
                <td>{{ $kode }}</td>
                <td>{{ $first->nama_peminjam }}</td>
        <td>{{ $first->no_telepon ?? '-' }}</td>
        <td>{{ $namaBarang }}</td>
                <td>{{ $totalJumlah }}</td>
                <td>{{ \Carbon\Carbon::parse($first->tgl_pinjam)->format('Y-m-d') }}</td>
                <td>
                    @if ($first->tgl_kembali)
                        {{ \Carbon\Carbon::parse($first->tgl_kembali)->format('Y-m-d') }}
                    @elseif ($first->tgl_kembali_rencana)
                        <span class="text-secondary">
                            Rencana: {{ \Carbon\Carbon::parse($first->tgl_kembali_rencana)->format('Y-m-d') }}
                        </span>
                    @else
                        <span class="text-muted">Belum dikembalikan</span>
                    @endif
                </td>
                <td>
                    @if ($first->status == 'dipinjam')
                        <span class="badge bg-warning text-dark">Dipinjam</span>
                    @else
                        <span class="badge bg-success">Dikembalikan</span>
                    @endif
                </td>
                <td>
                    <div class="d-flex justify-content-center gap-2">
                        <a href="{{ route('peminjaman.show', $first->id) }}" class="btn btn-info btn-sm" title="Detail">
                            <i class="bi bi-card-text"></i>
                        </a>
                        <a href="{{ route('peminjaman.edit', $first->id) }}" class="btn btn-warning btn-sm" title="Edit">
                            <i class="bi bi-pencil"></i>
                        </a>
                        @if ($first->status == 'dipinjam')
                        <form id="form-kembali-{{ $first->id }}" action="{{ route('peminjaman.kembalikan', $first->id) }}" method="POST"> @csrf @method('PUT') <button type="button" class="btn btn-success btn-sm btn-kembalikan" data-id="{{ $first->id }}" title="Kembalikan Barang"> <i class="bi bi-arrow-return-left"></i> </button> </form>
                        @endif
                        <form id="form-delete-{{ $first->id }}" action="{{ route('peminjaman.destroy', $first->id) }}" method="POST">
                            @csrf
                            @method('DELETE')
                            <button type="button" class="btn btn-danger btn-sm btn-delete" data-id="{{ $first->id }}" title="Hapus">
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
    // Tombol Hapus
    document.querySelectorAll('.btn-delete').forEach(button => {
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
                    document.getElementById(form-delete-${id}).submit();
                }
            });
        });
    });

    // Tombol Kembalikan Barang
    document.querySelectorAll('.btn-kembalikan').forEach(button => {
        button.addEventListener('click', function () {
            const id = this.getAttribute('data-id');
            Swal.fire({
                title: 'Konfirmasi Pengembalian',
                text: 'Apakah barang ini sudah dikembalikan?',
                icon: 'question',
                showCancelButton: true,
                confirmButtonColor: '#28a745',
                cancelButtonColor: '#6c757d',
                confirmButtonText: 'Ya, Kembalikan',
                cancelButtonText: 'Batal',
                reverseButtons: true
            }).then((result) => {
                if (result.isConfirmed) {
                    document.getElementById(form-kembali-${id}).submit();
                }
            });
        });
    });
});
</script>
@endsection