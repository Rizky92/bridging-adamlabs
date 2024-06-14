<?php

namespace App\Jobs;

use App\Models\Pemeriksaan;
use App\Models\Registrasi;
use App\Models\SIMRS\HasilPeriksaLab;
use App\Models\SIMRS\HasilPeriksaLabDetail;
use App\Models\SIMRS\PermintaanLabPK;
use App\Models\SIMRS\TindakanLab;
use App\Models\SIMRS\TindakanLabTemplate;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\DB;

class SimpanHasilLabKeSIMRS implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    private $noLaboratoriumLIS;

    private $noOrderLabSIMRS;

    private $noRawat;

    private $tglHasil;

    private $jam;

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

        $hasilPemeriksaanLab = Registrasi::query()
            ->with('pemeriksaan')
            ->where('no_laboratorium', $this->noLaboratoriumLIS)
            ->where('no_registrasi', $this->noOrderLabSIMRS)
            ->first();

        $dataTindakan = $hasilPemeriksaanLab->pemeriksaan->pluck('kode_tindakan_simrs');

        $waktuPeriksa = carbon($hasilPemeriksaanLab->first()->pemeriksaan->first()->waktu_pemeriksaan);

        $this->tglHasil = $waktuPeriksa->toDateString();

        $this->jam = $waktuPeriksa->format('H:i:s');

        $expertise = DB::connection('mysql_sik')
            ->table('set_pjlab')
            ->value('kd_dokterlab');

        $statusUmur = ($hasilPemeriksaanLab->umur_tahun >= 18) ? 'Dewasa' : 'Anak-anak';

        DB::connection('mysql_sik')
            ->transaction(function () use (
                $dataTindakan, $permintaanLab, $hasilPemeriksaanLab, $expertise, $statusUmur
            ) {
                PermintaanLabPK::query()
                    ->where('noorder', $this->noOrderLabSIMRS)
                    ->update([
                        'tgl_hasil' => $this->tglHasil,
                        'jam_hasil' => $this->jam,
                    ]);

                TindakanLab::query()
                    ->whereIn('kd_jenis_prw', $dataTindakan)
                    ->get()
                    ->each(function (TindakanLab $tindakan) use ($permintaanLab, $hasilPemeriksaanLab, $expertise, $statusUmur) {
                        HasilPeriksaLab::create([
                            'no_rawat'               => $permintaanLab->no_rawat,
                            'nip'                    => '-',
                            'kd_jenis_prw'           => $tindakan->kd_jenis_prw,
                            'tgl_periksa'            => $this->tglHasil,
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
                            'kd_dokter'              => $expertise,
                            'status'                 => $permintaanLab->status,
                            'kategori'               => $tindakan->kategori,
                        ]);

                        Pemeriksaan::isiHasilPeriksaLabDetail(
                            $hasilPemeriksaanLab->no_laboratorium,
                            $tindakan->kd_jenis_prw,
                            $permintaanLab->no_rawat,
                            $this->tglHasil,
                            $this->jam,
                            $hasilPemeriksaanLab->pasien_jenis_kelamin,
                            $statusUmur
                        );
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
