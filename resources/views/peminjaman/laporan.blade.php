<!DOCTYPE html>
<html>
<head>
    <title>Laporan Data Peminjaman</title>
    <style>
        body { font-family: Arial, sans-serif; font-size: 12px; }
        h2 { text-align: center; margin-bottom: 10px; }
        table { width: 100%; border-collapse: collapse; margin-top: 10px; }
        th, td { border: 1px solid #000; padding: 6px; text-align: center; vertical-align: middle; }
        th { background-color: #f2f2f2; }
    </style>
</head>
<body>
    <h2>Laporan Data Peminjaman</h2>
    <p style="text-align:right;">Tanggal Cetak: {{ now()->format('d-m-Y') }}</p>

    @php
        $grouped = $peminjamans->groupBy('kode_pinjam');
    @endphp

    <table>
        <thead>
            <tr>
                <th>No</th>
                <th>Kode Pinjam</th>
                <th>Nama Peminjam</th>
                <th>No Telepon</th>
                <th>Barang</th>
                <th>Jumlah</th>
                <th>Tanggal Pinjam</th>
                <th>Tanggal Kembali</th>
                <th>Status</th>
            </tr>
        </thead>
        <tbody>
            @foreach ($grouped as $kode => $items)
                @php
                    $first = $items->first();
                    $barangs = $items->map(fn($i) => $i->barang->nama_barang ?? '-')->implode(', ');
                    $totalJumlah = $items->sum('jumlah');
                    $tglKembali = $first->tgl_kembali
                        ? \Carbon\Carbon::parse($first->tgl_kembali)->format('d-m-Y')
                        : ($first->tgl_kembali_rencana
                            ? 'Rencana: ' . \Carbon\Carbon::parse($first->tgl_kembali_rencana)->format('d-m-Y')
                            : '-');
                @endphp
                <tr>
                    <td>{{ $loop->iteration }}</td>
                    <td>{{ $kode }}</td>
                    <td>{{ $first->nama_peminjam }}</td>
                    <td>{{ $first->no_telepon }}</td>
                    <td>{{ $barangs }}</td>
                    <td>{{ $totalJumlah }}</td>
                    <td>{{ \Carbon\Carbon::parse($first->tgl_pinjam)->format('d-m-Y') }}</td>
                    <td>{{ $tglKembali }}</td>
                    <td>{{ ucfirst($first->status) }}</td>
                </tr>
            @endforeach
        </tbody>
    </table>
</body>
</html>
