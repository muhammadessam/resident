<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\SoftDeletes;

class Relative extends Model
{
    use SoftDeletes;

    protected $fillable = [
        'name',
        'id_number',
        'phone1',
        'phone2',
        'phone3',
    ];

    public function residents(): BelongsToMany
    {
        return $this->belongsToMany(Resident::class, 'relative_resident')
            ->withTimestamps()
            ->using(RelativeResident::class)->withPivot('relation', 'is_guardian');
    }
}
