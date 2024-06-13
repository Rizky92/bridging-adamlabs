<?php

namespace App\Models\SIMRS;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class TindakanLabTemplate extends Model
{
    protected $connection = 'mysql_sik';

    protected $table = 'template_laboratorium';

    protected $primaryKey = 'id_template';

    protected $keyType = 'int';

    public $incrementing = true;

    public $timestamps = false;

    public function tindakan(): BelongsTo
    {
        return $this->belongsTo(TindakanLab::class, 'kd_jenis_prw', 'kd_jenis_prw');
    }
}
