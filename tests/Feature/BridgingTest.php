<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;

class BridgingTest extends TestCase
{
    /**
     * A basic feature test example.
     *
     * @return void
     */
    public function testStoreBridging()
    {
        $data = [
            "no_registrasi" => "89",
            "no_laboratorium" => "202212270001",
            "waktu_registrasi" => "2022-12-27 11:02:33",
            "diagnosa_awal" => "diagnosa",
            "kode_RS" => "RS02",
            "kode_lab" => "LAB_PK",
            "umur" => [
                "tahun" => 56,
                "bulan" => 6,
                "hari" => 10
            ],
            "pasien" => [
                "nama_pasien" => "testing",
                "no_rm" => "343434",
                "jenis_kelamin" => "L",
                "alamat" => "-",
                "no_telphone" => "-",
                "tanggal_lahir" => "1994-01-20",
                "nik" => "324008887878978978",
                "ras" => "Hitam/Putih",
                "berat_badan" => "45kg",
                "jenis_registrasi" => "Reguler / Cito"
            ],
            "dokter_pengirim" => [
                "kode" => "345",
                "nama" => "dr.hedy"
            ],
            "unit_asal" => [
                "kode" => "345",
                "nama" => "ruang"
            ],
            "penjamin" => [
                "nama" => "4678",
                "kode" => "bpjs"
            ],
            "pemeriksaan" => [
                [
                    "kategori_pemeriksaan" => [
                        "nama_kategori" => "HEMATOLOGI",
                        "nomor_urut" => 4
                    ],
                    "sub_kategori_pemeriksaan" => [
                        "nama_sub_kategori" => "Darah Rutin",
                        "nomor_urut" => 2
                    ],
                    "nomor_urut" => 2,
                    "kode_tindakan_simrs" => "HGB",
                    "kode_pemeriksaan_lis" => "HGB",
                    "nama_pemeriksaan_lis" => "Hemaglobin",
                    "metode" => "Imunokromatografi",
                    "waktu_pemeriksaan" => "2022-12-17 13:02:33",
                    "status_bridging" => true,
                    "hasil" => [
                        "satuan" => null,
                        "nilai_hasil" => "56",
                        "nilai_rujukan" => "34 - 100",
                        "flag_kode" => "N"
                    ]
                ],
                [
                    "kategori_pemeriksaan" => [
                        "nama_kategori" => "IMUNO-SEROLOGI",
                        "nomor_urut" => 4
                    ],
                    "sub_kategori_pemeriksaan" => [
                        "nama_sub_kategori" => "Infeski Lain",
                        "nomor_urut" => 2
                    ],
                    "nomor_urut" => 1,
                    "kode_tindakan_simrs" => "GOLDAR_IMUNO",
                    "kode_pemeriksaan_lis" => "GOLDAR_IMUNO ",
                    "nama_pemeriksaan_lis" => "Golongan darah",
                    "waktu_pemeriksaan" => "2022-12-17 13:02:33",
                    "metode" => "Imonokromatografi",
                    "hasil" => [
                        "satuan" => null,
                        "nilai_hasil" => "56",
                        "nilai_rujukan" => "34 - 100",
                        "flag_kode" => "N"
                    ]
                ]
            ]
        ];

        $response = $this->withHeaders([
            'x-api-key' => config('api.key'),
        ])->json('POST', '/api/adam-lis/bridging', $data);

        $response->assertStatus(200);
        $response->assertJson([
            'status' => true,
            'code' => 200,
            'message' => 'Data berhasil disimpan',
        ]);
    }

    public function testUpdateHasil()
    {
        $data = [
            "no_registrasi" => "89",
            "no_laboratorium" => "202212270001",
            "kode_RS" => "RS02",
            "kode_lab" => "LAB_PK",
            "pasien" => [
                "nama_pasien" => "testing",
                "no_rm" => "343434",
                "jenis_kelamin" => "L",
                "tanggal_lahir" => "1994-01-20",
                "nik" => "324008887878978978",
                "ras" => "Hitam/Putih",
                "berat_badan" => "45kg",
                "jenis_registrasi" => "Reguler / Cito"
            ],
            "pemeriksaan" => [
                [
                    "kategori_pemeriksaan" => [
                        "nama_kategori" => "HEMATOLOGI",
                    ],
                    "sub_kategori_pemeriksaan" => [
                        "nama_sub_kategori" => "Darah Rutin",
                    ],
                    "nomor_urut" => 2,
                    "kode_tindakan_simrs" => "HGB",
                    "kode_pemeriksaan_lis" => "HGB",
                    "nama_pemeriksaan_lis" => "Hemaglobin",
                    "hasil" => [
                        "nilai_hasil" => "56",
                        "flag_kode" => "N"
                    ]
                ],
                [
                    "kategori_pemeriksaan" => [
                        "nama_kategori" => "IMUNO-SEROLOGI",
                    ],
                    "sub_kategori_pemeriksaan" => [
                        "nama_sub_kategori" => "Infeski Lain",
                    ],
                    "nomor_urut" => 1,
                    "kode_tindakan_simrs" => "GOLDAR_IMUNO",
                    "kode_pemeriksaan_lis" => "GOLDAR_IMUNO ",
                    "nama_pemeriksaan_lis" => "Golongan darah",
                    "hasil" => [
                        "nilai_hasil" => "56",
                        "flag_kode" => "N"
                    ]
                ]
            ]
        ];

        $response = $this->withHeaders([
            'x-api-key' => config('api.key'),
        ])->json('POST', '/api/adam-lis/bridging/update-hasil', $data);

        $response->assertStatus(200);
        $response->assertJson([
            'status' => true,
            'code' => 200,
            'message' => 'Data berhasil diupdate',
        ]);
    }
}
