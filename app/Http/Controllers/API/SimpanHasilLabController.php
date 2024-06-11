<?php

namespace App\Http\Controllers\API;

use App\Http\Requests\API\SimpanHasilLabRequest;
use App\Models\SimpanHasilLab;
use App\Models\SimpanHasilLabDetail;
use Exception;
use Illuminate\Http\JsonResponse;

class SimpanHasilLabController
{
    public function __invoke(SimpanHasilLabRequest $request): JsonResponse
    {
        try {
            $data = $request->validated();

            SimpanHasilLab::create([
                'no_registrasi'        => $data['no_registrasi'],
                'no_laboratorium'      => $data['no_laboratorium'],
                'waktu_registrasi'     => $data['waktu_registrasi'],
                'diagnosa_awal'        => $data['diagnosa_awal'],
                'kode_rs'              => $data['kode_RS'],
                'kode_lab'             => $data['kode_lab'],
                'umur_tahun'           => $data['umur']['tahun'],
                'umur_bulan'           => $data['umur']['bulan'],
                'umur_hari'            => $data['umur']['hari'],
                'nama_pasien'          => $data['pasien']['nama_pasien'],
                'no_rm'                => $data['pasien']['no_rm'],
                'jenis_kelamin'        => $data['pasien']['jenis_kelamin'],
                'alamat'               => $data['pasien']['alamat'],
                'no_telphone'          => $data['pasien']['no_telphone'],
                'tanggal_lahir'        => $data['pasien']['tanggal_lahir'],
                'nik'                  => $data['pasien']['nik'],
                'ras'                  => $data['pasien']['ras'],
                'berat_badan'          => $data['pasien']['berat_badan'],
                'jenis_registrasi'     => $data['pasien']['jenis_registrasi'],
                'kode_dokter_pengirim' => $data['dokter_pengirim']['kode'],
                'nama_dokter_pengirim' => $data['dokter_pengirim']['nama'],
                'kode_unit_asal'       => $data['unit_asal']['kode'],
                'nama_unit_asal'       => $data['unit_asal']['nama'],
                'kode_penjamin'        => $data['penjamin']['kode'],
                'nama_penjamin'        => $data['penjamin']['nama'],
            ]);

            foreach ($data['pemeriksaan'] as [
                'nomor_urut'               => $urut,
                'kode_tindakan_simrs'      => $kodeSIMRS,
                'kode_pemeriksaan_lis'     => $kodeLIS,
                'nama_pemeriksaan_lis'     => $namaLIS,
                'metode'                   => $metode,
                'waktu_pemeriksaan'        => $waktuPemeriksaan,
                'kategori_pemeriksaan'     => [
                    'nama_kategori' => $namaKategori,
                    'nomor_urut'    => $urutKategori,
                ],
                'urut'                     => $urut,
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
                SimpanHasilLabDetail::create([
                    'no_laboratorium'              => $data['no_laboratorium'],
                    'nama_kategori_pemeriksaan'    => $namaKategori,
                    'urut_kategori_pemeriksaan'    => $urutKategori,
                    'nama_subkategori_pemeriksaan' => $namaSubKategori,
                    'urut_subkategori_pemeriksaan' => $urutSubKategori,
                    'urut'                         => $urut,
                    'kode_tindakan_simrs'          => $kodeSIMRS,
                    'kode_pemeriksaan_lis'         => $kodeLIS,
                    'nama_pemeriksaan_lis'         => $namaLIS,
                    'metode'                       => $metode,
                    'waktu_pemeriksaan'            => $waktuPemeriksaan,
                    'status_bridging'              => true,
                    'hasil_satuan'                 => $satuan,
                    'hasil_nilai_hasil'            => $nilaiHasil,
                    'hasil_nilai_rujukan'          => $nilaiRujukan,
                    'flag_kode'                    => $flagKode,
                ]);
            }

            return response()->json([
                'status' => true,
                'code' => 200,
                'message' => 'Data berhasil disimpan',
            ]);
        } catch (Exception $e) {
            return response()->json([
                'status' => false,
                'code' => 500,
                'message' => 'Terjadi kesalahan pada saat menyimpan data!',
            ]);
        }
    }
}
