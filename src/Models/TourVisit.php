<?php

namespace BadMushroom\LaravelTourist\Models;

use Illuminate\Database\Eloquent\Model;

class TourVisit extends Model
{
    protected $table = 'tour_visits';

    protected $fillable = [
        'tourable_type',
        'tourable_id',
        'passport',
        'visited_at',
    ];

    protected $with = [
        'session',
    ];

    public function tourable()
    {
        return $this->morphTo();
    }

    public function session()
    {
        return $this->belongsTo(TourSession::class, 'passport', 'passport');
    }
}
