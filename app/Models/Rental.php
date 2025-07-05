<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

use App\Models\Penyewa;
use App\Models\Kendaraan;
use App\Models\Pembayaran;

class Rental extends Model
{
    use HasFactory;

    protected $table = 'rental';

    protected $fillable = [
        'penyewa_id',
        'kendaraan_id',
        'tanggal_mulai',
        'tanggal_selesai',
        'total_biaya',
        'status',
        'notes',
    ];

    protected $casts = [
        'tanggal_mulai' => 'datetime',
        'tanggal_selesai' => 'datetime',
    ];

    public function penyewa()
    {
        return $this->belongsTo(Penyewa::class);
    }

    public function kendaraan()
    {
        return $this->belongsTo(Kendaraan::class);
    }

    public function pembayaran()
    {
        return $this->hasOne(Pembayaran::class);
    }
}
