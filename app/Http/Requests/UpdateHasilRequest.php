<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class UpdateHasilRequest extends FormRequest
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
            'no_registrasi'                                             => 'required|string',
            'no_laboratorium'                                           => 'required|string',
            'kode_RS'                                                   => 'required|string',
            'kode_lab'                                                  => 'required|string',
            'pasien.nama_pasien'                                        => 'required|string',
            'pasien.no_rm'                                              => 'required|string',
            'pasien.jenis_kelamin'                                      => 'required|string',
            'pasien.tanggal_lahir'                                      => 'required|date',
            'pasien.nik'                                                => 'required|string',
            'pasien.ras'                                                => 'required|string',
            'pasien.berat_badan'                                        => 'required|string',
            'pasien.jenis_registrasi'                                   => 'required|string',
            'pemeriksaan.*.kategori_pemeriksaan.nama_kategori'          => 'required|string',
            'pemeriksaan.*.sub_kategori_pemeriksaan.nama_sub_kategori'  => 'required|string',
            'pemeriksaan.*.nomor_urut'                                  => 'required|integer',
            'pemeriksaan.*.kode_tindakan_simrs'                         => 'required|string',
            'pemeriksaan.*.kode_pemeriksaan_lis'                        => 'required|string',
            'pemeriksaan.*.nama_pemeriksaan_lis'                        => 'required|string',
            'pemeriksaan.*.hasil.nilai_hasil'                           => 'required|string',
            'pemeriksaan.*.hasil.flag_kode'                             => 'required|string',
        ];
    }
}
