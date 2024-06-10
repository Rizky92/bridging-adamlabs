<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class SimpanHasilLab extends Model
{
    protected $connection = 'mysql_bridging';
    
    protected $table = 'hasil_pemeriksaan_lab';

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
        'nama_pasien',
        'no_rm',
        'jenis_kelamin',
        'alamat',
        'no_telphone',
        'tanggal_lahir',
        'nik',
        'ras',
        'berat_badan',
        'jenis_registrasi',
        'kode_dokter_pengirim',
        'nama_dokter_pengirim',
        'kode_unit_asal',
        'nama_unit_asal',
        'kode_penjamin',
        'nama_penjamin',
    ];

    public function detail(): HasMany
    {
        return $this->hasMany(SimpanHasilLabDetail::class, 'no_laboratorium', 'no_laboratorium');
    }
}
