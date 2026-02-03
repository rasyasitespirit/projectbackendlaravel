<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Penyewaan extends Model
{
    use HasFactory;

    protected $table = 'penyewaan';
    protected $primaryKey = 'penyewaan_id';

    protected $fillable = [
        'penyewaan_pelanggan_id',
        'penyewaan_tglsewa',
        'penyewaan_tglkembali',
        'penyewaan_sttspembayaran',
        'penyewaan_sttskembali',
        'penyewaan_totalharga',
    ];

    protected $casts = [
        'penyewaan_tglsewa' => 'date',
        'penyewaan_tglkembali' => 'date',
    ];

    /**
     * Relasi ke pelanggan (1 penyewaan milik 1 pelanggan)
     */
    public function pelanggan()
    {
        return $this->belongsTo(Pelanggan::class, 'penyewaan_pelanggan_id', 'pelanggan_id');
    }

    /**
     * Relasi ke penyewaan_detail (1 penyewaan punya banyak detail)
     */
    public function penyewaanDetail()
    {
        return $this->hasMany(PenyewaanDetail::class, 'penyewaan_detail_penyewaan_id', 'penyewaan_id');
    }
}
