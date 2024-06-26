<?php

namespace App\Jobs;

use App\Models\Pemeriksaan;
use App\Models\Registrasi;
use App\Models\SIMRS\HasilPeriksaLab;
use App\Models\SIMRS\HasilPeriksaLabDetail;
use App\Models\SIMRS\MappingTindakan;
use App\Models\SIMRS\PermintaanLabPK;
use App\Models\SIMRS\TindakanLab;
use App\Models\SIMRS\TindakanLabTemplate;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class SimpanHasilLabKeSIMRS implements ShouldQueue
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

        $this->noRawat = $permintaanLab->no_rawat;
        $this->statusRawat = $permintaanLab->status;

        $registrasi = Registrasi::query()
            ->with(['pemeriksaan' => fn (HasMany $query) => $query->orderBy('kategori_pemeriksaan_urut')])
            ->where('no_laboratorium', $this->noLaboratoriumLIS)
            ->where('no_registrasi', $this->noOrderLabSIMRS)
            ->first();

        $tindakanDariLIS = $registrasi->pemeriksaan->pluck('kode_tindakan_simrs')->unique()->values();

        $waktuPeriksa = carbon($registrasi->first()->pemeriksaan->first()->waktu_pemeriksaan);
        $this->tgl = $waktuPeriksa->toDateString();
        $this->jam = $waktuPeriksa->format('H:i:s');

        $kodeDokterPJ = DB::connection('mysql_sik')
            ->table('set_pjlab')
            ->value('kd_dokterlab');

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
            ->transaction(function () use ($tindakanDariLIS, $registrasi, $permintaanLab, $kodeDokterPJ, $tindakanTersedia) {
                PermintaanLabPK::query()
                    ->where('noorder', $this->noOrderLabSIMRS)
                    ->update([
                        'tgl_hasil' => $this->tgl,
                        'jam_hasil' => $this->jam,
                    ]);

                TindakanLab::query()
                    ->whereIn('kd_jenis_prw', $tindakanDariLIS)
                    ->get()
                    ->each(function (TindakanLab $tindakan) use ($registrasi, $permintaanLab, $kodeDokterPJ, $tindakanTersedia) {
                        HasilPeriksaLab::create([
                            'no_rawat'               => $this->noRawat,
                            'kd_jenis_prw'           => $tindakan->kd_jenis_prw,
                            'tgl_periksa'            => $this->tgl,
                            'jam'                    => $this->jam,
                            'nip'                    => '-',
                            'dokter_perujuk'         => $permintaanLab->dokter_perujuk,
                            'bagian_rs'              => $tindakan->bagian_rs,
                            'bhp'                    => $tindakan->bhp,
                            'tarif_perujuk'          => $tindakan->tarif_perujuk,
                            'tarif_tindakan_dokter'  => $tindakan->tarif_tindakan_dokter,
                            'tarif_tindakan_petugas' => $tindakan->tarif_tindakan_petugas,
                            'kso'                    => $tindakan->kso,
                            'menejemen'              => $tindakan->menejemen,
                            'biaya'                  => $tindakan->total_byr,
                            'kd_dokter'              => $kodeDokterPJ,
                            'status'                 => $permintaanLab->status,
                            'kategori'               => $tindakan->kategori,
                        ]);

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

                                HasilPeriksaLabDetail::create([
                                    'no_rawat'       => $this->noRawat,
                                    'kd_jenis_prw'   => $template->kd_jenis_prw,
                                    'tgl_periksa'    => $this->tgl,
                                    'jam'            => $this->jam,
                                    'id_template'    => $template->id_template,
                                    'nilai'          => $detailPemeriksaan->hasil_nilai_hasil ?? '',
                                    'nilai_rujukan'  => $detailPemeriksaan->hasil_nilai_rujukan ?? '',
                                    'keterangan'     => $detailPemeriksaan->hasil_flag_kode ?? '',
                                    'bagian_rs'      => $template->bagian_rs,
                                    'bhp'            => $template->bhp,
                                    'bagian_perujuk' => $template->bagian_perujuk,
                                    'bagian_dokter'  => $template->bagian_dokter,
                                    'bagian_laborat' => $template->bagian_laborat,
                                    'kso'            => $template->kso,
                                    'menejemen'      => $template->menejemen,
                                    'biaya_item'     => $template->biaya_item,
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
