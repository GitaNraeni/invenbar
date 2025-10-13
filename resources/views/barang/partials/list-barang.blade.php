<x-table-list>
    <x-slot name="header">
        <tr class="text-center align-middle">
            <th style="width: 50px;">#</th>
            <th style="width: 90px;">Kode</th>
            <th>Nama Barang</th>
            <th>Kategori</th>
            <th>Lokasi</th>
            <th>Sumber Dana</th>
            <th style="width: 90px;">Jumlah</th>
            <th style="width: 200px;">Kondisi</th>
            <th style="width: 130px;">Tanggal Pengadaan</th>
            <th style="width: 120px;">Aksi</th>
        </tr>
    </x-slot>

    @forelse ($barangs as $index => $barang)
        <tr class="align-middle">
            <td class="text-center">{{ $barangs->firstItem() + $index }}</td>
            <td class="text-center fw-semibold">{{ $barang->kode_barang }}</td>
            <td>{{ $barang->nama_barang }}</td>
            <td class="text-center">{{ $barang->kategori->nama_kategori ?? '-' }}</td>
            <td class="text-center">{{ $barang->lokasi->nama_lokasi ?? '-' }}</td>
            <td class="text-center">{{ $barang->sumber_dana ?? '-' }}</td>
            <td class="text-center">{{ $barang->jumlah ?? 0 }} {{ $barang->satuan ?? '' }}</td>

            <td>
                <div class="d-flex justify-content-center flex-wrap gap-1">
                    @forelse ($barang->kondisis as $kondisi)
                        <span class="badge rounded-pill
                            @if ($kondisi->kondisi == 'Baik') bg-success
                            @elseif ($kondisi->kondisi == 'Rusak Ringan') bg-warning text-dark
                            @elseif ($kondisi->kondisi == 'Rusak Berat') bg-danger
                            @else bg-secondary @endif">
                            {{ $kondisi->kondisi }}: {{ $kondisi->jumlah }}
                        </span>
                    @empty
                        <span class="badge bg-secondary">Belum ada data</span>
                    @endforelse
                </div>
            </td>

            <td class="text-center">{{ $barang->tanggal_pengadaan?->format('d-m-Y') }}</td>
            
            <td class="text-center">
                <div class="d-flex justify-content-center gap-1">
                    @can('manage barang')
                        <x-tombol-aksi href="{{ route('barang.show', $barang->id) }}" type="show" />
                        <x-tombol-aksi href="{{ route('barang.edit', $barang->id) }}" type="edit" />
                    @endcan

                    @can('delete barang')
                        <x-tombol-aksi href="{{ route('barang.destroy', $barang->id) }}" type="delete" />
                    @endcan
                </div>
            </td>
        </tr>
    @empty
        <tr>
            <td colspan="10" class="text-center">
                <div class="alert alert-danger mb-0">
                    Data barang belum tersedia.
                </div>
            </td>
        </tr>
    @endforelse
</x-table-list>
