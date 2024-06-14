<?php

namespace App\Http\Requests\API;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

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
            'no_registrasi'                                        => ['required'],
            'no_laboratorium'                                      => ['required'],
            'waktu_registrasi'                                     => ['required', 'date'],
            'diagnosa_awal'                                        => ['sometimes', 'nullable', 'string'],
            'kode_RS'                                              => ['required', 'string'],
            'kode_lab'                                             => ['sometimes', 'required', 'string'],
            'umur.tahun'                                           => ['nullable', 'integer'],
            'umur.bulan'                                           => ['nullable', 'integer'],
            'umur.hari'                                            => ['nullable', 'integer'],
            'pasien.no_rm'                                         => ['required', 'string'],
            'pasien.nama_pasien'                                   => ['required', 'string'],
            'pasien.jenis_kelamin'                                 => ['required', 'string'],
            'pasien.tanggal_lahir'                                 => ['required', 'string'],
            'pasien.alamat'                                        => ['required', 'string'],
            'pasien.nik'                                           => ['required', 'string'],
            'pasien.no_telphone'                                   => ['required', 'string'],
            'pasien.ras'                                           => ['required', 'string'],
            'pasien.berat_badan'                                   => ['required', 'string'],
            'pasien.jenis_registrasi'                              => ['sometimes', 'required', Rule::in(['Reguler', 'Cito'])],
            'dokter_pengirim.kode'                                 => ['nullable'],
            'dokter_pengirim.nama'                                 => ['nullable'],
            'unit_asal.kode'                                       => ['nullable'],
            'unit_asal.nama'                                       => ['nullable'],
            'penjamin.kode'                                        => ['nullable'],
            'penjamin.nama'                                        => ['nullable'],
            'pemeriksaan'                                          => ['array'],
            'pemeriksaan.*.nomor_urut'                             => ['sometimes', 'nullable', 'integer'],
            'pemeriksaan.*.kode_tindakan_simrs'                    => ['required', 'string'],
            'pemeriksaan.*.kode_pemeriksaan_lis'                   => ['required', 'string'],
            'pemeriksaan.*.nama_pemeriksaan_lis'                   => ['required', 'string'],
            'pemeriksaan.*.metode'                                 => ['nullable', 'string'],
            'pemeriksaan.*.waktu_pemeriksaan'                      => ['nullable', 'date'],
            'pemeriksaan.*.status_bridging'                        => ['boolean'],
            'pemeriksaan.*.kategori_pemeriksaan.nama_kategori'     => ['sometimes', 'nullable', 'string'],
            'pemeriksaan.*.kategori_pemeriksaan.nomor_urut'        => ['sometimes', 'nullable', 'integer'],
            'pemeriksaan.*.sub_kategori_pemeriksaan.nama_kategori' => ['sometimes', 'nullable', 'string'],
            'pemeriksaan.*.sub_kategori_pemeriksaan.nomor_urut'    => ['sometimes', 'nullable', 'integer'],
            'pemeriksaan.*.hasil.satuan'                           => ['sometimes', 'nullable', 'string'],
            'pemeriksaan.*.hasil.nilai_hasil'                      => ['required', 'string'],
            'pemeriksaan.*.hasil.nilai_rujukan'                    => ['sometimes', 'nullable', 'string'],
            'pemeriksaan.*.hasil.flag_kode'                        => ['sometimes', 'nullable', 'string'],
        ];
    }
}
