<?php

namespace App\Http\Requests\API;

use Illuminate\Foundation\Http\FormRequest;

class SimpanHasilLabRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize()
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {
        return [
            'no_registrasi'                          => ['required'],
            'no_laboratorium'                        => ['required'],
            'waktu_registrasi'                       => ['required', 'date'],
            'diagnosa_awal'                          => ['required'],
            'kode_RS'                                => ['string'],
            'kode_lab'                               => ['required'],
            'umur.tahun'                             => ['required', 'integer'],
            'umur.bulan'                             => ['required', 'integer'],
            'umur.hari'                              => ['required', 'integer'],
            'pasien.no_rm'                           => ['required'],
            'pasien.nama_pasien'                     => ['required'],
            'pasien.jenis_kelamin'                   => ['required'],
            'pasien.tanggal_lahir'                   => ['required'],
            'pasien.alamat'                          => ['required'],
            'pasien.nik'                             => ['required'],
            'pasien.no_telphone'                     => ['required'],
            'pasien.ras'                             => ['required'],
            'pasien.berat_badan'                     => ['required'],
            'pasien.jenis_registrasi'                => ['required'],
            'dokter_pengirim.kode'                   => ['required'],
            'dokter_pengirim.nama'                   => ['required'],
            'unit_asal.kode'                         => ['required'],
            'unit_asal.nama'                         => ['required'],
            'penjamin.kode'                          => ['required'],
            'penjamin.nama'                          => ['required'],
            'pemeriksaan'                            => ['array'],
            'pemeriksaan.*.nomor_urut'               => ['required', 'integer'],
            'pemeriksaan.*.kode_tindakan_simrs'      => ['required', 'string'],
            'pemeriksaan.*.kode_pemeriksaan_lis'     => ['required', 'string'],
            'pemeriksaan.*.nama_pemeriksaan_lis'     => ['required', 'string'],
            'pemeriksaan.*.metode'                   => ['required', 'string'],
            'pemeriksaan.*.waktu_pemeriksaan'        => ['required', 'date'],
            'pemeriksaan.*.status_bridging'          => ['boolean'],
            'pemeriksaan.*.kategori_pemeriksaan'     => ['required', 'array'],
            'pemeriksaan.*.sub_kategori_pemeriksaan' => ['required', 'array'],
            'pemeriksaan.*.hasil'                    => ['required', 'array'],
        ];
    }
}
