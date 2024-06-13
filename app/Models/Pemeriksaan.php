<?php

namespace App\Models;

use App\Models\SIMRS\MappingTindakan;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Reedware\LaravelCompositeRelations\CompositeBelongsTo;
use Reedware\LaravelCompositeRelations\HasCompositeRelations;

class Pemeriksaan extends Model
{
    use HasCompositeRelations;

    protected $connection = 'mysql';

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
        'sub_kategori_pemeriksaan_nama',
        'sub_kategori_pemeriksaan_urut',
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

    protected $casts = [
        'status_bridging' => 'boolean',
    ];

    public function registrasi(): BelongsTo
    {
        return $this->belongsTo(Registrasi::class, 'no_laboratorium', 'no_laboratorium');
    }

    public function mappingTindakan(): CompositeBelongsTo
    {
        return $this->compositeBelongsTo(
            MappingTindakan::class,
            ['kode_tindakan_simrs', 'nama_pemeriksaan_lis'],
            ['kd_jenis_prw', 'pemeriksaan']
        );
    }
}
