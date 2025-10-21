@extends('layouts.main')

@section('content')
<style>
    /* ====== CUSTOM MODERN STYLE ====== */
    body {
        background-color: #f8f9fb;
    }

    .card-modern {
        border: none;
        border-radius: 16px;
        box-shadow: 0 4px 20px rgba(0,0,0,0.06);
        background: #fff;
        overflow: hidden;
    }

    .card-modern .card-header {
        background: linear-gradient(135deg, #4f8ef7, #6ba8ff);
        color: white;
        border: none;
        padding: 1rem 1.5rem;
    }

    .section-title {
        font-size: 1.1rem;
        font-weight: 600;
        color: #4e5969;
        margin-bottom: 0.75rem;
    }

    .form-control,
    .form-select {
        border-radius: 10px !important;
        border: 1px solid #dee2e6;
        padding: 0.65rem 0.9rem;
        font-size: 0.95rem;
        transition: all 0.2s ease;
    }

    .form-control:focus,
    .form-select:focus {
        box-shadow: 0 0 0 0.2rem rgba(79,142,247,0.25);
        border-color: #4f8ef7;
    }

    .barang-item {
        border: 1px solid #e8ebf0;
        background: #fdfdfd;
        border-radius: 12px;
        padding: 1rem;
        margin-bottom: 1rem;
        transition: all 0.2s ease;
    }

    .barang-item:hover {
        background: #f7f9fc;
        box-shadow: 0 2px 10px rgba(0,0,0,0.03);
    }

    .btn-modern {
        border-radius: 10px;
        padding: 0.6rem 1.2rem;
        font-weight: 500;
        transition: all 0.2s ease;
    }

    .btn-modern:focus {
        box-shadow: none;
    }

    .btn-tambah, .btn-hapus {
        font-size: 1.2rem;
    }
</style>

<div class="container py-5">
    <div class="card card-modern">
        <div class="card-header">
            <h4 class="mb-0 fw-semibold">
                <i class="bi bi-plus-circle me-2"></i>Tambah Peminjaman
            </h4>
        </div>

        <div class="card-body p-4">

            {{-- ALERTS --}}
            @if (session('success'))
                <div class="alert alert-success alert-dismissible fade show rounded-3 mb-4 shadow-sm">
                    <i class="bi bi-check-circle-fill me-2"></i>{{ session('success') }}
                    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                </div>
            @endif

            @if ($errors->any())
                <div class="alert alert-danger rounded-3 mb-4">
                    <strong>Terjadi kesalahan:</strong>
                    <ul class="mb-0 ps-3">
                        @foreach ($errors->all() as $err)
                            <li>{{ $err }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif

            <form action="{{ route('peminjaman.store') }}" method="POST">
                @csrf

                {{-- SECTION 1: DATA PEMINJAM --}}
                <div class="mb-4">
                    <div class="section-title">🧍‍♂️ Data Peminjam</div>
                    <div class="row g-3">
                        <div class="col-md-6">
                            <input type="text" name="nama_peminjam" class="form-control" placeholder="Nama Peminjam" required>
                        </div>
                        <div class="col-md-6">
                            <input type="text" name="no_telepon" class="form-control" placeholder="Nomor Telepon" required>
                        </div>
                    </div>
                </div>

                {{-- SECTION 2: BARANG --}}
                <div class="mb-4">
                    <div class="section-title">📦 Barang yang Dipinjam</div>

                    <div id="barang-container">
                        <div class="barang-item">
                            <div class="row g-3 align-items-end">
                                <div class="col-md-6">
                                    <label class="form-label">Barang</label>
                                    <select name="barang_id[]" class="form-select" required>
                                        <option value="">-- Pilih Barang --</option>
                                        @foreach ($barangs as $barang)
                                            <option value="{{ $barang->id }}">{{ $barang->nama_barang }} (Stok: {{ $barang->jumlah }})</option>
                                        @endforeach
                                    </select>
                                </div>
                                <div class="col-md-4">
                                    <label class="form-label">Jumlah</label>
                                    <input type="number" name="jumlah[]" class="form-control" min="1" placeholder="0" required>
                                </div>
                                <div class="col-md-2 d-grid">
                                    <button type="button" class="btn btn-success btn-modern btn-tambah">
                                        <i class="bi bi-plus-lg"></i>
                                    </button>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                {{-- SECTION 3: DETAIL PINJAM --}}
                <div class="mb-4">
                    <div class="section-title">📅 Detail Peminjaman</div>
                    <div class="row g-3">
                        <div class="col-md-6">
                            <input type="date" name="tgl_pinjam" class="form-control" required>
                        </div>
                        <div class="col-md-6">
                            <input type="date" name="tgl_kembali_rencana" class="form-control" required>
                        </div>
                        <div class="col-md-6">
                            <select name="status" class="form-select" required>
                                <option value="">-- Pilih Status --</option>
                                <option value="dipinjam">Dipinjam</option>
                                <option value="dikembalikan">Dikembalikan</option>
                            </select>
                        </div>
                    </div>
                </div>

                <div class="d-flex justify-content-between mt-4">
                    <a href="{{ route('peminjaman.index') }}" class="btn btn-outline-secondary btn-modern">
                        ← Kembali
                    </a>
                    <button type="submit" class="btn btn-primary btn-modern px-4">
                        💾 Simpan
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

{{-- Script tambah / hapus barang --}}
<script>
document.addEventListener('click', function(e) {
    if (e.target.closest('.btn-tambah')) {
        e.preventDefault();
        const container = document.getElementById('barang-container');
        const item = e.target.closest('.barang-item');
        const clone = item.cloneNode(true);

        clone.querySelectorAll('input, select').forEach(el => el.value = '');
        const btn = clone.querySelector('.btn');
        btn.classList.remove('btn-success', 'btn-tambah');
        btn.classList.add('btn-danger', 'btn-hapus');
        btn.innerHTML = '<i class="bi bi-dash-lg"></i>';

        container.appendChild(clone);
    }

    if (e.target.closest('.btn-hapus')) {
        e.preventDefault();
        e.target.closest('.barang-item').remove();
    }
});
</script>
@endsection
