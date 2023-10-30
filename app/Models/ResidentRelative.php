<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Relations\Pivot;
use Illuminate\Database\Eloquent\SoftDeletes;

class ResidentRelative extends Pivot
{
    use SoftDeletes;

    public $timestamps = true;
    protected $fillable = [
        'id',
        'resident_id',
        'relative_id',
        'created_at',
        'updated_at'
    ];


}
