<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class BarangKondisi extends Model
{
    protected $fillable = ['barang_id', 'kondisi', 'jumlah'];

    public function barang()
    {
        return $this->belongsTo(Barang::class);
    }
}
