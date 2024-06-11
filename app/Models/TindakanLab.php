<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class TindakanLab extends Model
{
    protected $connection = 'mysql_sik';

    protected $table = 'mapping_adamlabs';

    protected $primaryKey = 'id';

    protected $keyType = 'int';

    public $incrementing = true;

    public $timestamps = false;
}
