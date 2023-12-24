<?php

namespace App\Models;

use App\Traits\InteractsWithSoftCascadedRelation;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Database\Eloquent\SoftDeletes;
use Spatie\MediaLibrary\HasMedia;
use Spatie\MediaLibrary\InteractsWithMedia;

class Resident extends Model implements HasMedia
{
    use SoftDeletes;
    use InteractsWithMedia;
    use InteractsWithSoftCascadedRelation;
    use HasFactory;

    protected static array $relations_to_cascade = ['visits', 'relatives'];

    protected $with = ['lastVisit'];

    const TYPE = [
        'male' => 'الذكور',
        'female' => 'الاناث',
    ];
    const MALE_BUILDINGS = [
        "1" => "1",
        "2" => "2",
        "3" => "3",
        "4" => "4",
        "5" => "5",
        "6" => "6",
    ];

    const FEMALE_BUILDINGS = [
        "A1" => "A1",
        'A2' => 'A2',
        'A3' => 'A3',
        'A4' => 'A4',
        'B1' => 'B1',
        'B2' => 'B2',
        'B3' => 'B3',
        'B4' => 'B4',
        'B5' => 'B5',
        'B6' => 'B6',
        'DRO' => 'DRO'
    ];
    const BUILDINGS = [
        "1" => "1",
        "2" => "2",
        "3" => "3",
        "4" => "4",
        "5" => "5",
        "6" => "6",
        "A1" => "A1",
        'A2' => 'A2',
        'A3' => 'A3',
        'A4' => 'A4',
        'B1' => 'B1',
        'B2' => 'B2',
        'B3' => 'B3',
        'B4' => 'B4',
        'B5' => 'B5',
        'B6' => 'B6',
        'DRO' => 'DRO'
    ];
    const METAL_DEGREE = [
        'simple' => 'اعاقة بسيطة',
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
        'city_id',
        'deletion_reason',
        'created_at',
        'updated_at',
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

    public function residentialRelatives(): BelongsToMany
    {
        return $this->belongsToMany(Resident::class, 'resident_resident', 'resident_id', 'relative_id');
    }

    public function relativesResidential(): BelongsToMany
    {
        return $this->belongsToMany(Resident::class, 'resident_resident', 'relative_id', 'relative_id');
    }


    public function relatives(): BelongsToMany
    {
        return $this->belongsToMany(Relative::class, 'relative_resident')
            ->withTimestamps()
            ->using(RelativeResident::class)->withPivot('relation', 'is_guardian');
    }

    public function visits(): HasMany
    {
        return $this->hasMany(Visit::class, 'resident_id');
    }

    public function internalVisits(): HasMany
    {
        return $this->hasMany(Visit::class, 'resident_id')->where('type', 'internal');
    }

    public function externalVisits(): HasMany
    {
        return $this->hasMany(Visit::class, 'resident_id')->where('type', 'external');
    }

    public function city(): BelongsTo
    {
        return $this->belongsTo(City::class, 'city_id');
    }

    public function lastVisit(): HasOne
    {
        return $this->hasOne(Visit::class, 'resident_id')->latestOfMany('date_time');
    }
}
