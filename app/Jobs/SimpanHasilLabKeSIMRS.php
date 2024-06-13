<?php

namespace App\Jobs;

use App\Models\Pemeriksaan;
use App\Models\Registrasi;
use App\Models\SimpanHasilLab;
use App\Models\SIMRS\HasilPeriksaLab;
use App\Models\SIMRS\PermintaanLabPK;
use App\Models\SIMRS\TindakanLab;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldBeUnique;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;

class SimpanHasilLabKeSIMRS implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    private $noLaboratoriumLIS;

    private $noOrderLabSIMRS;

    private $tglHasil;

    private $jam;

    /**
     * Create a new job instance.
     * 
     * @param  array{no_laboratorium: string, no_registrasi: string}  $options
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
        //
    }

    private function simpanHasilLab(): void
    {
        // registrasi pemeriksaan
        $hasilPemeriksaanLab = Registrasi::query()
            ->with('pemeriksaan')
            ->where('no_laboratorium', $this->noLaboratoriumLIS)
            ->where('no_registrasi', $this->noOrderLabSIMRS)
            ->first();

        $hasilLab = Pemeriksaan::query()
            ->with('registrasi', 'mappingTindakan.tindakan')
            ->where('no_laboratorium', $this->noLaboratoriumLIS)
            ->groupBy('id')
            ->get();

        // tindakan
        $tindakan = $hasilLab->pluck('kode_tindakan_simrs');

        // waktu pemeriksaan, gunakan waktu pemeriksaan pertama
        $waktuPeriksa = carbon($hasilLab
            ->first(['waktu_pemeriksaan'])
            ->waktu_pemeriksaan);

        $this->tglHasil = $waktuPeriksa->toDateString();

        $this->jam = $waktuPeriksa->format('H:i:s');

        $permintaanLab = PermintaanLabPK::query()
            ->where('noorder', $this->noOrderLabSIMRS)
            ->first();

        $periksaLab = [];

        TindakanLab::query()
            ->with('template')
            ->whereIn('kd_jenis_prw', $tindakan)
            ->get()
            ->each(function (TindakanLab $tindakan) use ($permintaanLab, &$periksaLab) {
                $hasilPeriksaLab = HasilPeriksaLab::create([
                    'no_rawat'               => $permintaanLab->no_rawat,
                    'nip'                    => '-',
                    'kd_jenis_prw'           => $tindakan->kd_jenis_prw,
                    'tgl_periksa'            => $this->tglHasil,
                    'jam'                    => $this->jam,
                    'dokter_perujuk'         => ,
                    'bagian_rs'              => ,
                    'bhp'                    => ,
                    'tarif_perujuk'          => ,
                    'tarif_tindakan_dokter'  => ,
                    'tarif_tindakan_petugas' => ,
                    'kso'                    => ,
                    'menejemen'              => ,
                    'biaya'                  => ,
                    'kd_dokter'              => ,
                    'status'                 => ,
                    'kategori'               => ,
                ]);
            });

        // $periksaLab[] = [
        //     'no_rawat',
        //     'nip',
        //     'kd_jenis_prw',
        //     'tgl_periksa',
        //     'jam',
        //     'dokter_perujuk',
        //     'bagian_rs',
        //     'bhp',
        //     'tarif_perujuk',
        //     'tarif_tindakan_dokter',
        //     'tarif_tindakan_petugas',
        //     'kso',
        //     'menejemen',
        //     'biaya',
        //     'kd_dokter',
        //     'status',
        //     'kategori',
        // ];
    }

    private function updatePermintaan(): void
    {
        
    }

    private function catatJurnal(): bool
    {
        return true;
    }
}
