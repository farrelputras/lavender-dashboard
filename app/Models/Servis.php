<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

use App\Models\Kendaraan;

class Servis extends Model
{
    use HasFactory;

    protected $table = 'servis';

    protected $fillable = [
        'kendaraan_id',
        'deskripsi',
        'kilometer_servis',
        'tipe_servis',
        'bengkel',
        'tanggal_servis',
        'biaya',
        'keterangan',
    ];

    protected $casts = [
        'tanggal_servis' => 'date',
    ];

    public function kendaraan(): BelongsTo
    {
        return $this->belongsTo(Kendaraan::class);
    }
}
