<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\Pivot;

class RelativeResident extends Pivot
{
    use HasFactory;

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
        'granny' => 'جدة',
        'other' => 'غير ذلك'
    ];
    public $timestamps = true;
    protected $primaryKey = 'id';
    protected $fillable = ['id', 'resident_id', 'relative_id', 'relation', 'is_guardian', 'updated_at', 'created_at'];


}
