<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Peminjaman extends Model
{
    use HasFactory;

    protected $table = 'peminjamans'; 

    protected $fillable = [
        'kode_pinjam',
        'nama_peminjam',
        'barang_id',
        'jumlah',
        'tgl_pinjam',
        'tgl_kembali',
        'status',
    ];

    public function barang()
    {
        return $this->belongsTo(Barang::class);
    }
}