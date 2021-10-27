<?php

namespace BadMushroom\Tourist\Parsers;

use Illuminate\Http\Request;
use UAParser\Parser;

class UserAgentParser implements UserAgentParserInterface
{
    protected $request;

    protected $parser;

    public function __construct(Request $request)
    {
        $this->request = $request;
        $this->parser = $this->bootParser();
    }

    public function browser(): ?string
    {
        return $this->parser->ua->family ?? '';
    }

    public function platform(): ?string
    {
        return $this->parser->os->family ?? '';
    }

    public function device(): ?string
    {
        return $this->parser->device->family ?? '';
    }

    protected function bootParser()
    {
        $parser = Parser::create();

        $result = $parser->parse($this->request->userAgent());

        return $result;
    }
}
