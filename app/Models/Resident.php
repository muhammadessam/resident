<?php

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\SoftDeletes;
use Spatie\MediaLibrary\HasMedia;
use Spatie\MediaLibrary\InteractsWithMedia;

class Resident extends Model implements HasMedia
{
    use SoftDeletes;
    use InteractsWithMedia;


    const METALDEGREE = [
        'simple' => 'اعاقة بسيط',
        'moderate' => 'اعاقة متوسطة',
        'strong' => 'اعاقة شديدة',
        'deep' => 'اعاقة عميقة',
    ];

    protected $fillable = [
        'name',
        'number',
        'type',
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


    public function registerMediaCollections(): void
    {
        $this->addMediaCollection('visit_allow_report')->singleFile()->useDisk('visit_allow_report');
        $this->addMediaCollection('uploads')->useDisk('uploads');
    }

    public function scopeMale(Builder $builder): Builder
    {
        return $builder->where('type', 'male');
    }

    public function scopeFemale(Builder $builder): Builder
    {
        return $builder->where('type', 'female');
    }

    public function age(): Attribute
    {
        return Attribute::make(get: fn() => Carbon::parse($this->dob)->age);
    }

    public function healthProblems(): BelongsToMany
    {
        return $this->belongsToMany(SubHealthProblem::class);
    }

    public function residentRelatives(): BelongsToMany
    {
        return $this->belongsToMany(Resident::class, 'resident_relative', 'resident_id', 'relative_id')
            ->using(ResidentRelative::class)
            ->withTimestamps();
    }
}
