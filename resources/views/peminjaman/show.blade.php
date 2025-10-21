@extends('layouts.main', ['titlePage' => 'Detail Peminjaman'])

@section('content')
<div class="card shadow">
    <div class="card-header bg-info text-white fw-bold">
        Detail Peminjaman
    </div>

    <div class="card-body">
        <table class="table table-bordered mb-4">
            <tr>
                <th width="30%">Kode Pinjam</th>
                <td>{{ $peminjaman->kode_pinjam }}</td>
            </tr>

            <tr>
                <th>Nama Peminjam</th>
                <td>{{ $peminjaman->nama_peminjam }}</td>
            </tr>

            <tr>
                <th>Nomor Telepon</th>
                <td>{{ $peminjaman->no_telepon ?? '-' }}</td>
            </tr>

            <tr>
                <th>Barang Dipinjam</th>
                <td>
                    @php
                        // Ambil semua peminjaman dengan kode pinjam yang sama
                        $listBarang = \App\Models\Peminjaman::where('kode_pinjam', $peminjaman->kode_pinjam)->get();
                    @endphp

                    <ul class="mb-0">
                        @foreach ($listBarang as $item)
                            <li>{{ $item->barang->nama_barang ?? '-' }} (Jumlah: {{ $item->jumlah }})</li>
                        @endforeach
                    </ul>
                </td>
            </tr>

            <tr>
                <th>Tanggal Pinjam</th>
                <td>{{ \Carbon\Carbon::parse($peminjaman->tgl_pinjam)->format('d-m-Y') }}</td>
            </tr>

            <tr>
                <th>Tanggal Kembali</th>
                <td>
                    @if($peminjaman->tgl_kembali)
                        {{ \Carbon\Carbon::parse($peminjaman->tgl_kembali)->format('d-m-Y') }}
                    @elseif($peminjaman->tgl_kembali_rencana)
                        <span class="text-secondary">
                            Rencana: {{ \Carbon\Carbon::parse($peminjaman->tgl_kembali_rencana)->format('d-m-Y') }}
                        </span>
                    @else
                        <span class="text-muted">Belum dikembalikan</span>
                    @endif
                </td>
            </tr>

            <tr>
                <th>Status</th>
                <td>
                    <span class="badge bg-{{ $peminjaman->status == 'dipinjam' ? 'warning text-dark' : 'success' }}">
                        {{ ucfirst($peminjaman->status) }}
                    </span>
                </td>
            </tr>
        </table>

        <div class="d-flex justify-content-between">
            <a href="{{ route('peminjaman.index') }}" class="btn btn-secondary">← Kembali</a>

            @if($peminjaman->status == 'dipinjam')
                <form action="{{ route('peminjaman.kembalikan', $peminjaman->id) }}" method="POST">
                    @csrf
                    <button type="submit" class="btn btn-success">Kembalikan Sekarang</button>
                </form>
            @endif
        </div>
    </div>
</div>
@endsection
