<?php

namespace BadMushroom\LaravelTourist\Models;

use Illuminate\Database\Eloquent\Model;

class TourSession extends Model
{
    protected $table = 'tour_sessions';

    protected $fillable = [
        'passport',
        'user_agent',
        'device',
        'browser',
        'platform',
        'referrer',
        'is_bot',
        'tour_started_at',
        'utm_source',
        'utm_medium',
        'utm_campaign',
        'utm_content',
        'utm_term',
    ];

    public function visits()
    {
        return $this->hasMany(TourVisit::class, 'passport', 'passport');
    }
}
