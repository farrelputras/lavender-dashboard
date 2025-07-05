<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

use App\Models\Rental;

class Penyewa extends Model
{
    use HasFactory;

    protected $table = 'penyewa';

    protected $fillable = [
        'nama',
        'alamat',
        'no_telp',
        'asal',
        'jenis_kelamin',
        'jaminan1',
        'jaminan2',
    ];

    public function rental()
    {
        return $this->hasMany(Rental::class);
    }
}
