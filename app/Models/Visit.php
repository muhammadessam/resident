<?php

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;
use Mpdf\Tag\B;

class Visit extends Model
{
    use SoftDeletes, HasFactory;

    const TYPE = [
        'internal' => 'زيارة داخلية',
        'external' => 'زيارة خارجية',
    ];
    const DURATION_TYPE = [
        'days' => 'يوم',
        'hours' => 'ساعة',
    ];
    protected $fillable = [
        'resident_id',
        'relative_id',
        'type',
        'duration_type',
        'duration',
        'date_time',
        'companion_no',
        'created_by',
        'end_date'
    ];

    protected static function booted(): void
    {
        static::creating(function (Visit $visit) {
            if ($visit['type'] == 'internal') {
                $visit->end_date = Carbon::parse($visit->date_time)->addHour();
            } elseif ($visit->type == 'external') {
                $visit->end_date = Carbon::parse($visit->date_time)->add($visit->duration_type, $visit->duration);
            }
        });
        static::updating(function (Visit $visit) {
            if ($visit->isDirty(['type', 'date_time', 'duration_type', 'duration'])) {
                if ($visit['type'] == 'internal') {
                    $visit->end_date = Carbon::parse($visit->date_time)->addHour();
                } elseif ($visit->type == 'external') {
                    $visit->end_date = Carbon::parse($visit->date_time)->add($visit->duration_type, $visit->duration);
                }
            }
            if ($visit->isDirty('end_date')) {
                $duration = $visit->end_date->diffInDays($visit->date_time);
                if ($duration) {
                    $visit->duration = $duration;
                    $visit->duration_type = 'days';
                } else {
                    $visit->duration = $visit->end_date->diffInHours($visit->date_time);
                    $visit->duration_type = 'hours';
                };
            }
        });
    }

    protected $casts = [
        'date_time' => 'datetime',
        'end_date' => 'datetime',
    ];

    public function resident(): BelongsTo
    {
        return $this->belongsTo(Resident::class, 'resident_id');
    }

    public function relative(): BelongsTo
    {
        return $this->belongsTo(Relative::class, 'relative_id');
    }

    public function scopeExternal(Builder $builder): void
    {
        $builder->where('type', 'external');
    }

    public function scopeInternal(Builder $builder): void
    {
        $builder->where('type', 'internal');
    }

    public function createdBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by');
    }
}
