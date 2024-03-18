<?php

namespace App\Models\Bridging;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;

class Registrasi extends Model
{
    use HasFactory;

    protected $table = 'registrasi';

    protected $fillable = [
        'no_registrasi',
        'no_laboratorium',
        'waktu_registrasi',
        'diagnosa_awal',
        'kode_RS',
        'kode_lab',
        'umur_tahun',
        'umur_bulan',
        'umur_hari',
        'pasien_id',
        'dokter_pengirim_id',
        'unit_asal_id',
        'penjamin_id',
    ];

    public function pasien()
    {
        return $this->hasOne(Pasien::class);
    }

    public function dokterPengirim()
    {
        return $this->belongsTo(DokterPengirim::class);
    }

    public function unitAsal()
    {
        return $this->belongsTo(UnitAsal::class);
    }

    public function penjamin()
    {
        return $this->belongsTo(Penjamin::class);
    }

    public function pemeriksaan()
    {
        return $this->hasMany(Pemeriksaan::class);
    }
}
