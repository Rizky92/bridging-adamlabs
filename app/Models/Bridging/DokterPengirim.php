<?php

namespace App\Models\Bridging;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class DokterPengirim extends Model
{
    use HasFactory;

    protected $table = 'dokter_pengirim';

    protected $fillable = [
        'kode',
        'nama',
    ];

    public function registrasi()
    {
        return $this->hasMany(Registrasi::class);
    }
    
}
