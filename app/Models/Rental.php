<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

use App\Models\Penyewa;
use App\Models\Kendaraan;
use App\Models\Transaksi;

use App\Enums\StatusBayar;
use App\Enums\StatusRental;

class Rental extends Model
{
    use HasFactory;

    protected $table = 'rental';

    protected $fillable = [
        'penyewa_id',
        'kendaraan_id',
        'tanggal_mulai',
        'tanggal_selesai',
        'bbm_awal',
        'bbm_kembali',
        'biaya_dibayar',
        'total_biaya',
        'status_rental',
        'status_bayar',
        'notes',
    ];

    protected $casts = [
        'tanggal_mulai' => 'datetime',
        'tanggal_selesai' => 'datetime',
        'status_rental' => StatusRental::class, // Enum Casting
        'status_bayar' => StatusBayar::class,   // Enum Casting
    ];

    public function penyewa(): BelongsTo
    {
        return $this->belongsTo(Penyewa::class);
    }

    public function kendaraan(): BelongsTo
    {
        return $this->belongsTo(Kendaraan::class);
    }

    public function transaksi(): HasMany
    {
        return $this->hasMany(Transaksi::class);
    }
}
