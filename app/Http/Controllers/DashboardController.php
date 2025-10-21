<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Barang;
use App\Models\Kategori;
use App\Models\Lokasi;
use App\Models\User;
use App\Models\BarangKondisi;
use App\Models\Peminjaman; // tambahkan ini

class DashboardController extends Controller
{
    public function index()
    {
        $jumlahBarang   = Barang::count();
        $jumlahKategori = Kategori::count();
        $jumlahLokasi   = Lokasi::count();
        $jumlahUser     = User::count();
        $jumlahPeminjaman = Peminjaman::select('kode_pinjam')->distinct()->count('kode_pinjam');

        $kondisiBaik = BarangKondisi::where('kondisi', 'Baik')->sum('jumlah');
        $kondisiRusakRingan = BarangKondisi::where('kondisi', 'Rusak Ringan')->sum('jumlah');
        $kondisiRusakBerat = BarangKondisi::where('kondisi', 'Rusak Berat')->sum('jumlah');

        $barangTerbaru = Barang::with(['kategori', 'lokasi'])
            ->latest()
            ->take(5)
            ->get();

        return view('dashboard', compact(
            'jumlahBarang',
            'jumlahKategori',
            'jumlahLokasi',
            'jumlahUser',
            'jumlahPeminjaman', // tambahkan ini juga
            'kondisiBaik',
            'kondisiRusakRingan',
            'kondisiRusakBerat',
            'barangTerbaru'
        ));
    }
}