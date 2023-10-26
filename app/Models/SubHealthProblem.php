<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\SoftDeletes;

class SubHealthProblem extends Model
{
    use SoftDeletes;

    protected $fillable = [
        'name',
        'main_health_problem_id',
    ];

    public function mainHealthProblem(): BelongsTo
    {
        return $this->belongsTo(MainHealthProblem::class);
    }

    public function residents(): BelongsToMany
    {
        return $this->belongsToMany(Resident::class);
    }
}
