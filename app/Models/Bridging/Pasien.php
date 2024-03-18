<?php

namespace App\Models\Bridging;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Pasien extends Model
{
    use HasFactory;

    protected $table = 'pasien';

    protected $fillable = [
        'nama_pasien',
        'no_rm',
        'jenis_kelamin',
        'alamat',
        'no_telphone',
        'tanggal_lahir',
        'nik',
        'ras',
        'berat_badan',
        'jenis_registrasi',
    ];

    public function registrasi()
    {
        return $this->belongsTo(Registrasi::class);
    }
}
