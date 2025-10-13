<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Barang extends Model
{
    protected $guarded = ['id'];

    protected $casts = [
        'tanggal_pengadaan' => 'date',
    ];

    public function kategori(): BelongsTo
    {
        return $this->belongsTO(Kategori::class, 'kategori_id');
    }
    
    public function lokasi(): BelongsTo
    {
        return $this->belongsTO(Lokasi::class, 'lokasi_id');
    }
    
    public function kondisis()
    {
        return $this->hasMany(BarangKondisi::class);
    }
}
