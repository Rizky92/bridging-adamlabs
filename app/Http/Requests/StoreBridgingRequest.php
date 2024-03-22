<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreBridgingRequest extends FormRequest
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
            'no_registrasi'                             => 'required',
            'no_laboratorium'                           => 'required',
            'waktu_registrasi'                          => 'required|date',
            'diagnosa_awal'                             => 'required',
            'kode_RS'                                   => 'required',
            'kode_lab'                                  => 'required',
            'umur.tahun'                                => 'required|integer',
            'umur.bulan'                                => 'required|integer',
            'umur.hari'                                 => 'required|integer',
            'pasien'                                    => 'required|array',
            'dokter_pengirim'                           => 'required|array',
            'unit_asal'                                 => 'required|array',
            'penjamin'                                  => 'required|array',
            'pemeriksaan'                               => 'required|array',
            'pemeriksaan.*.nomor_urut'                  => 'required|integer',
            'pemeriksaan.*.kode_tindakan_simrs'         => 'required',
            'pemeriksaan.*.kode_pemeriksaan_lis'        => 'required',
            'pemeriksaan.*.nama_pemeriksaan_lis'        => 'required',
            'pemeriksaan.*.metode'                      => 'required',
            'pemeriksaan.*.waktu_pemeriksaan'           => 'required|date',
            'pemeriksaan.*.status_bridging'             => 'boolean',
            'pemeriksaan.*.kategori_pemeriksaan'        => 'required|array',
            'pemeriksaan.*.sub_kategori_pemeriksaan'    => 'required|array',
            'pemeriksaan.*.hasil'                       => 'required|array',
        ];
    }
}
