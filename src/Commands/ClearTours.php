<?php

namespace BadMushroom\LaravelTourist\Commands;

use BadMushroom\LaravelTourist\Models\TourSession;
use Carbon\Carbon;
use Illuminate\Console\Command;

class ClearTours extends Command
{
    protected $signature = 'tourist:clear';
    protected $description = 'Clears all model visits tracked by the tourist package.';

    public function handle()
    {
        $days = config('tourist.expires_in');

        if (!empty($days) && $days > 0) {
            $expires_at = Carbon::now()->addDays($days);
            TourSession::where('tour_started_at', '>', $expires_at)->delete();
        }
    }
}
