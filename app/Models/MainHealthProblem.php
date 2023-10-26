<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;

class MainHealthProblem extends Model
{
    use SoftDeletes;

    protected $fillable = [
        'name',
    ];


    public function subHealthProblems(): HasMany
    {
        return $this->hasMany(SubHealthProblem::class, 'main_health_problem_id');
    }
}
