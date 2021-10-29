<?php

namespace BadMushroom\LaravelTourist\Traits;

use BadMushroom\LaravelTourist\Models\TourVisit;

trait Tourable
{
    // -- Relationships

    /**
     * TourVists polymorphic releationship.
     *
     * @return morphMany
     */
    public function visits()
    {
        return $this->morphMany(TourVisit::class, 'tourable');
    }
}
