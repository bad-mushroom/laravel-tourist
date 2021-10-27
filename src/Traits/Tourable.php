<?php

namespace BadMushroom\Tourist\Traits;

use BadMushroom\Tourist\Models\TourVisit;

trait Tourable
{
    // -- Relationships

    /**
     * Morph Relationship
     */
    public function tourable()
    {
        return $this->morphTo();
    }

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
