<?php

namespace App\Models\SIMRS;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Reedware\LaravelCompositeRelations\CompositeHasOne;
use Reedware\LaravelCompositeRelations\HasCompositeRelations;

class PeriksaLab extends Model
{
    use HasCompositeRelations;

    protected $connection = 'mysql_sik';

    protected $table = 'periksa_lab';

    protected $primaryKey = false;

    protected $keyType = false;

    public $incrementing = false;

    public $timestamps = false;

    protected $fillable = [
        'no_rawat',
        'nip',
        'kd_jenis_prw',
        'tgl_periksa',
        'jam',
        'dokter_perujuk',
        'bagian_rs',
        'bhp',
        'tarif_perujuk',
        'tarif_tindakan_dokter',
        'tarif_tindakan_petugas',
        'kso',
        'menejemen',
        'biaya',
        'kd_dokter',
        'status',
        'kategori',
    ];

    /**
     * @psalm-return Builder<Model>
     */
    public function permintaan(): Builder
    {
        return $this
            ->compositeBelongsTo(
                PermintaanLabPK::class,
                ['no_rawat', 'tgl_hasil', 'jam_hasil'],
                ['no_rawat', 'tgl_periksa', 'jam']
            )
            ->where('kategori', 'PK');
    }

    public function catatan(): CompositeHasOne
    {
        return $this->compositeHasOne(
            KesanSaran::class,
            ['no_rawat', 'tgl_periksa', 'jam'],
            ['no_rawat', 'tgl_periksa', 'jam']
        );
    }

    /**
     * @psalm-return Builder<Model>
     */
    public function detail(): Builder
    {
        return $this
            ->compositeHasMany(
                PeriksaLabDetail::class,
                ['no_rawat', 'tgl_periksa', 'jam', 'kd_jenis_prw'],
                ['no_rawat', 'tgl_periksa', 'jam', 'kd_jenis_prw'],
            )
            ->where('kategori', 'PK');
    }
}
