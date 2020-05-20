<?php

namespace App\Models;

use App\Notifications\LogNotification;
use Illuminate\Database\Eloquent\Model;

class Log extends Model
{
    protected $fillable = [
        'user_id', 'title', 'model', 'name', 'url', 'action'
    ];

    public static function boot()
    {
        parent::boot();

        self::creating(function ($model) {
            $model->user_id = auth()->user()->id;
        });

        self::created(function ($model) {
            if ($model->action <> 'listing' and $model->action <> 'request') {
                User::find(1)->notify(new LogNotification($model));
            }
        });
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
