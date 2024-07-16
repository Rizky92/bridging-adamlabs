<?php

namespace App\Http\Controllers\API;

use App\Http\Requests\API\UpdateHasilLabRequest;
use App\Jobs\UpdateHasilLabKeSIMRS;
use App\Models\Pemeriksaan;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Arr;

class UpdateHasilLabController
{
    public function __invoke(UpdateHasilLabRequest $request): JsonResponse
    {
        $data = $request->validated();

        foreach ($data['pemeriksaan'] as $pemeriksaan) {
            if (Arr::get($pemeriksaan, 'status_bridging')) {
                Pemeriksaan::query()
                    ->where('no_laboratorium', $data['no_laboratorium'])
                    ->where('no_registrasi', $data['no_registrasi'])
                    ->where('kode_tindakan_simrs', Arr::get($pemeriksaan, 'kode_tindakan_simrs'))
                    ->where('kode_pemeriksaan_lis', Arr::get($pemeriksaan, 'kode_pemeriksaan_lis'))
                    ->where('nama_pemeriksaan_lis', Arr::get($pemeriksaan, 'nama_pemeriksaan_lis'))
                    ->update([
                        'status_bridging'   => true,
                        'hasil_nilai_hasil' => Arr::get($pemeriksaan, 'hasil.nilai_hasil'),
                        'hasil_flag_kode'   => Arr::get($pemeriksaan, 'hasil.flag_kode'),
                    ]);
            } else {
                Pemeriksaan::create([
                    'no_laboratorium'               => $data['no_laboratorium'],
                    'no_registrasi'                 => $data['no_registrasi'],
                    'kategori_pemeriksaan_nama'     => Arr::get($pemeriksaan, 'kategori_pemeriksaan.nama_kategori'),
                    'kategori_pemeriksaan_urut'     => Arr::get($pemeriksaan, 'kategori_pemeriksaan.nomor_urut'),
                    'sub_kategori_pemeriksaan_nama' => Arr::get($pemeriksaan, 'sub_kategori_pemeriksaan.nama_sub_kategori'),
                    'sub_kategori_pemeriksaan_urut' => Arr::get($pemeriksaan, 'sub_kategori_pemeriksaan.nomor_urut'),
                    'nomor_urut'                    => Arr::get($pemeriksaan, 'nomor_urut'),
                    'kode_tindakan_simrs'           => Arr::get($pemeriksaan, 'kode_tindakan_simrs'),
                    'kode_pemeriksaan_lis'          => Arr::get($pemeriksaan, 'kode_pemeriksaan_lis'),
                    'nama_pemeriksaan_lis'          => Arr::get($pemeriksaan, 'nama_pemeriksaan_lis'),
                    'metode'                        => Arr::get($pemeriksaan, 'metode'),
                    'waktu_pemeriksaan'             => Arr::get($pemeriksaan, 'waktu_pemeriksaan'),
                    'status_bridging'               => false,
                    'hasil_satuan'                  => Arr::get($pemeriksaan, 'hasil.satuan'),
                    'hasil_nilai_hasil'             => Arr::get($pemeriksaan, 'hasil.nilai_hasil'),
                    'hasil_nilai_rujukan'           => Arr::get($pemeriksaan, 'hasil.nilai_rujukan'),
                    'hasil_flag_kode'               => Arr::get($pemeriksaan, 'hasil.flag_kode'),
                    'compound'                      => sprintf('%s-%s-adamlabs', Arr::get($pemeriksaan, 'kode_pemeriksaan_lis'), Arr::get($pemeriksaan, 'kategori_pemeriksaan.nama_kategori')),
                ]);
            }
        }

        UpdateHasilLabKeSIMRS::dispatch([
            'no_laboratorium' => $data['no_laboratorium'],
            'no_registrasi'   => $data['no_registrasi'],
        ]);

        return response()->json([
            'status'  => true,
            'code'    => 200,
            'message' => 'Hasil pemeriksaan segera diproses.',
        ]);
    }
}
