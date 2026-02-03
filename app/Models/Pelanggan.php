<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Pelanggan extends Model
{
    use HasFactory;

    protected $table = 'pelanggan';
    protected $primaryKey = 'pelanggan_id';

    protected $fillable = [
        'pelanggan_nama',
        'pelanggan_alamat',
        'pelanggan_notelp',
        'pelanggan_email',
    ];

    /**
     * Relasi ke pelanggan_data (1 pelanggan punya banyak data/dokumen)
     */
    public function pelangganData()
    {
        return $this->hasMany(PelangganData::class, 'pelanggan_data_pelanggan_id', 'pelanggan_id');
    }

    /**
     * Relasi ke penyewaan (1 pelanggan punya banyak transaksi sewa)
     */
    public function penyewaan()
    {
        return $this->hasMany(Penyewaan::class, 'penyewaan_pelanggan_id', 'pelanggan_id');
    }
}
