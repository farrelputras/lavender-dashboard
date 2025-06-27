<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

use App\Models\Kendaraan;

class Servis extends Model
{
    use HasFactory;

    protected $table = 'servis';

    public function kendaraan()
    {
        return $this->belongsTo(Kendaraan::class);
    }
}
