<?php

namespace App\Models\Bridging;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class SubKategoriPemeriksaan extends Model
{
    use HasFactory;

    protected $table = 'sub_kategori_pemeriksaan';

    protected $fillable = [
        'nama_sub_kategori',
        'nomor_urut',
    ];
}
