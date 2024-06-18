<?php

namespace App\Models\SIMRS;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class MappingTindakan extends Model
{
    protected $connection = 'mysql_sik';

    protected $table = 'mapping_adamlabs';

    protected $primaryKey = 'id';

    protected $keyType = 'int';

    public $incrementing = true;

    public $timestamps = false;

    public function tindakan(): BelongsTo
    {
        return $this->belongsTo(TindakanLab::class, 'kd_jenis_prw', 'kd_jenis_prw');
    }

    public function template(): BelongsTo
    {
        return $this->belongsTo(TindakanLabTemplate::class, 'id_template', 'id_template');
    }
}
