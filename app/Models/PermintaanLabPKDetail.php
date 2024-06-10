<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class PermintaanLabPKDetail extends Model
{
    protected $connection = 'mysql_sik';

    protected $table = 'permintaan_detail_permintaan_lab';

    protected $primaryKey = 'noorder';

    protected $keyType = 'string';

    public $incrementing = false;

    public $timestamps = false;
}
