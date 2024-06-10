<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Http\Requests\StoreBridgingRequest;
use App\Http\Requests\UpdateHasilRequest;
use App\Models\Bridging\DokterPengirim;
use App\Models\Bridging\Hasil;
use App\Models\Bridging\KategoriPemeriksaan;
use App\Models\Bridging\Pasien;
use App\Models\Bridging\Pemeriksaan;
use App\Models\Bridging\Penjamin;
use App\Models\Bridging\Registrasi;
use App\Models\Bridging\SubKategoriPemeriksaan;
use App\Models\Bridging\UnitAsal;
use Illuminate\Support\Facades\DB;

class BridgingAdamlabsController extends Controller
{
    public function store(StoreBridgingRequest $request)
    {
        try {
            DB::beginTransaction();

            $data = $request->validated();

            $dokter_pengirim    = DokterPengirim::firstOrCreate($data['dokter_pengirim']);
            $unit_asal          = UnitAsal::firstOrCreate($data['unit_asal']);
            $penjamin           = Penjamin::firstOrCreate($data['penjamin']);
            $pasien             = Pasien::firstOrCreate($data['pasien']);

            $registrasi = new Registrasi([
                'pasien_id'             => $pasien->id,
                'no_registrasi'         => $data['no_registrasi'],
                'no_laboratorium'       => $data['no_laboratorium'],
                'waktu_registrasi'      => $data['waktu_registrasi'],
                'diagnosa_awal'         => $data['diagnosa_awal'],
                'kode_RS'               => $data['kode_RS'],
                'kode_lab'              => $data['kode_lab'],
                'umur_tahun'            => $data['umur']['tahun'],
                'umur_bulan'            => $data['umur']['bulan'],
                'umur_hari'             => $data['umur']['hari'],
            ]);
            
            $registrasi->dokterPengirim()->associate($dokter_pengirim);
            $registrasi->unitAsal()->associate($unit_asal);
            $registrasi->penjamin()->associate($penjamin);
            $registrasi->save();

            foreach ($data['pemeriksaan'] as $pemeriksaanData) {
                $kategori_pemeriksaan       = KategoriPemeriksaan::firstOrCreate($pemeriksaanData['kategori_pemeriksaan']);
                $sub_kategori_pemeriksaan   = SubKategoriPemeriksaan::firstOrCreate($pemeriksaanData['sub_kategori_pemeriksaan']);
                $hasil                      = Hasil::create($pemeriksaanData['hasil']);

                $pemeriksaan = new Pemeriksaan([
                    'nomor_urut'                => $pemeriksaanData['nomor_urut'],
                    'kode_tindakan_simrs'       => $pemeriksaanData['kode_tindakan_simrs'],
                    'kode_pemeriksaan_lis'      => $pemeriksaanData['kode_pemeriksaan_lis'],
                    'nama_pemeriksaan_lis'      => $pemeriksaanData['nama_pemeriksaan_lis'],
                    'metode'                    => $pemeriksaanData['metode'],
                    'waktu_pemeriksaan'         => $pemeriksaanData['waktu_pemeriksaan'],
                    'status_bridging'           => isset($pemeriksaanData['status_bridging']) ? $pemeriksaanData['status_bridging'] : false, 
                ]);

                $pemeriksaan->kategoriPemeriksaan()->associate($kategori_pemeriksaan);
                $pemeriksaan->subKategoriPemeriksaan()->associate($sub_kategori_pemeriksaan);
                $pemeriksaan->hasil()->associate($hasil);
                $pemeriksaan->registrasi()->associate($registrasi);
                $pemeriksaan->save();
            }
            DB::commit();

            return response()->json([
                'status' => true,
                'code' => 200,
                'message' => 'Data berhasil disimpan',
            ], 200);
        } catch (\Exception $e) {
            DB::rollBack();

            return response()->json([
                'status' => false,
                'code' => 500,
                'message' => 'Terjadi kesalahan',
            ], 500);
        }
    }

    public function updateHasil(UpdateHasilRequest $request)
    {
        try {
            DB::beginTransaction();

            $data = $request->validated();

            $registrasi = Registrasi::where('no_registrasi', $data['no_registrasi'])->first();

            if (!$registrasi) {
                throw new \Exception('No registrasi ' . $data['no_registrasi'] . ' not found');
            }

            $updatedPemeriksaan = [];

            foreach ($data['pemeriksaan'] as $pemeriksaanData) {
                $pemeriksaan = Pemeriksaan::where('nomor_urut', $pemeriksaanData['nomor_urut'])
                    ->where('registrasi_id', $registrasi->id)
                    ->first();

                if (!$pemeriksaan) {
                    throw new \Exception('Pemeriksaan with nomor_urut ' . $pemeriksaanData['nomor_urut'] . ' not found');
                }

                $hasil = $pemeriksaan->hasil;
                $hasil->nilai_hasil = $pemeriksaanData['hasil']['nilai_hasil'];
                $hasil->flag_kode = $pemeriksaanData['hasil']['flag_kode'];
                $hasil->save();

                $updatedPemeriksaan[] = $pemeriksaan;
            }

            DB::commit();

            return response()->json([
                'status' => true,
                'code' => 200,
                'message' => 'Data berhasil diupdate',
            ], 200);
        } catch (\Exception $e) {
            DB::rollBack();

            return response()->json([
                'status' => false,
                'code' => 500,
                'message' => 'Terjadi kesalahan',
            ], 500);
        }
    }
}
