<form action="{{ route('barang.store') }}" method="POST" enctype="multipart/form-data">
    @csrf

    {{-- ALERT ERROR VALIDASI --}}
    @if ($errors->any())
        <div class="alert alert-danger">
            <ul class="mb-0">
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <div class="card shadow-sm border-0">
        <div class="card-body p-4">
            <div class="row g-3">

                {{-- Kode & Nama Barang --}}
                <div class="col-md-6">
                    <label for="kode_barang" class="form-label fw-semibold">Kode Barang</label>
                    <input type="text" class="form-control" name="kode_barang"
                        value="{{ old('kode_barang', $barang->kode_barang ?? '') }}">
                </div>

                <div class="col-md-6">
                    <label for="nama_barang" class="form-label fw-semibold">Nama Barang</label>
                    <input type="text" class="form-control" name="nama_barang"
                        value="{{ old('nama_barang', $barang->nama_barang ?? '') }}">
                </div>

                {{-- Kategori & Lokasi --}}
                <div class="col-md-6">
                    <label for="kategori_id" class="form-label fw-semibold">Kategori</label>
                    <select name="kategori_id" class="form-select">
                        <option value="">Pilih Kategori :</option>
                        @foreach ($kategori as $item)
                            <option value="{{ $item->id }}"
                                {{ old('kategori_id', $barang->kategori_id ?? '') == $item->id ? 'selected' : '' }}>
                                {{ $item->nama_kategori }}
                            </option>
                        @endforeach
                    </select>
                </div>

                <div class="col-md-6">
                    <label for="lokasi_id" class="form-label fw-semibold">Lokasi</label>
                    <select name="lokasi_id" class="form-select">
                        <option value="">Pilih Lokasi :</option>
                        @foreach ($lokasi as $item)
                            <option value="{{ $item->id }}"
                                {{ old('lokasi_id', $barang->lokasi_id ?? '') == $item->id ? 'selected' : '' }}>
                                {{ $item->nama_lokasi }}
                            </option>
                        @endforeach
                    </select>
                </div>

                {{-- Kondisi Barang --}}
                <div class="col-md-4">
                    <label for="kondisi_baik" class="form-label fw-semibold">Kondisi Baik</label>
                    <input type="number" class="form-control" id="kondisi_baik" name="kondisi[Baik]"
                        value="{{ old('kondisi.Baik', $barang->kondisis->where('kondisi', 'Baik')->first()->jumlah ?? 0) }}">
                </div>

                <div class="col-md-4">
                    <label for="kondisi_rusak_ringan" class="form-label fw-semibold">Rusak Ringan</label>
                    <input type="number" class="form-control" id="kondisi_rusak_ringan" name="kondisi[Rusak Ringan]"
                        value="{{ old('kondisi.Rusak Ringan', $barang->kondisis->where('kondisi', 'Rusak Ringan')->first()->jumlah ?? 0) }}">
                </div>

                <div class="col-md-4">
                    <label for="kondisi_rusak_berat" class="form-label fw-semibold">Rusak Berat</label>
                    <input type="number" class="form-control" id="kondisi_rusak_berat" name="kondisi[Rusak Berat]"
                        value="{{ old('kondisi.Rusak Berat', $barang->kondisis->where('kondisi', 'Rusak Berat')->first()->jumlah ?? 0) }}">
                </div>

                {{-- Jumlah & Satuan --}}
                <div class="col-md-6">
                    <label for="jumlah" class="form-label fw-semibold">Jumlah</label>
                    <input type="number" class="form-control" name="jumlah" id="jumlah"
                        value="{{ old('jumlah', $barang->jumlah ?? 0) }}" readonly>
                </div>

                <div class="col-md-6">
                    <label for="satuan" class="form-label fw-semibold">Satuan</label>
                    <input type="text" class="form-control" name="satuan"
                        value="{{ old('satuan', $barang->satuan ?? '') }}">
                </div>

                {{-- Tanggal & Gambar --}}
                <div class="col-md-6">
                    <label for="tanggal_pengadaan" class="form-label fw-semibold">Tanggal Pengadaan</label>
                    <input type="date" class="form-control" name="tanggal_pengadaan"
                        value="{{ old('tanggal_pengadaan', isset($barang->tanggal_pengadaan) ? \Carbon\Carbon::parse($barang->tanggal_pengadaan)->format('Y-m-d') : '') }}">
                </div>

                <div class="col-md-6">
                    <label for="gambar" class="form-label fw-semibold">Gambar Barang</label>
                    <div class="d-flex align-items-center gap-3">
                        <input type="file" class="form-control" name="gambar">
                        @if (!empty($barang->gambar))
                            <img src="{{ asset('gambar-barang/' . $barang->gambar) }}" alt="Gambar Barang"
                                width="90" class="rounded shadow-sm border">
                        @endif
                    </div>
                </div>

                {{-- Sumber Dana --}}
                <div class="col-12">
                    <label for="sumber_dana" class="form-label fw-semibold">Sumber Dana</label>
                    <div class="d-flex flex-wrap gap-2">
                        @php
                            $sumberDana = old('sumber_dana', $barang->sumber_dana ?? '');
                            $isLainnya = !in_array($sumberDana, ['Pemerintah', 'Swadaya', 'Donatur']) && $sumberDana != '';
                        @endphp

                        <select name="sumber_dana" id="sumber_dana" class="form-select w-auto flex-grow-1"
                            onchange="toggleLainnya()">
                            <option value="">Pilih Sumber Dana :</option>
                            <option value="Pemerintah" {{ $sumberDana === 'Pemerintah' ? 'selected' : '' }}>Pemerintah</option>
                            <option value="Swadaya" {{ $sumberDana === 'Swadaya' ? 'selected' : '' }}>Swadaya</option>
                            <option value="Donatur" {{ $sumberDana === 'Donatur' ? 'selected' : '' }}>Donatur</option>
                            <option value="Lainnya" {{ $isLainnya ? 'selected' : '' }}>Lainnya...</option>
                        </select>

                        <input type="text" class="form-control w-auto flex-grow-1" name="sumber_dana_lainnya"
                            id="sumber_dana_lainnya" placeholder="Masukkan sumber dana lainnya..."
                            value="{{ $isLainnya ? $sumberDana : '' }}"
                            style="{{ $isLainnya ? '' : 'display:none;' }}">
                    </div>
                </div>

                {{-- Tombol --}}
                <div class="col-12 text-start mt-4">
                    <button type="submit" class="btn btn-primary px-4">Simpan</button>
                    <a href="{{ route('barang.index') }}" class="btn btn-secondary px-4">Kembali</a>
                </div>

            </div>
        </div>
    </div>
</form>

{{-- Script --}}
<script>
    function hitungJumlah() {
        let baik = parseInt(document.getElementById('kondisi_baik').value) || 0;
        let ringan = parseInt(document.getElementById('kondisi_rusak_ringan').value) || 0;
        let berat = parseInt(document.getElementById('kondisi_rusak_berat').value) || 0;
        document.getElementById('jumlah').value = baik + ringan + berat;
    }

    document.getElementById('kondisi_baik').addEventListener('input', hitungJumlah);
    document.getElementById('kondisi_rusak_ringan').addEventListener('input', hitungJumlah);
    document.getElementById('kondisi_rusak_berat').addEventListener('input', hitungJumlah);
    hitungJumlah();

    function toggleLainnya() {
        const dropdown = document.getElementById('sumber_dana');
        const inputLainnya = document.getElementById('sumber_dana_lainnya');
        if (dropdown.value === 'Lainnya') {
            inputLainnya.style.display = 'block';
        } else {
            inputLainnya.style.display = 'none';
            inputLainnya.value = '';
        }
    }
</script>
