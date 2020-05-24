<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Lararole\Traits\Activable;
use Lararole\Traits\Loggable;

class Product extends Model
{
    use Activable, SoftDeletes, Loggable;

    protected $fillable = [
        'attribute_id', 'brand_id', 'category_id', 'unit_id', 'name', 'handler', 'attribute_value', 'description', 'available_at', 'type',
    ];

    protected $guarded = [
        'continue', 'active',
    ];

    public function attributes()
    {
        return $this->belongsToMany(Attribute::class);
    }

    public function variations()
    {
        return $this->hasMany(Variation::class);
    }
}
