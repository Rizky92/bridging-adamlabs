<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasOne;

class SimpanHasilLabDetail extends Model
{
    protected $connection = 'mysql_bridging';
    
    protected $table = 'hasil_pemeriksaan_lab_detail';

    protected $primaryKey = 'id';

    protected $keyType = 'int';

    public $incrementing = true;

    public $timestamps = false;

    protected $fillable = [
        'no_registrasi',
        'nama_kategori_pemeriksaan',
        'urut_kategori_pemeriksaan',
        'nama_subkategori_pemeriksaan',
        'urut_subkategori_pemeriksaan',
        'urut',
        'kode_tindakan_simrs',
        'kode_pemeriksaan_lis',
        'nama_pemeriksaan_lis',
        'metode',
        'waktu_pemeriksaan',
        'status_bridging',
        'hasil_satuan',
        'hasil_nilai_hasil',
        'hasil_nilai_rujukan',
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
