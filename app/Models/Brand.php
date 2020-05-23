<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Lararole\Traits\Activable;
use Lararole\Traits\Loggable;
use Sluggable\Traits\Sluggable;

class Brand extends Model
{
    use Sluggable, Activable, SoftDeletes, Loggable;

    protected $fillable = [
        'name', 'description', 'iso2', 'logo', 'cover', 'meta_title', 'meta_keywords', 'meta_description',
    ];

    protected $guarded = [
        'popular', 'active',
    ];

    public function togglePopular()
    {
        $this->popular = !$this->popular;
        $this->save();
    }
}
