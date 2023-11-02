<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
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
}
