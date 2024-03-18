<?php

namespace App\Models\Bridging;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Model;

class HRegistrasi extends Model
{
    use HasFactory;

    protected $table = 'h_registrasi';

    protected $fillable = [
        'no_lab',
        'no_reg_rs',
        'diagnosa_awal',
        'keterangan_klinis',
        'expertise',
        'waktu_expertise',
        'waktu_terbit',
        'waktu_registrasi',
        'total_bayar',
        'pasien_no_rm',
        'pasien_nama',
        'pasien_jenis_kelamin',
        'pasien_tanggal_lahir',
        'pasien_alamat',
        'pasien_no_telphone',
        'pasien_umur_hari',
        'pasien_umur_bulan',
        'pasien_umur_tahun',
        'dokter_pengirim_nama',
        'dokter_pengirim_kode',
        'dokter_pengirim_alamat',
        'dokter_pengirim_no_telphone',
        'dokter_pengirim_spesialis_nama',
        'dokter_pengirim_spesialis_kode',
        'unit_asal_nama',
        'unit_asal_kode',
        'unit_asal_kelas',
        'unit_asal_keterangan',
        'unit_asal_jenis_nama',
        'unit_asal_jenis_kode',
        'penjamin_nama',
        'penjamin_kode',
        'penjamin_jenis_nama',
        'penjamin_jenis_kode',
        'pasien_nik',
        'status_lis_simrs',
    ];

    public function hItemPemeriksaan()
    {
        return $this->hasMany(HItemPemeriksaan::class, 'h_registrasi_no_lab', 'no_lab');
    }
}
