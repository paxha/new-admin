<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Lararole\Traits\Activable;
use Lararole\Traits\Loggable;
use RecursiveRelationships\Traits\HasRecursiveRelationships;
use Sluggable\Traits\Sluggable;

class Category extends Model
{
    use Sluggable, Activable, HasRecursiveRelationships, SoftDeletes, Loggable;

    protected $fillable = [
        'parent_id', 'name', 'icon'
    ];

    protected $guarded = [
        'active',
    ];

    public static function boot()
    {
        parent::boot();

        self::updating(function ($model) {
            if (! $model->active) {
                foreach ($model->children as $child) {
                    $child->active = false;
                    $child->save();
                }
            } else {
                if ($model->parent) {
                    $model->parent->active = true;
                    $model->parent->save();
                }
            }
        });

        self::deleting(function ($model) {
            $model->children()->delete();
        });
    }
}
