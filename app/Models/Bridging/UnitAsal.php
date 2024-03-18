<?php

namespace App\Models\Bridging;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class UnitAsal extends Model
{
    use HasFactory;

    protected $table = 'unit_asal';

    protected $fillable = [
        'kode',
        'nama',
    ];
}
