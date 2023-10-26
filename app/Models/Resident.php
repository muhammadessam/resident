<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\SoftDeletes;

class Resident extends Model
{
    use SoftDeletes;

    const METALDEGREE = [
        'simple' => 'اعاقة بسيط',
        'moderate' => 'اعاقة متوسطة',
        'strong' => 'اعاقة شديدة',
        'deep' => 'اعاقة عميقة',
    ];

    protected $fillable = [
        'name',
        'number',
        'dob',
        'doe',
        'building',
        'ability_to_external_visit',
        'external_visit_authorized',
        'internal_visit_authorized',
        'notes',
        'mental_disability_degree',
    ];

    protected $casts = [
        'dob' => 'date',
        'doe' => 'date',
    ];


    public function scopeMale(Builder $builder): Builder
    {
        return $builder->where('type', 'male');
    }

    public function scopeFemale(Builder $builder): Builder
    {
        return $builder->where('type', 'female');
    }

    public function healthProblems(): BelongsToMany
    {
        return $this->belongsToMany(SubHealthProblem::class);
    }
}
