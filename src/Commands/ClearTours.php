<?php

namespace BadMushroom\Tourist\Commands;

use BadMushroom\Tourist\Models\ModelTour;
use Carbon\Carbon;
use Illuminate\Console\Command;

class ClearTours extends Command
{
    protected $signature = 'tourist:clear';
    protected $description = 'Clears all model tours tracked by the tourist package.';

    public function handle()
    {
        $days = config('tourist.expires_in');

        if (!empty($days) && $days > 0) {
            $expires_at = Carbon::now()->addDays($days);
            ModelTour::where('created_at', '>', $expires_at)->delete();
        }
    }
}
