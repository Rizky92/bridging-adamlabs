<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
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
use Illuminate\Http\Request;

class BridgingAdamlabsController extends Controller
{
    public function __invoke(Request $request)
    {
        if ($request->header('x-api-key') !== env('API_KEY')) {
            return response()->json([
                'status' => false,
                'code' => 401,
                'message' => 'Unauthorized',
            ], 401);
        }

        $data = $request->all();

        DB::beginTransaction();

        try {  
            $dokter_pengirim = DokterPengirim::firstOrCreate($data['dokter_pengirim']);
            $unit_asal = UnitAsal::firstOrCreate($data['unit_asal']);
            $penjamin = Penjamin::firstOrCreate($data['penjamin']);
            $pasien = Pasien::firstOrCreate($data['pasien']);

            $registrasi = Registrasi::create([
                'no_registrasi' => $data['no_registrasi'],
                'no_laboratorium' => $data['no_laboratorium'],
                'waktu_registrasi' => $data['waktu_registrasi'],
                'diagnosa_awal' => $data['diagnosa_awal'],
                'kode_RS' => $data['kode_RS'],
                'kode_lab' => $data['kode_lab'],
                'umur_tahun' => $data['umur']['tahun'],
                'umur_bulan' => $data['umur']['bulan'],
                'umur_hari' => $data['umur']['hari'],
                'pasien_id' => $pasien->id,
                'dokter_pengirim_id' => $dokter_pengirim->id,
                'unit_asal_id' => $unit_asal->id,
                'penjamin_id' => $penjamin->id,
            ]);

            foreach ($data['pemeriksaan'] as $pemeriksaanData) {
                $kategori_pemeriksaan = KategoriPemeriksaan::firstOrCreate($pemeriksaanData['kategori_pemeriksaan']);
                $sub_kategori_pemeriksaan = SubKategoriPemeriksaan::firstOrCreate($pemeriksaanData['sub_kategori_pemeriksaan']);
                $hasil = Hasil::create($pemeriksaanData['hasil']);

                Pemeriksaan::create([
                    'nomor_urut' => $pemeriksaanData['nomor_urut'],
                    'kode_tindakan_simrs' => $pemeriksaanData['kode_tindakan_simrs'],
                    'kode_pemeriksaan_lis' => $pemeriksaanData['kode_pemeriksaan_lis'],
                    'nama_pemeriksaan_lis' => $pemeriksaanData['nama_pemeriksaan_lis'],
                    'metode' => $pemeriksaanData['metode'],
                    'waktu_pemeriksaan' => $pemeriksaanData['waktu_pemeriksaan'],
                    'status_bridging' => isset($pemeriksaanData['status_bridging']) ? $pemeriksaanData['status_bridging'] : false, 
                    'kategori_pemeriksaan_id' => $kategori_pemeriksaan->id,
                    'sub_kategori_pemeriksaan_id' => $sub_kategori_pemeriksaan->id,  // Now $sub_kategori_pemeriksaan is defined
                    'hasil_id' => $hasil->id,
                    'registrasi_id' => $registrasi->id,
                ]);
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
                'message' => 'Terjadi kesalahan: ' . $e->getMessage(),
            ], 500);
        }
    }
}
