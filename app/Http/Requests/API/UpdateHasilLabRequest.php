<?php

namespace App\Http\Requests\API;

use Illuminate\Foundation\Http\FormRequest;

class UpdateHasilLabRequest extends FormRequest
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
            'no_registrasi'                                             => 'string',
            'no_laboratorium'                                           => 'string',
            'kode_RS'                                                   => 'string',
            'kode_lab'                                                  => 'string',
            'pasien.nama_pasien'                                        => 'string',
            'pasien.no_rm'                                              => 'string',
            'pasien.jenis_kelamin'                                      => 'string',
            'pasien.tanggal_lahir'                                      => 'date',
            'pasien.nik'                                                => 'string',
            'pasien.ras'                                                => 'string',
            'pasien.berat_badan'                                        => 'string',
            'pasien.jenis_registrasi'                                   => 'string',
            'pemeriksaan.*.kategori_pemeriksaan.nama_kategori'          => 'string',
            'pemeriksaan.*.sub_kategori_pemeriksaan.nama_sub_kategori'  => 'string',
            'pemeriksaan.*.nomor_urut'                                  => 'integer',
            'pemeriksaan.*.kode_tindakan_simrs'                         => 'string',
            'pemeriksaan.*.kode_pemeriksaan_lis'                        => 'string',
            'pemeriksaan.*.nama_pemeriksaan_lis'                        => 'string',
            'pemeriksaan.*.hasil.nilai_hasil'                           => 'string',
            'pemeriksaan.*.hasil.flag_kode'                             => 'string',
        ];
    }
}
