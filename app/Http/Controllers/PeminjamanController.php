<?php

namespace App\Http\Controllers;

use App\Models\Peminjaman;
use App\Models\Barang;
use Illuminate\Http\Request;
use Barryvdh\DomPDF\Facade\Pdf;

class PeminjamanController extends Controller
{
    public function index(Request $request)
    {
        $search = $request->search;

        $peminjamans = Peminjaman::with('barang')
            ->when($search, function($query) use ($search) {
                $query->where('nama_peminjam', 'like', "%$search%")
                      ->orWhere('kode_pinjam', 'like', "%$search%");
            })
            ->latest()
            ->get();

        return view('peminjaman.index', compact('peminjamans'));
    }

    public function create()
    {
        $barangs = Barang::all();
        return view('peminjaman.create', compact('barangs'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'nama_peminjam' => 'required',
            'barang_id'     => 'required|exists:barangs,id',
            'jumlah'        => 'required|integer|min:1',
            'tgl_pinjam'    => 'required|date',
        ]);

        $barang = Barang::findOrFail($request->barang_id);

        // cek stok barang
        if ($barang->jumlah < $request->jumlah) {
            return redirect()->back()->with('error', 'Stok barang tidak mencukupi!');
        }

        $kode = 'PJ' . time();

        // simpan data peminjaman
        Peminjaman::create([
            'kode_pinjam'   => $kode,
            'nama_peminjam' => $request->nama_peminjam,
            'barang_id'     => $request->barang_id,
            'jumlah'        => $request->jumlah,
            'tgl_pinjam'    => $request->tgl_pinjam,
            'status'        => 'dipinjam',
        ]);

        // kurangi stok barang
        $barang->decrement('jumlah', $request->jumlah);

        return redirect()->route('peminjaman.index')->with('success', 'Peminjaman berhasil ditambahkan!');
    }

    public function kembalikan($id)
    {
        $peminjaman = Peminjaman::with('barang')->findOrFail($id);

        // balikin stok barang
        $barang = $peminjaman->barang;
        $barang->increment('jumlah', $peminjaman->jumlah);

        // update status peminjaman
        $peminjaman->update([
            'tgl_kembali' => now(),
            'status'      => 'dikembalikan',
        ]);

        return redirect()->route('peminjaman.index')->with('success', 'Barang berhasil dikembalikan!');
    }

    public function cetakLaporan()
    {
        $peminjamans = Peminjaman::with('barang')->get();
        $pdf = Pdf::loadView('peminjaman.laporan', compact('peminjamans'))
                ->setPaper('a4', 'portrait');

        return $pdf->stream('Laporan_Peminjaman_' . now()->format('d-m-Y') . '.pdf');
    }
    
    public function edit($id)
    {
        $peminjaman = Peminjaman::findOrFail($id);
        $barangs = Barang::all();
        return view('peminjaman.edit', compact('peminjaman', 'barangs'));
    }

    public function update(Request $request, $id)
    {
        $request->validate([
            'nama_peminjam' => 'required',
            'barang_id'     => 'required|exists:barangs,id',
            'jumlah'        => 'required|integer|min:1',
            'tgl_pinjam'    => 'required|date',
            'status'        => 'required|in:dipinjam,dikembalikan',
        ]);

        $peminjaman = Peminjaman::findOrFail($id);
        $barang = Barang::findOrFail($request->barang_id);

        // kalau status dikembalikan dan sebelumnya masih dipinjam, stok dikembalikan
        if ($request->status === 'dikembalikan' && $peminjaman->status === 'dipinjam') {
            $barang->increment('jumlah', $peminjaman->jumlah);
        }

        // kalau status dipinjam lagi tapi sebelumnya dikembalikan, stok dikurangi
        if ($request->status === 'dipinjam' && $peminjaman->status === 'dikembalikan') {
            if ($barang->jumlah < $request->jumlah) {
                return redirect()->back()->with('error', 'Stok barang tidak mencukupi!');
            }
            $barang->decrement('jumlah', $request->jumlah);
        }

        // update data peminjaman
        $peminjaman->update([
            'nama_peminjam' => $request->nama_peminjam,
            'barang_id'     => $request->barang_id,
            'jumlah'        => $request->jumlah,
            'tgl_pinjam'    => $request->tgl_pinjam,
            'tgl_kembali'   => $request->status === 'dikembalikan' ? now() : null,
            'status'        => $request->status,
        ]);

        return redirect()->route('peminjaman.index')->with('success', 'Data peminjaman berhasil diperbarui!');
    }

    public function show($id)
    {
        $peminjaman = Peminjaman::with('barang')->findOrFail($id);
        return view('peminjaman.show', compact('peminjaman'));
    }

    public function destroy($id)
    {
        $peminjaman = Peminjaman::findOrFail($id);

        // Kalau masih dipinjam, balikin dulu stok barang
        if ($peminjaman->status == 'dipinjam') {
            $barang = $peminjaman->barang;
            $barang->increment('jumlah', $peminjaman->jumlah);
        }

        $peminjaman->delete();

        return redirect()->route('peminjaman.index')->with('success', 'Data peminjaman berhasil dihapus!');
    }
}