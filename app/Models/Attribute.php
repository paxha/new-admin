<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Lararole\Traits\Activable;
use Lararole\Traits\Loggable;

class Attribute extends Model
{
    use Activable, SoftDeletes, Loggable;

    protected $fillable = [
        'name',
    ];

    protected $guarded = [
        'active',
    ];

    public static function boot()
    {
        parent::boot();

        self::deleting(function ($model) {
            $model->categories()->detach();

            $model->units()->detach();
        });
    }

    public function categories()
    {
        return $this->belongsToMany(Category::class);
    }

    public function units()
    {
        return $this->belongsToMany(Unit::class);
    }
}
