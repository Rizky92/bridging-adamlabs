<?php

namespace App\Models;

use App\Models\SIMRS\HasilPeriksaLabDetail;
use App\Models\SIMRS\MappingTindakan;
use Exception;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Query\JoinClause;
use Illuminate\Support\Facades\DB;
use Reedware\LaravelCompositeRelations\CompositeBelongsTo;
use Reedware\LaravelCompositeRelations\HasCompositeRelations;

class Pemeriksaan extends Model
{
    use HasCompositeRelations;

    protected $connection = 'mysql';

    protected $table = 'pemeriksaan';

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
        'hasil_nilai_hasil',
        'hasil_nilai_rujukan',
        'hasil_flag_kode',
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

    public static function isiHasilPeriksaLabDetail(
        string $noLaboratorium,
        string $kodeTindakan,
        string $noRawat,
        string $tglHasil,
        string $jam,
        string $jk,
        string $statusUmur
    ): void {
        if (
            empty($noLaboratorium) ||
            empty($kodeTindakan) ||
            empty($noRawat) ||
            empty($tglHasil) ||
            empty($jam) ||
            empty($jk) ||
            empty($statusUmur)
        ) {
            throw new Exception('All parameters are required!');
        }

        $sqlSelect = <<<SQL
            ? as no_rawat,
            pemeriksaan.kode_tindakan_simrs as kd_jenis_prw,
            ? as tgl_periksa,
            ? as jam,
            mapping_adamlabs.id_template,
            pemeriksaan.hasil_nilai_hasil as nilai,
            pemeriksaan.hasil_nilai_rujukan,
            pemeriksaan.hasil_flag_kode as keterangan,
            template_laboratorium.bagian_rs,
            template_laboratorium.bhp,
            template_laboratorium.bagian_perujuk,
            template_laboratorium.bagian_dokter,
            template_laboratorium.bagian_laborat,
            template_laboratorium.kso,
            template_laboratorium.menejemen,
            template_laboratorium.biaya_item
            SQL;

        $sik = DB::connection('mysql_sik')->getDatabaseName();

        static::query()
            ->selectRaw($sqlSelect, [$noRawat, $tglHasil, $jam])
            ->join('registrasi', fn (JoinClause $join) => $join
                ->on('pemeriksaan.no_laboratorium', '=', 'registrasi.no_laboratorium')
                ->on('pemeriksaan.no_registrasi', '=', 'registrasi.no_registrasi'))
            ->join(DB::raw("$sik.mapping_adamlabs mapping_adamlabs"), fn (JoinClause $join) => $join
                ->on('pemeriksaan.kode_tindakan_simrs', '=', 'mapping_adamlabs.kd_jenis_prw')
                ->on('pemeriksaan.nama_pemeriksaan_lis', '=', 'mapping_adamlabs.pemeriksaan')
                ->on('registrasi.pasien_jenis_kelamin', '=', DB::raw("'$jk'"))
                ->on('mapping_adamlabs.status_umur', '=', DB::raw("'$statusUmur'")))
            ->join(DB::raw("$sik.template_laboratorium template_laboratorium"), fn (JoinClause $join) => $join
                ->on('mapping_adamlabs.id_template', '=', 'template_laboratorium.id_template')
                ->on('mapping_adamlabs.kd_jenis_prw', '=', 'template_laboratorium.kd_jenis_prw'))
            ->where('pemeriksaan.no_laboratorium', $noLaboratorium)
            ->where('pemeriksaan.kode_tindakan_simrs', $kodeTindakan)
            ->groupBy('pemeriksaan.id')
            ->get()
            ->each(function (Pemeriksaan $hasil) {
                HasilPeriksaLabDetail::create([
                    'no_rawat'       => $hasil->no_rawat,
                    'kd_jenis_prw'   => $hasil->kd_jenis_prw,
                    'tgl_periksa'    => $hasil->tgl_periksa,
                    'jam'            => $hasil->jam,
                    'id_template'    => $hasil->id_template,
                    'nilai'          => $hasil->nilai ?? '',
                    'nilai_rujukan'  => $hasil->nilai_rujukan ?? '',
                    'keterangan'     => $hasil->keterangan ?? '',
                    'bagian_rs'      => $hasil->bagian_rs ?? 0,
                    'bhp'            => $hasil->bhp ?? 0,
                    'bagian_perujuk' => $hasil->bagian_perujuk ?? 0,
                    'bagian_dokter'  => $hasil->bagian_dokter ?? 0,
                    'bagian_laborat' => $hasil->bagian_laborat ?? 0,
                    'kso'            => $hasil->kso ?? 0,
                    'menejemen'      => $hasil->menejemen ?? 0,
                    'biaya_item'     => $hasil->biaya_item ?? 0,
                ]);
            });
    }
}
