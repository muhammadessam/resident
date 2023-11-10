<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;

class Visit extends Model
{
    use SoftDeletes;

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
    ];

    protected $casts = [
        'date_time' => 'datetime',
    ];

    public function resident(): BelongsTo
    {
        return $this->belongsTo(Resident::class, 'resident_id');
    }

    public function relative(): BelongsTo
    {
        return $this->belongsTo(Relative::class, 'relative_id');
    }
}
