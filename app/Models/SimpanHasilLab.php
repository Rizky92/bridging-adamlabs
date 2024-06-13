<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class SimpanHasilLab extends Model
{
    protected $table = 'adamlabs_registrasi';

    protected $primaryKey = 'no_laboratorium';

    protected $keyType = 'string';

    public $incrementing = false;

    public $timestamps = false;

    protected $fillable = [
        'no_registrasi',
        'no_laboratorium',
        'waktu_registrasi',
        'diagnosa_awal',
        'kode_rs',
        'kode_lab',
        'umur_tahun',
        'umur_bulan',
        'umur_hari',
        'pasien_no_rm',
        'pasien_nama_pasien',
        'pasien_jenis_kelamin',
        'pasien_tanggal_lahir',
        'pasien_alamat',
        'pasien_nik',
        'pasien_no_telphone',
        'pasien_ras',
        'pasien_berat_badan',
        'pasien_jenis_registrasi',
        'dokter_pengirim_kode',
        'dokter_pengirim_nama',
        'unit_asal_kode',
        'unit_asal_nama',
        'penjamin_kode',
        'penjamin_nama',
    ];

    public function detail(): HasMany
    {
        return $this->hasMany(SimpanHasilLabDetail::class, 'no_laboratorium', 'no_laboratorium');
    }

    public function permintaanLabSIMRS(): BelongsTo
    {
        return $this->belongsTo(PermintaanLabPK::class, 'noorder', 'no_registrasi');
    }
}
