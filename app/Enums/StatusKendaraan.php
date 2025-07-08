<?php

namespace App\Enums;

enum StatusKendaraan: string
{
    case TERSEDIA = 'TERSEDIA';
    case DISEWA = 'DISEWA';
    case PERBAIKAN = 'PERBAIKAN';
}
