<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasOne;

class SimpanHasilLabDetail extends Model
{
    protected $table = 'adamlabs_hasil_pemeriksaan';

    protected $primaryKey = 'id';

    protected $keyType = 'int';

    public $incrementing = true;

    public $timestamps = false;

    protected $fillable = [
        'no_laboratorium',
        'no_registrasi',
        'kategori_pemeriksaan_nama',
        'kategori_pemeriksaan_urut',
        'subkategori_pemeriksaan_nama',
        'subkategori_pemeriksaan_urut',
        'nomor_urut',
        'kode_tindakan_simrs',
        'kode_pemeriksaan_lis',
        'nama_pemeriksaan_lis',
        'metode',
        'waktu_pemeriksaan',
        'status_bridging',
        'hasil_satuan',
        'nilai_hasil',
        'nilai_rujukan',
        'flag_kode',
    ];

    public function master(): BelongsTo
    {
        return $this->belongsTo(SimpanHasilLab::class, 'no_laboratorium', 'no_laboratorium');
    }

    public function tindakan(): HasOne
    {
        return $this->hasOne(TindakanLab::class, 'kd_jenis_prw', 'kode_tindakan_simrs');
    }
}
