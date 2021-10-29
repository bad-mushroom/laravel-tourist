<?php

namespace BadMushroom\LaravelTourist\Parsers;

interface UserAgentParserInterface
{
    public function browser(): ?string;

    public function platform(): ?string;

    public function device(): ?string;
}
