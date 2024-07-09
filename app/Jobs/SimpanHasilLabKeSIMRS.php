<?php

namespace App\Jobs;

use App\Models\Registrasi;
use App\Models\SIMRS\HasilPeriksaLab;
use App\Models\SIMRS\HasilPeriksaLabDetail;
use App\Models\SIMRS\Jurnal\Jurnal;
use App\Models\SIMRS\MappingTindakan;
use App\Models\SIMRS\PermintaanLabPK;
use App\Models\SIMRS\TindakanLab;
use App\Models\SIMRS\TindakanLabTemplate;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\DB;
use Throwable;

class SimpanHasilLabKeSIMRS implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    /** @var string */
    private $noLaboratoriumLIS;

    /** @var string */
    private $noOrderLabSIMRS;

    /** @var string */
    private $noRawat;

    /** @var "ralan"|"ranap" */
    private $statusRawat;

    /** @var string */
    private $tgl;

    /** @var string */
    private $jam;

    /** @var "Laki-laki"|"Perempuan" */
    private $jenisKelamin;

    /** @var "Dewasa"|"Anak-anak" */
    private $statusUmur;

    private float $totalJasaMedisDokter = 0;

    private float $totalJasaMedisPetugas = 0;

    private float $totalKSO = 0;
    
    private float $totalPendapatan = 0;

    private float $totalBHP = 0;

    private float $totalJasaSarana = 0;

    private float $totalJasaPerujuk = 0;

    private float $totalManajemen = 0;

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
            ->with(['pemeriksaan' => fn (HasMany $query) => $query
                ->orderBy('kategori_pemeriksaan_urut')
                ->orderBy('sub_kategori_pemeriksaan_urut')
                ->orderBy('nomor_urut')])
            ->where('no_laboratorium', $this->noLaboratoriumLIS)
            ->where('no_registrasi', $this->noOrderLabSIMRS)
            ->first();

        $tindakanDariLIS = $registrasi->pemeriksaan->pluck('kode_tindakan_simrs')->unique()->values();

        $waktuPeriksa = carbon_immutable($registrasi->pemeriksaan->first()->waktu_pemeriksaan);
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

        try {
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
                                'nip'                    => '-',
                                'kd_jenis_prw'           => $tindakan->kd_jenis_prw,
                                'tgl_periksa'            => $this->tgl,
                                'jam'                    => $this->jam,
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

                            $this->totalJasaSarana += $tindakan->bagian_rs;
                            $this->totalBHP += $tindakan->bhp;
                            $this->totalJasaPerujuk += $tindakan->tarif_perujuk;
                            $this->totalJasaMedisDokter += $tindakan->tarif_tindakan_dokter;
                            $this->totalJasaMedisPetugas += $tindakan->tarif_tindakan_petugas;
                            $this->totalKSO += $tindakan->kso;
                            $this->totalManajemen += $tindakan->menejemen;
                            $this->totalPendapatan += $tindakan->total_byr;

                            $pemeriksaan = $registrasi->pemeriksaan->where('kode_tindakan_simrs', $tindakan->kd_jenis_prw);

                            TindakanLabTemplate::query()
                                ->where('kd_jenis_prw', $tindakan->kd_jenis_prw)
                                ->whereIn('id_template', $tindakanTersedia->where('kd_jenis_prw', $tindakan->kd_jenis_prw)->pluck('id_template'))
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

                                    $this->totalJasaSarana += $template->bagian_rs;
                                    $this->totalBHP += $template->bhp;
                                    $this->totalJasaPerujuk += $template->bagian_perujuk;
                                    $this->totalJasaMedisDokter += $template->bagian_dokter;
                                    $this->totalJasaMedisPetugas += $template->bagian_laborat;
                                    $this->totalKSO += $template->kso;
                                    $this->totalManajemen += $template->menejemen;
                                    $this->totalPendapatan += $template->biaya_item;
                                });
                        });
                    $this->catatJurnal();
                });
        } catch (Throwable $e) {
            Registrasi::query()
                ->where('no_laboratorium', $this->noLaboratoriumLIS)
                ->delete();

            throw $e;
        }
    }

    private function catatJurnal(): void
    {
        $akunLaborat = null;

        if ($this->statusRawat === 'ranap') {
            $akunLaborat = DB::connection('mysql_sik')
                ->table('set_akun_ranap')
                ->select([
                    'Suspen_Piutang_Laborat_Ranap as suspen_piutang',
                    'Laborat_Ranap as tindakan_laborat',
                    'Beban_Jasa_Medik_Dokter_Laborat_Ranap as beban_jasa_medik_dokter',
                    'Utang_Jasa_Medik_Dokter_Laborat_Ranap as utang_jasa_medik_dokter',
                    'Beban_Jasa_Medik_Petugas_Laborat_Ranap as beban_jasa_medik_petugas',
                    'Utang_Jasa_Medik_Petugas_Laborat_Ranap as utang_jasa_medik_petugas',
                    'Beban_Kso_Laborat_Ranap as beban_kso',
                    'Utang_Kso_Laborat_Ranap as utang_kso',
                    'HPP_Persediaan_Laborat_Rawat_inap as hpp_persediaan',
                    'Persediaan_BHP_Laborat_Rawat_Inap as persediaan_bhp',
                    'Beban_Jasa_Sarana_Laborat_Ranap as beban_jasa_sarana',
                    'Utang_Jasa_Sarana_Laborat_Ranap as utang_jasa_sarana',
                    'Beban_Jasa_Perujuk_Laborat_Ranap as beban_jasa_perujuk',
                    'Utang_Jasa_Perujuk_Laborat_Ranap as utang_jasa_perujuk',
                    'Beban_Jasa_Menejemen_Laborat_Ranap as beban_jasa_manajemen',
                    'Utang_Jasa_Menejemen_Laborat_Ranap as utang_jasa_manajemen',
                ])
                ->first();
        } else {
            $akunLaborat = DB::connection('mysql_sik')
                ->table('set_akun_ralan')
                ->select([
                    'Suspen_Piutang_Laborat_Ralan as suspen_piutang',
                    'Laborat_Ralan as tindakan_laborat',
                    'Beban_Jasa_Medik_Dokter_Laborat_Ralan as beban_jasa_medik_dokter',
                    'Utang_Jasa_Medik_Dokter_Laborat_Ralan as utang_jasa_medik_dokter',
                    'Beban_Jasa_Medik_Petugas_Laborat_Ralan as beban_jasa_medik_petugas',
                    'Utang_Jasa_Medik_Petugas_Laborat_Ralan as utang_jasa_medik_petugas',
                    'Beban_Kso_Laborat_Ralan as beban_kso',
                    'Utang_Kso_Laborat_Ralan as utang_kso',
                    'HPP_Persediaan_Laborat_Rawat_Jalan as hpp_persediaan',
                    'Persediaan_BHP_Laborat_Rawat_Jalan as persediaan_bhp',
                    'Beban_Jasa_Sarana_Laborat_Ralan as beban_jasa_sarana',
                    'Utang_Jasa_Sarana_Laborat_Ralan as utang_jasa_sarana',
                    'Beban_Jasa_Perujuk_Laborat_Ralan as beban_jasa_perujuk',
                    'Utang_Jasa_Perujuk_Laborat_Ralan as utang_jasa_perujuk',
                    'Beban_Jasa_Menejemen_Laborat_Ralan as beban_jasa_manajemen',
                    'Utang_Jasa_Menejemen_Laborat_Ralan as utang_jasa_manajemen',
                ])
                ->first();
        }

        if (! $akunLaborat) {
            return;
        }

        $detailJurnal = collect();

        if ($this->totalPendapatan > 0) {
            $detailJurnal->push(['kd_rek' => $akunLaborat->suspen_piutang, 'debet' => $this->totalPendapatan, 'kredit' => 0]);
            $detailJurnal->push(['kd_rek' => $akunLaborat->tindakan_laborat, 'debet' => 0, 'kredit' => $this->totalPendapatan]);
        }

        if ($this->totalJasaMedisDokter > 0) {
            $detailJurnal->push(['kd_rek' => $akunLaborat->beban_jasa_medis_dokter, 'debet' => $this->totalJasaMedisDokter, 'kredit' => 0]);
            $detailJurnal->push(['kd_rek' => $akunLaborat->utang_jasa_medis_dokter, 'debet' => 0, 'kredit' => $this->totalJasaMedisDokter]);
        }

        if ($this->totalJasaMedisPetugas > 0) {
            $detailJurnal->push(['kd_rek' => $akunLaborat->beban_jasa_medis_petugas, 'debet' => $this->totalJasaMedisPetugas, 'kredit' => 0]);
            $detailJurnal->push(['kd_rek' => $akunLaborat->utang_jasa_medis_petugas, 'debet' => 0, 'kredit' => $this->totalJasaMedisPetugas]);
        }

        if ($this->totalBHP > 0) {
            $detailJurnal->push(['kd_rek' => $akunLaborat->hpp_persediaan, 'debet' => $this->totalBHP, 'kredit' => 0]);
            $detailJurnal->push(['kd_rek' => $akunLaborat->persediaan_bhp, 'debet' => 0, 'kredit' => $this->totalBHP]);
        }

        if ($this->totalKSO > 0) {
            $detailJurnal->push(['kd_rek' => $akunLaborat->beban_kso, 'debet' => $this->totalKSO, 'kredit' => 0]);
            $detailJurnal->push(['kd_rek' => $akunLaborat->utang_kso, 'debet' => 0, 'kredit' => $this->totalKSO]);
        }

        if ($this->totalJasaSarana > 0) {
            $detailJurnal->push(['kd_rek' => $akunLaborat->beban_jasa_sarana, 'debet' => $this->totalJasaSarana, 'kredit' => 0]);
            $detailJurnal->push(['kd_rek' => $akunLaborat->utang_jasa_sarana, 'debet' => 0, 'kredit' => $this->totalJasaSarana]);
        }

        if ($this->totalJasaPerujuk > 0) {
            $detailJurnal->push(['kd_rek' => $akunLaborat->beban_jasa_perujuk, 'debet' => $this->totalJasaPerujuk, 'kredit' => 0]);
            $detailJurnal->push(['kd_rek' => $akunLaborat->utang_jasa_perujuk, 'debet' => 0, 'kredit' => $this->totalJasaPerujuk]);
        }

        if ($this->totalManajemen > 0) {
            $detailJurnal->push(['kd_rek' => $akunLaborat->beban_manajemen, 'debet' => $this->totalManajemen, 'kredit' => 0]);
            $detailJurnal->push(['kd_rek' => $akunLaborat->utang_manajemen, 'debet' => 0, 'kredit' => $this->totalManajemen]);
        }

        Jurnal::catat(
            $this->noRawat,
            sprintf('PEMERIKSAAN LABORAT RAWAT %s, DIPOSTING OLEH %s', str()->upper($this->statusRawat), 'SERVICE LIS'),
            $this->tgl . ' ' . $this->jam,
            $detailJurnal
                ->reject(fn (array $value): bool =>
                    isset($value['kd_rek'], $value['debet'], $value['kredit']) &&
                    (round($value['debet'], 2) === 0.00 && round($value['kredit'], 2) === 0.00)
                )
                ->all()
        );
    }
}
