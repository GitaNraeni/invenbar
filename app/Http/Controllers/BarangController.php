<?php

namespace App\Http\Controllers;

use App\Models\Barang;
use App\Models\Kategori;
use App\Models\Lokasi;
use Illuminate\Http\Request;
use Illuminate\Routing\Controllers\HasMiddleware;
use Illuminate\Routing\Controllers\Middleware;
use Illuminate\Support\Facades\Storage;
use Barryvdh\DomPDF\Facade\Pdf;

class BarangController extends Controller implements HasMiddleware
{
    public static function middleware()
    {
        return [
            new Middleware('permission:manage barang', except: ['destroy']),
            new Middleware('permission:delete barang', only: ['destroy']),
        ];
    }

    public function index(Request $request)
    {
        $search = $request->search;

        $barangs = Barang::with('kategori', 'lokasi')
            ->when($search, function ($query, $search) {
                $query->where('nama_barang', 'like', '%' . $search . '%')
                    ->orWhere('kode_barang', 'like', '%' . $search . '%');
            })
            ->latest()->paginate()->withQueryString();

        return view('barang.index', compact('barangs'));
    }

    public function create()
    {
        $kategori = Kategori::all();
        $lokasi = Lokasi::all();
        $barang = new Barang();

        return view('barang.create', compact('barang', 'kategori', 'lokasi'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'kode_barang' => 'required|string|max:50|unique:barangs,kode_barang',
            'nama_barang' => 'required|string|max:150',
            'kategori_id' => 'required|exists:kategoris,id',
            'lokasi_id' => 'required|exists:lokasis,id',
            'jumlah' => 'required|integer|min:0',
            'satuan' => 'required|string|max:20',
            'tanggal_pengadaan' => 'required|date',
            'gambar' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
            'kondisi' => 'array',
            'kondisi.*' => 'nullable|integer|min:0',
            'sumber_dana' => 'nullable|string|max:100',
            'sumber_dana_lainnya' => 'nullable|string|max:100',
        ]);

        // Handle sumber dana "Lainnya"
        if ($request->sumber_dana === 'Lainnya' && $request->filled('sumber_dana_lainnya')) {
            $validated['sumber_dana'] = $request->sumber_dana_lainnya;
        }

        if ($request->hasFile('gambar')) {
            $file = $request->file('gambar');
            $namaFile = time() . '_' . $file->getClientOriginalName();
            $file->move(public_path('gambar-barang'), $namaFile);
            $validated['gambar'] = $namaFile;
        }

        $kondisiData = $validated['kondisi'] ?? [];
        unset($validated['kondisi']);

        $barang = Barang::create($validated);

        if ($request->has('kondisi')) {
            foreach ($request->kondisi as $kondisi => $jumlah) {
                if ($jumlah > 0) {
                    $barang->kondisis()->create([
                        'kondisi' => $kondisi,
                        'jumlah' => $jumlah,
                    ]);
                }
            }
        }

        return redirect()->route('barang.index')
            ->with('success', 'Data barang berhasil ditambahkan.');
    }

    public function show(Barang $barang)
    {
        $barang->load(['kategori', 'lokasi']);
        return view('barang.show', compact('barang'));
    }

    public function edit(Barang $barang)
    {
        $kategori = Kategori::all();
        $lokasi = Lokasi::all();

        return view('barang.edit', compact('barang', 'kategori', 'lokasi'));
    }

    public function update(Request $request, Barang $barang)
    {
        $validated = $request->validate([
            'kode_barang' => 'required|string|max:50|unique:barangs,kode_barang,' . $barang->id,
            'nama_barang' => 'required|string|max:150',
            'kategori_id' => 'required|exists:kategoris,id',
            'lokasi_id' => 'required|exists:lokasis,id',
            'jumlah' => 'required|integer|min:0',
            'satuan' => 'required|string|max:20',
            'tanggal_pengadaan' => 'required|date',
            'gambar' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
            'kondisi' => 'array',
            'kondisi.*' => 'nullable|integer|min:0',
            'sumber_dana' => 'nullable|string|max:100',
            'sumber_dana_lainnya' => 'nullable|string|max:100',
        ]);

        // Handle sumber dana "Lainnya"
        if ($request->sumber_dana === 'Lainnya' && $request->filled('sumber_dana_lainnya')) {
            $validated['sumber_dana'] = $request->sumber_dana_lainnya;
        }

        if ($request->hasFile('gambar')) {
            if ($barang->gambar && file_exists(public_path('gambar-barang/' . $barang->gambar))) {
                unlink(public_path('gambar-barang/' . $barang->gambar));
            }

            $file = $request->file('gambar');
            $namaFile = time() . '_' . $file->getClientOriginalName();
            $file->move(public_path('gambar-barang'), $namaFile);
            $validated['gambar'] = $namaFile;
        }

        $kondisiData = $validated['kondisi'] ?? [];
        unset($validated['kondisi']);

        $barang->update($validated);

        $barang->kondisis()->delete();
        foreach ($kondisiData as $kondisi => $jumlah) {
            if ($jumlah > 0) {
                $barang->kondisis()->create([
                    'kondisi' => $kondisi,
                    'jumlah' => $jumlah,
                ]);
            }
        }

        return redirect()->route('barang.index')->with('success', 'Data barang berhasil diperbarui.');
    }

    public function destroy(Barang $barang)
    {
        if ($barang->gambar && file_exists(public_path('gambar-barang/' . $barang->gambar))) {
            unlink(public_path('gambar-barang/' . $barang->gambar));
        }

        $barang->delete();

        return redirect()->route('barang.index')->with('success', 'Data barang berhasil dihapus.');
    }

    public function cetakLaporan()
    {
        $barangs = Barang::with(['kategori', 'lokasi', 'kondisis'])->get();

        $data = [
            'title' => 'Laporan Data Barang Inventaris',
            'date' => date('d F Y'),
            'barangs' => $barangs
        ];

        $pdf = Pdf::loadView('barang.laporan', $data);
        return $pdf->stream('laporan-inventaris-barang.pdf');
    }
}
