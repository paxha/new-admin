<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Lararole\Traits\Activable;
use Lararole\Traits\Loggable;

class Unit extends Model
{
    use Activable, SoftDeletes, Loggable;

    protected $fillable = [
        'name',
    ];

    protected $guarded = [
        'active',
    ];
}
