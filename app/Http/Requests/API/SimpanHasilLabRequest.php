<?php

namespace App\Http\Requests\API;

use App\Models\Registrasi;
use App\Rules\DoesntExist;
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
            'no_laboratorium'                                      => ['required', new DoesntExist(Registrasi::class, 'no_laboratorium')],
            'no_registrasi'                                        => ['required'],
            'waktu_registrasi'                                     => ['required', 'date'],
            'diagnosa_awal'                                        => ['nullable', 'string'],
            'kode_RS'                                              => ['required', 'string'],
            'kode_lab'                                             => ['required', 'string'],
            'umur.tahun'                                           => ['nullable', 'integer'],
            'umur.bulan'                                           => ['nullable', 'integer'],
            'umur.hari'                                            => ['nullable', 'integer'],
            'pasien.no_rm'                                         => ['required', 'string'],
            'pasien.nama_pasien'                                   => ['required', 'string'],
            'pasien.jenis_kelamin'                                 => ['nullable', 'string'],
            'pasien.tanggal_lahir'                                 => ['nullable', 'string'],
            'pasien.alamat'                                        => ['nullable', 'string'],
            'pasien.nik'                                           => ['nullable', 'string'],
            'pasien.no_telphone'                                   => ['nullable', 'string'],
            'pasien.ras'                                           => ['nullable', 'string'],
            'pasien.berat_badan'                                   => ['nullable', 'string'],
            'pasien.jenis_registrasi'                              => ['required', Rule::in(['Reguler', 'Cito'])],
            'dokter_pengirim.kode'                                 => ['nullable'],
            'dokter_pengirim.nama'                                 => ['nullable'],
            'unit_asal.kode'                                       => ['nullable'],
            'unit_asal.nama'                                       => ['nullable'],
            'penjamin.kode'                                        => ['nullable'],
            'penjamin.nama'                                        => ['nullable'],
            'pemeriksaan'                                          => ['array'],
            'pemeriksaan.*.nomor_urut'                             => ['nullable', 'integer'],
            'pemeriksaan.*.kode_tindakan_simrs'                    => ['required', 'string'],
            'pemeriksaan.*.kode_pemeriksaan_lis'                   => ['required', 'string'],
            'pemeriksaan.*.nama_pemeriksaan_lis'                   => ['required', 'string'],
            'pemeriksaan.*.metode'                                 => ['nullable', 'string'],
            'pemeriksaan.*.waktu_pemeriksaan'                      => ['nullable', 'date'],
            'pemeriksaan.*.status_bridging'                        => ['boolean'],
            'pemeriksaan.*.kategori_pemeriksaan.nama_kategori'     => ['nullable', 'string'],
            'pemeriksaan.*.kategori_pemeriksaan.nomor_urut'        => ['nullable', 'integer'],
            'pemeriksaan.*.sub_kategori_pemeriksaan.nama_kategori' => ['nullable', 'string'],
            'pemeriksaan.*.sub_kategori_pemeriksaan.nomor_urut'    => ['nullable', 'integer'],
            'pemeriksaan.*.hasil.satuan'                           => ['nullable', 'string'],
            'pemeriksaan.*.hasil.nilai_hasil'                      => ['required', 'string'],
            'pemeriksaan.*.hasil.nilai_rujukan'                    => ['nullable', 'string'],
            'pemeriksaan.*.hasil.flag_kode'                        => ['nullable', 'string'],
        ];
    }
}
