<?php

namespace App\Models\Bridging;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class HItemPemeriksaan extends Model
{
    use HasFactory;

    protected $table = 'h_item_pemeriksaan';

    protected $fillable = [
        'h_registrasi_no_lab',
        'waktu_pemeriksaan_di_isi',
        'waktu_verifikasi',
        'hasil_pemeriksaan',
        'keterangan',
        'nilai_rujukan_tampilan_nilai_rujukan',
        'item_pemeriksaan_kode',
        'item_pemeriksaan_nama',
        'item_pemeriksaan_satuan',
        'item_pemeriksaan_moetode',
        'item_pemeriksaan_no_urut',
        'item_pemeriksaan_jenis_input',
        'item_pemeriksaan_is_tampilkan_waktu_periksa',
        'kategori_pemeriksaan_nama',
        'kategori_pemeriksaan_kode',
        'kategori_pemeriksaan_no_urut',
        'sub_kategori_pemeriksaan_nama',
        'sub_kategori_pemeriksaan_kode',
        'sub_kategori_pemeriksaan_no_urut',
        'flag_nama',
        'flag_kode',
        'flag_warna',
        'flag_jenis_pewarnaan',
    ];

    public function hRegistrasi()
    {
        return $this->belongsTo(HRegistrasi::class, 'h_registrasi_no_lab', 'no_lab');
    }
}
