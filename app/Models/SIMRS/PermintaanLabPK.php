<?php

namespace App\Models\SIMRS;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Reedware\LaravelCompositeRelations\HasCompositeRelations;

class PermintaanLabPK extends Model
{
    use HasCompositeRelations;

    protected $connection = 'mysql_sik';

    protected $table = 'permintaan_lab';

    protected $primaryKey = 'noorder';

    protected $keyType = 'string';

    public $incrementing = false;

    public $timestamps = false;

    /**
     * @psalm-return Builder<Model>
     */
    public function hasil(): Builder
    {
        return $this
            ->compositeHasMany(
                PeriksaLab::class,
                ['no_rawat', 'tgl_periksa', 'jam'],
                ['no_rawat', 'tgl_hasil', 'jam_hasil'],
            )
            ->where('kategori', 'PK');
    }
}
