<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Facades\DB;
use Lararole\Traits\Activable;
use Lararole\Traits\Loggable;

class Variation extends Model
{
    use Activable, SoftDeletes, Loggable;

    protected $fillable = [
        'sku', 'slug', 'description', 'price',
    ];

    protected $guarded = [
        'continue', 'active',
    ];

    protected $appends = [
        'name',
    ];

    public function getNameAttribute()
    {
        return DB::select('select variation_name(?) as variation_name;', [$this->id])[0]->variation_name;
    }

    public function product()
    {
        return $this->belongsTo(Product::class);
    }

    public function attributes()
    {
        return $this->belongsToMany(Attribute::class);
    }

    public function barcodes()
    {
        return $this->hasMany(Barcode::class);
    }

    public function toggleContinue()
    {
        $this->continue = !$this->continue;
        $this->save();
    }
}
