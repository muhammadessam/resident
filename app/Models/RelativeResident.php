<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Relations\Pivot;

class RelativeResident extends Pivot
{
    const RELATION = [
        'father' => 'اب',
        'mother' => 'ام',
        'brother' => 'اخ',
        'sister' => 'اخت',
        'maternal_uncle' => 'خال',
        'maternal_aunt' => 'خالة',
        'uncle' => 'عم',
        'aunt' => 'عمة',
        'grand' => 'جد',
    ];
    public $timestamps = true;
    protected $primaryKey = 'id';
    protected $fillable = ['id', 'resident_id', 'relative_id', 'relation', 'is_guardian', 'updated_at', 'created_at'];

}
