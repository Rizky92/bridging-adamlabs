<?php

namespace App\Http\Controllers\API;

use App\Http\Requests\API\SimpanHasilLabRequest;
use App\Jobs\SimpanHasilLabKeSIMRS;
use App\Models\Pemeriksaan;
use App\Models\Registrasi;
use Exception;
use Illuminate\Http\JsonResponse;

class SimpanHasilLabController
{
    public function __invoke(SimpanHasilLabRequest $request): JsonResponse
    {
        try {
            $data = $request->validated();

            Registrasi::create([
                'no_registrasi'           => $data['no_registrasi'],
                'no_laboratorium'         => $data['no_laboratorium'],
                'waktu_registrasi'        => $data['waktu_registrasi'],
                'diagnosa_awal'           => $data['diagnosa_awal'],
                'kode_rs'                 => $data['kode_RS'],
                'kode_lab'                => $data['kode_lab'],
                'umur_tahun'              => $data['umur']['tahun'],
                'umur_bulan'              => $data['umur']['bulan'],
                'umur_hari'               => $data['umur']['hari'],
                'pasien_no_rm'            => $data['pasien']['no_rm'],
                'pasien_nama_pasien'      => $data['pasien']['nama_pasien'],
                'pasien_jenis_kelamin'    => $data['pasien']['jenis_kelamin'],
                'pasien_tanggal_lahir'    => $data['pasien']['tanggal_lahir'],
                'pasien_alamat'           => $data['pasien']['alamat'],
                'pasien_nik'              => $data['pasien']['nik'],
                'pasien_no_telphone'      => $data['pasien']['no_telphone'],
                'pasien_ras'              => $data['pasien']['ras'],
                'pasien_berat_badan'      => $data['pasien']['berat_badan'],
                'pasien_jenis_registrasi' => $data['pasien']['jenis_registrasi'],
                'dokter_pengirim_kode'    => $data['dokter_pengirim']['kode'],
                'dokter_pengirim_nama'    => $data['dokter_pengirim']['nama'],
                'unit_asal_kode'          => $data['unit_asal']['kode'],
                'unit_asal_nama'          => $data['unit_asal']['nama'],
                'penjamin_kode'           => $data['penjamin']['kode'],
                'penjamin_nama'           => $data['penjamin']['nama'],
            ]);

            foreach ($data['pemeriksaan'] as [
                'kode_tindakan_simrs'      => $kodeSIMRS,
                'kode_pemeriksaan_lis'     => $kodeLIS,
                'nama_pemeriksaan_lis'     => $namaLIS,
                'metode'                   => $metode,
                'waktu_pemeriksaan'        => $waktuPemeriksaan,
                'kategori_pemeriksaan'     => [
                    'nama_kategori' => $namaKategori,
                    'nomor_urut'    => $urutKategori,
                ],
                'nomor_urut'               => $urut,
                'sub_kategori_pemeriksaan' => [
                    'nama_sub_kategori' => $namaSubKategori,
                    'nomor_urut'        => $urutSubKategori,
                ],
                'hasil'                    => [
                    'satuan'        => $satuan,
                    'nilai_hasil'   => $nilaiHasil,
                    'nilai_rujukan' => $nilaiRujukan,
                    'flag_kode'     => $flagKode,
                ]
            ]) {
                Pemeriksaan::create([
                    'no_laboratorium'               => $data['no_laboratorium'],
                    'no_registrasi'                 => $data['no_registrasi'],
                    'kategori_pemeriksaan_nama'     => $namaKategori,
                    'kategori_pemeriksaan_urut'     => $urutKategori,
                    'sub_kategori_pemeriksaan_nama' => $namaSubKategori,
                    'sub_kategori_pemeriksaan_urut' => $urutSubKategori,
                    'nomor_urut'                    => $urut,
                    'kode_tindakan_simrs'           => $kodeSIMRS,
                    'kode_pemeriksaan_lis'          => $kodeLIS,
                    'nama_pemeriksaan_lis'          => $namaLIS,
                    'metode'                        => $metode,
                    'waktu_pemeriksaan'             => $waktuPemeriksaan,
                    'status_bridging'               => false,
                    'hasil_satuan'                  => $satuan,
                    'hasil_nilai_hasil'             => $nilaiHasil,
                    'hasil_nilai_rujukan'           => $nilaiRujukan,
                    'hasil_flag_kode'               => $flagKode,
                ]);
            }

            SimpanHasilLabKeSIMRS::dispatch([
                'no_laboratorium' => $data['no_laboratorium'],
                'no_registrasi' => $data['no_registrasi'],
            ]);

            return response()->json([
                'status' => true,
                'code' => 200,
                'message' => 'Data berhasil disimpan',
            ]);
        } catch (Exception $e) {
            return response()->json([
                'status' => false,
                'code' => 500,
                'message' => $e->getMessage(),
            ]);
        }
    }
}
