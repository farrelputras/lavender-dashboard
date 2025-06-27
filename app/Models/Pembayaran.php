<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

use App\Models\Rental;

class Pembayaran extends Model
{
    use HasFactory;

    protected $table = 'pembayaran';

    public function rental()
    {
        return $this->belongsTo(Rental::class);
    }
}
