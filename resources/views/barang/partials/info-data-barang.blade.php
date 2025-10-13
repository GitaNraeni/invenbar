<table class="table table-bordered table-striped">
    <tbody>
        <tr>
            <th style="width: 30%;">Nama Barang</th>
            <td>{{ $barang->nama_barang }}</td>
        </tr>
        <tr>
            <th>Kategori</th>
            <td>{{ $barang->kategori->nama_kategori }}</td>
        </tr>
        <tr>
            <th>Lokasi</th>
            <td>{{ $barang->lokasi->nama_lokasi }}</td>
        </tr>
        <tr>
            <th>Sumber Dana</th>
            <td>{{ $barang->sumber_dana ?? '-' }}</td>
        </tr>
        <tr>
            <th>Jumlah</th>
            <td>{{ $barang->jumlah }} {{ $barang->satuan }}</td>
        </tr>
        <tr>
            <th>Kondisi</th>
            <td>
                @if ($barang->kondisis->count() > 0) 
                    @foreach ($barang->kondisis as $kondisi)
                     @php
                        $class = 'bg-success';
                        if ($kondisi->kondisi === 'Rusak Ringan'){
                            $class = 'bg-warning text-dark';
                        } elseif ($kondisi->kondisi === 'Rusak Berat') {
                            $class = 'bg-danger';
                        }
                    @endphp
                <span class="badge {{ $class }}">
                    {{ $kondisi->kondisi }}: {{ $kondisi->jumlah }}
                </span>
                @endforeach
            @else
                <span class="badge bg-secondary">Tidak Ada Data</span>
            @endif
            </td>
        </tr>
        <tr>
            <th>Tanggal Pengadaan</th>
            <td>{{ \Carbon\Carbon::parse($barang->tanggal_pengadaan)->translatedFormat('d F Y') }}
            </td>
        </tr>
        <tr>
            <th>Terakhir Diperbarui</th>
            <td>{{ $barang->updated_at->translatedFormat('d F Y, H:i') }}</td>
        </tr>

    </tbody>
</table>