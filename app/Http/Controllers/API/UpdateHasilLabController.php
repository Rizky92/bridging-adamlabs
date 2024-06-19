<?php

namespace App\Http\Controllers\API;

use App\Http\Requests\API\UpdateHasilLabRequest;
use App\Jobs\SimpanHasilLabKeSIMRS;
use App\Models\Pemeriksaan;
use App\Models\Registrasi;
use Exception;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Arr;

class UpdateHasilLabController
{
    public function __invoke(UpdateHasilLabRequest $request): JsonResponse
    {
        try {
            $data = $request->validated();

            Registrasi::query()
                ->where('no_laboratorium', $data['no_laboratorium'])
                ->update([
                    'username'                => Arr::get($data, 'username'),
                    'nama_pegawai'            => Arr::get($data, 'nama_pegawai'),
                    'dokter_penanggung_jawab' => Arr::get($data, 'dokter_penanggung_jawab'),
                    'umur_tahun'              => Arr::get($data, 'umur.tahun'),
                    'umur_bulan'              => Arr::get($data, 'umur.bulan'),
                    'umur_hari'               => Arr::get($data, 'umur.hari'),
                    'pasien_no_rm'            => Arr::get($data, 'pasien.no_rm'),
                    'pasien_nama_pasien'      => Arr::get($data, 'pasien.nama_pasien'),
                    'pasien_jenis_kelamin'    => Arr::get($data, 'pasien.jenis_kelamin'),
                    'pasien_tanggal_lahir'    => Arr::get($data, 'pasien.tanggal_lahir'),
                    'pasien_alamat'           => Arr::get($data, 'pasien.alamat'),
                    'pasien_nik'              => Arr::get($data, 'pasien.nik'),
                    'pasien_no_telphone'      => Arr::get($data, 'pasien.no_telphone'),
                    'pasien_ras'              => Arr::get($data, 'pasien.ras'),
                    'pasien_berat_badan'      => Arr::get($data, 'pasien.berat_badan'),
                    'pasien_jenis_registrasi' => Arr::get($data, 'pasien.jenis_registrasi'),
                    'dokter_pengirim_kode'    => Arr::get($data, 'dokter_pengirim.kode'),
                    'dokter_pengirim_nama'    => Arr::get($data, 'dokter_pengirim.nama'),
                    'unit_asal_kode'          => Arr::get($data, 'unit_asal.kode'),
                    'unit_asal_nama'          => Arr::get($data, 'unit_asal.nama'),
                    'penjamin_kode'           => Arr::get($data, 'penjamin.kode'),
                    'penjamin_nama'           => Arr::get($data, 'penjamin.nama'),
                ]);

            foreach ($data['pemeriksaan'] as $pemeriksaan) {
                Pemeriksaan::query()
                    ->where('no_laboratorium', $data['no_laboratorium'])
                    ->where('no_registrasi', $data['no_registrasi'])
                    ->where('kode_tindakan_simrs', Arr::get($pemeriksaan, 'kode_tindakan_simrs'))
                    ->where('kode_pemeriksaan_lis', Arr::get($pemeriksaan, 'kode_pemeriksaan_lis'))
                    ->where('nama_pemeriksaan_lis', Arr::get($pemeriksaan, 'nama_pemeriksaan_lis'))
                    ->update([
                        'hasil_satuan'        => Arr::get($pemeriksaan, 'hasil.satuan'),
                        'hasil_nilai_hasil'   => Arr::get($pemeriksaan, 'hasil.nilai_hasil'),
                        'hasil_nilai_rujukan' => Arr::get($pemeriksaan, 'hasil.nilai_rujukan'),
                        'hasil_flag_kode'     => Arr::get($pemeriksaan, 'hasil.flag_kode'),
                    ]);
            }

            SimpanHasilLabKeSIMRS::dispatch([
                'no_laboratorium' => $data['no_laboratorium'],
                'no_registrasi' => $data['no_registrasi'],
            ]);
            
            return response()->json([
                'status' => true,
                'code' => 200,
                'message' => 'Data berhasil diupdate',
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
