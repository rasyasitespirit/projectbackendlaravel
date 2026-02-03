<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Alat extends Model
{
    use HasFactory;

    protected $table = 'alat';
    protected $primaryKey = 'alat_id';

    protected $fillable = [
        'alat_kategori_id',
        'alat_nama',
        'alat_deskripsi',
        'alat_hargaperhari',
        'alat_stok',
    ];

    /**
     * Relasi ke kategori (1 alat milik 1 kategori)
     */
    public function kategori()
    {
        return $this->belongsTo(Kategori::class, 'alat_kategori_id', 'kategori_id');
    }

    /**
     * Relasi ke penyewaan_detail (1 alat bisa ada di banyak detail penyewaan)
     */
    public function penyewaanDetail()
    {
        return $this->hasMany(PenyewaanDetail::class, 'penyewaan_detail_alat_id', 'alat_id');
    }
}
