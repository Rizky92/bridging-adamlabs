<?php

namespace App\Models\Bridging;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Penjamin extends Model
{
    use HasFactory;

    protected $table = 'penjamin';

    protected $fillable = [
        'kode',
        'nama',
    ];
}
