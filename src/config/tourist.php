<?php

return [

    /*
    |--------------------------------------------------------------------------
    | Tourism Record Expiration
    |--------------------------------------------------------------------------
    |
    | Tracking model views over long periods of time can create a large amount
    | of data that may not be neccesary for your needs. Set the number of
    | each record is valid for. Running the tourism:clear command will
    | remove any expired records. Setting this value to null will
    | keep the data indefinitly.
    |
    */

    'expires_in' => env('TOURISM_EXPIRE_DAYS', 180),

    /*
    |--------------------------------------------------------------------------
    | User Agent Parser
    |--------------------------------------------------------------------------
    |
    | Tourist includes a built in browser User Agent parsing class but you're
    | free to add your own if you need a more customizable approach.
    | Your class will need to implement a single interface called
    | BadMushroom\Tourist\Parsers\UserAgentParserInterface.
    |
    */

    'parser' => BadMushroom\Tourist\Parsers\UserAgentParser::class,

    /*
    |--------------------------------------------------------------------------
    | Ignore Bots
    |--------------------------------------------------------------------------
    |
    | Bots are search engine indexers, robots, or crawlers that may visit your
    | web page. Usually these are harmeless but can cause a lot of unwanted
    | collected data in the logs. Bots are ignored by default but you can
    | track them if you wish.
    |
    */

    'ignore_bots' => true,

    /*
    |--------------------------------------------------------------------------
    | Bot Terms
    |--------------------------------------------------------------------------
    |
    | There isn't always clear way to determine what is a bot and what is a
    | legit page view by a human being. Most bots will identify themselves
    | with a term like "Crawler" or "Search" or even more specifc like
    | "GoogleBot". Tourist will attempt to determine if a visit is a
    | bot by looking at the key words defined below.
    |
    */

    'bot_terms' => ['crawl', '*bot', 'slurp', 'search', '*spider', 'ia_archiver',],
];
