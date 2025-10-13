<div class="card-body">
    @php
        // Hitung total barang berdasarkan semua kondisi
        $total = $kondisiBaik + $kondisiRusakRingan + $kondisiRusakBerat;

        // Data ringkasan kondisi barang
        $kondisis = [
            [
                'judul' => 'Baik',
                'jumlah' => $total,
                'kondisi' => $kondisiBaik,
                'color' => 'success',
            ],
            [
                'judul' => 'Rusak Ringan',
                'jumlah' => $total,
                'kondisi' => $kondisiRusakRingan,
                'color' => 'warning',
            ],
            [
                'judul' => 'Rusak Berat',
                'jumlah' => $total,
                'kondisi' => $kondisiRusakBerat,
                'color' => 'danger',
            ],
        ];
    @endphp

    @foreach ($kondisis as $kondisi)
        @php extract($kondisi); @endphp
        <x-progress-kondisi 
            judul="{{ $judul }}" 
            jumlah="{{ $jumlah }}" 
            kondisi="{{ $kondisi }}" 
            color="{{ $color }}" 
        />
    @endforeach
</div>