<?php

namespace App\Jobs;

use App\Models\Registrasi;
use App\Models\SIMRS\HasilPeriksaLab;
use App\Models\SIMRS\HasilPeriksaLabDetail;
use App\Models\SIMRS\MappingTindakan;
use App\Models\SIMRS\PermintaanLabPK;
use App\Models\SIMRS\TindakanLab;
use App\Models\SIMRS\TindakanLabTemplate;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldBeUnique;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\DB;

class UpdateHasilLabKeSIMRS implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    /** @var string */
    private $noLaboratoriumLIS;

    /** @var string */
    private $noOrderLabSIMRS;

    /** @var string */
    private $noRawat;

    /** @var string */
    private $statusRawat;

    /** @var string */
    private $tgl;

    /** @var string */
    private $jam;

    /** @var string */
    private $jenisKelamin;

    /** @var string */
    private $statusUmur;

    /**
     * Create a new job instance.
     * 
     * @param  array{
     *     no_laboratorium: string,
     *     no_registrasi: string
     * }  $options
     */
    public function __construct(array $options)
    {
        $this->noLaboratoriumLIS = $options['no_laboratorium'];
        $this->noOrderLabSIMRS = $options['no_registrasi'];
    }

    /**
     * Execute the job.
     */
    public function handle()
    {
        $this->simpanHasilLab();
    }

    private function simpanHasilLab(): void
    {
        $permintaanLab = PermintaanLabPK::query()
            ->where('noorder', $this->noOrderLabSIMRS)
            ->first();

        $this->tgl = $permintaanLab->tgl_hasil;
        $this->jam = $permintaanLab->jam_hasil;

        $this->noRawat = $permintaanLab->no_rawat;
        $this->statusRawat = $permintaanLab->status;

        $registrasi = Registrasi::query()
            ->with(['pemeriksaan' => fn (HasMany $query) => $query->orderBy('kategori_pemeriksaan_urut')])
            ->where('no_laboratorium', $this->noLaboratoriumLIS)
            ->where('no_registrasi', $this->noOrderLabSIMRS)
            ->first();

        $tindakanDariLIS = $registrasi->pemeriksaan->pluck('kode_tindakan_simrs')->unique()->values();

        $this->jenisKelamin = $registrasi->pasien_jenis_kelamin === 'L'
            ? 'Laki-laki' : 'Perempuan';

        $this->statusUmur = $registrasi->umur_tahun >= 18
            ? 'Dewasa' : 'Anak-anak';

        $tindakanTersedia = MappingTindakan::query()
            ->whereIn('kd_jenis_prw', $tindakanDariLIS)
            ->where('jenis_kelamin', $this->jenisKelamin)
            ->where('status_umur', $this->statusUmur)
            ->whereIn('kode_pemeriksaan_lis', $registrasi->pemeriksaan->pluck('kode_pemeriksaan_lis'))
            ->groupBy(['kd_jenis_prw', 'pemeriksaan'])
            ->orderBy('kd_jenis_prw')
            ->orderBy('urutan')
            ->get();

        DB::connection('mysql_sik')
            ->transaction(function () use ($tindakanDariLIS, $registrasi, $tindakanTersedia) {
                TindakanLab::query()
                    ->whereIn('kd_jenis_prw', $tindakanDariLIS)
                    ->get()
                    ->each(function (TindakanLab $tindakan) use ($registrasi, $tindakanTersedia) {
                        $pemeriksaan = $registrasi->pemeriksaan->where('kode_tindakan_simrs', $tindakan->kd_jenis_prw);

                        TindakanLabTemplate::query()
                            ->where('kd_jenis_prw', $tindakan->kd_jenis_prw)
                            ->whereIn('id_template', $tindakanTersedia->where('kd_jenis_prw', $tindakan->kd_jenis_prw)->pluck('id_template'))
                            ->orderBy('urut')
                            ->get()
                            ->each(function (TindakanLabTemplate $template) use ($pemeriksaan, $tindakanTersedia) {
                                $detailPemeriksaan = $pemeriksaan->where(
                                    'kode_pemeriksaan_lis',
                                    $tindakanTersedia->where('id_template', $template->id_template)->first()->kode_pemeriksaan_lis
                                )->first();

                                HasilPeriksaLabDetail::query()
                                    ->where('no_rawat', $this->noRawat)
                                    ->where('kd_jenis_prw', $template->kd_jenis_prw)
                                    ->where('tgl_periksa', $this->tgl)
                                    ->where('jam', $this->jam)
                                    ->where('id_template', $template->id_template)
                                    ->update([
                                        'nilai'      => $detailPemeriksaan->hasil_nilai_hasil ?? '',
                                        'keterangan' => $detailPemeriksaan->hasil_flag_kode ?? '',
                                    ]);
                            });
                    });
            });
    }

    private function updatePermintaan(): void
    {
        
    }

    private function catatJurnal(): bool
    {
        return true;
    }
}
