<?php

namespace App\Models\Bridging;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Pemeriksaan extends Model
{
    use HasFactory;

    protected $table = 'pemeriksaan';

    protected $fillable = [
        'nomor_urut',
        'kode_tindakan_simrs',
        'kode_pemeriksaan_lis',
        'nama_pemeriksaan_lis',
        'metode',
        'waktu_pemeriksaan',
        'status_bridging',
        'registrasi_id',
        'kategori_pemeriksaan_id',
        'sub_kategori_pemeriksaan_id',
        'hasil_id',
    ];

    public function registrasi()
    {
        return $this->belongsTo(Registrasi::class);
    }

    public function kategoriPemeriksaan()
    {
        return $this->belongsTo(KategoriPemeriksaan::class);
    }

    public function subKategoriPemeriksaan()
    {
        return $this->belongsTo(SubKategoriPemeriksaan::class);
    }

    public function hasil()
    {
        return $this->belongsTo(Hasil::class);
    }
}
