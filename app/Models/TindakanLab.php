<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class TindakanLab extends Model
{
    protected $connection = 'mysql_sik';

    protected $table = 'jns_perawatan_lab';

    protected $primaryKey = 'kd_jenis_prw';

    protected $keyType = 'string';

    public $incrementing = false;

    public $timestamps = false;
}
