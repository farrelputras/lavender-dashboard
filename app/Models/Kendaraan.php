<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

use App\Models\Rental;
use App\Models\Servis;

class Kendaraan extends Model
{
    use HasFactory;

    protected $table = 'kendaraan';

    protected $fillable = [
        'nopol',
        'jenis',
        'merk',
        'warna',
        'tahun',
        'harga_sewa',
        'status',
    ];

    public function rental()
    {
        return $this->hasMany(Rental::class);
    }

    public function servis()
    {
        return $this->hasMany(Servis::class);
    }
}
