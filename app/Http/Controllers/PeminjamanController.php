<?php

namespace App\Http\Controllers;

use App\Models\Peminjaman;
use App\Models\Barang;
use App\Models\BarangKondisi;
use Illuminate\Http\Request;
use Barryvdh\DomPDF\Facade\Pdf;

class PeminjamanController extends Controller
{
    public function index(Request $request)
    {
        $search = $request->search;

        $peminjamans = Peminjaman::with('barang')
            ->when($search, function ($query) use ($search) {
                $query->where('nama_peminjam', 'like', "%$search%")
                      ->orWhere('kode_pinjam', 'like', "%$search%");
            })
            ->orderBy('kode_pinjam', 'desc')
            ->get()
            ->groupBy('kode_pinjam');

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
            'nama_peminjam'         => 'required',
            'no_telepon'            => 'required',
            'barang_id'             => 'required|array',
            'barang_id.*'           => 'exists:barangs,id',
            'jumlah'                => 'required|array',
            'jumlah.*'              => 'required|integer|min:1',
            'tgl_pinjam'            => 'required|date',
            'tgl_kembali_rencana'   => 'nullable|date|after_or_equal:tgl_pinjam',
            'status'                => 'required|in:dipinjam,dikembalikan',
        ]);

        $kode = 'PJ' . time();

        foreach ($request->barang_id as $index => $barangId) {
            $barang = Barang::findOrFail($barangId);
            $jumlah = $request->jumlah[$index];

            // cek stok kondisi baik
            $stokBaik = BarangKondisi::where('barang_id', $barangId)
                ->where('kondisi', 'baik')
                ->value('jumlah') ?? 0;

            if ($stokBaik < $jumlah) {
                return redirect()->back()->with('error', "Stok barang {$barang->nama_barang} tidak mencukupi!");
            }

            // simpan data
            Peminjaman::create([
                'kode_pinjam'           => $kode,
                'nama_peminjam'         => $request->nama_peminjam,
                'no_telepon'            => $request->no_telepon,
                'barang_id'             => $barangId,
                'jumlah'                => $jumlah,
                'tgl_pinjam'            => $request->tgl_pinjam,
                'tgl_kembali_rencana'   => $request->tgl_kembali_rencana,
                'status'                => $request->status,
            ]);

            // kurangi stok kondisi baik
            if ($request->status === 'dipinjam') {
                BarangKondisi::where('barang_id', $barangId)
                    ->where('kondisi', 'baik')
                    ->decrement('jumlah', $jumlah);
            }

            // sync jumlah total di tabel barang
            $barang->update([
                'jumlah' => BarangKondisi::where('barang_id', $barangId)->sum('jumlah')
            ]);
        }

        return redirect()->route('peminjaman.index')->with('success', 'Peminjaman berhasil ditambahkan!');
    }

    public function kembalikan($id)
    {
        $peminjaman = Peminjaman::findOrFail($id);

        // ambil semua peminjaman dengan kode sama
        $semuaPeminjaman = Peminjaman::where('kode_pinjam', $peminjaman->kode_pinjam)
            ->with('barang')
            ->get();

        foreach ($semuaPeminjaman as $item) {
            // balikin stok kondisi baik
            BarangKondisi::where('barang_id', $item->barang_id)
                ->where('kondisi', 'baik')
                ->increment('jumlah', $item->jumlah);

            // update status & tanggal kembali
            $item->update([
                'tgl_kembali' => now(),
                'status'      => 'dikembalikan',
            ]);

            // sync total di tabel barang
            $item->barang->update([
                'jumlah' => BarangKondisi::where('barang_id', $item->barang_id)->sum('jumlah')
            ]);
        }

        return redirect()->route('peminjaman.index')->with('success', 'Semua barang pada kode pinjam ini telah dikembalikan!');
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
        'no_telepon'    => 'required',
        'tgl_pinjam'    => 'required|date',
        'status'        => 'required|in:dipinjam,dikembalikan',
    ]);

    $peminjaman = Peminjaman::findOrFail($id);

    // ambil semua peminjaman yang punya kode sama
    $semuaPeminjaman = Peminjaman::where('kode_pinjam', $peminjaman->kode_pinjam)
        ->with('barang')
        ->get();

    foreach ($semuaPeminjaman as $item) {
        // kalau status diubah ke dikembalikan
        if ($request->status === 'dikembalikan' && $item->status === 'dipinjam') {
            BarangKondisi::where('barang_id', $item->barang_id)
                ->where('kondisi', 'baik')
                ->increment('jumlah', $item->jumlah);

            $item->update([
                'tgl_kembali' => now(),
                'status' => 'dikembalikan',
            ]);
        }

        // kalau status diubah ke dipinjam lagi
        elseif ($request->status === 'dipinjam' && $item->status === 'dikembalikan') {
            $stokBaik = BarangKondisi::where('barang_id', $item->barang_id)
                ->where('kondisi', 'baik')
                ->value('jumlah') ?? 0;

            if ($stokBaik < $item->jumlah) {
                return redirect()->back()->with('error', 'Stok barang tidak mencukupi untuk meminjam ulang!');
            }

            BarangKondisi::where('barang_id', $item->barang_id)
                ->where('kondisi', 'baik')
                ->decrement('jumlah', $item->jumlah);

            $item->update([
                'tgl_kembali' => null,
                'status' => 'dipinjam',
            ]);
        }

        // update stok total barang
        $item->barang->update([
            'jumlah' => BarangKondisi::where('barang_id', $item->barang_id)->sum('jumlah')
        ]);

        // update data umum (nama, tgl_pinjam, dst)
        $item->update([
            'nama_peminjam' => $request->nama_peminjam,
            'no_telepon' => $request->no_telepon,
            'tgl_pinjam' => $request->tgl_pinjam,
        ]);
    }

    return redirect()->route('peminjaman.index')->with('success', 'Data peminjaman berhasil diperbarui!');
}


    public function cetakLaporan()
    {
        $peminjamans = Peminjaman::with('barang')->get();
        $pdf = Pdf::loadView('peminjaman.laporan', compact('peminjamans'))
            ->setPaper('a4', 'portrait');

        return $pdf->stream('Laporan_Peminjaman_' . now()->format('d-m-Y') . '.pdf');
    }

    public function show($id)
    {
        $peminjaman = Peminjaman::with('barang')->findOrFail($id);
        return view('peminjaman.show', compact('peminjaman'));
    }

    public function destroy($id)
    {
        $peminjaman = Peminjaman::findOrFail($id);
        $semuaPeminjaman = Peminjaman::where('kode_pinjam', $peminjaman->kode_pinjam)->get();

        foreach ($semuaPeminjaman as $item) {
            if ($item->status == 'dipinjam') {
                BarangKondisi::where('barang_id', $item->barang_id)
                    ->where('kondisi', 'baik')
                    ->increment('jumlah', $item->jumlah);
            }

            $item->barang->update([
                'jumlah' => BarangKondisi::where('barang_id', $item->barang_id)->sum('jumlah')
            ]);

            $item->delete();
        }

        return redirect()->route('peminjaman.index')->with('success', 'Seluruh data peminjaman dengan kode tersebut berhasil dihapus!');
    }
}
