<?php

namespace App\Models\Bridging;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class KategoriPemeriksaan extends Model
{
    use HasFactory;

    protected $table = 'kategori_pemeriksaan';

    protected $fillable = [
        'nama_kategori',
        'nomor_urut',
    ];

    public function pemeriksaan()
    {
        return $this->hasMany(Pemeriksaan::class);
    }
}
