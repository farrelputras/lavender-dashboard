<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\HasMany;

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
        'foto_jaminan1',
        'jaminan2',
        'foto_jaminan2',
        'notes',
    ];

    public function rental(): HasMany
    {
        return $this->hasMany(Rental::class);
    }
}
