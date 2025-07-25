<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\HasMany;

use App\Models\Rental;
use App\Models\Servis;

use App\Enums\StatusKendaraan;

class Kendaraan extends Model
{
    use HasFactory;

    protected $table = 'kendaraan';

    protected $fillable = [
        'nopol',
        'jenis',
        'model',
        'tahun',
        'tgl_pajak',
        'stnk_nama',
        'no_gps',
        'imei',
        'kilometer',
        'gambar',
        'harga_6jam',
        'harga_12jam',
        'harga_24jam',
        'bbm',
        'bbm_per_kotak',
        'status',
        'notes',
    ];

    protected $casts = [
        'status' => StatusKendaraan::class, // Eloquent Enum Casting
    ];

    public function rental(): HasMany
    {
        return $this->hasMany(Rental::class);
    }

    public function servis(): HasMany
    {
        return $this->hasMany(Servis::class);
    }
}
