<?php

namespace BadMushroom\LaravelTourist;

use BadMushroom\LaravelTourist\Models\TourSession;
use BadMushroom\LaravelTourist\Models\TourVisit;
use BadMushroom\LaravelTourist\Parsers\UserAgentParserInterface;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class Tourist
{
    /**
     * The request.
     *
     * @var Request $request
     */
    protected $request;

    /**
     * The authenticated user/entitiy making the request.
     *
     * @var mixed $traveler
     */
    protected $traveler;

    /**
     * Configuration values.
     *
     * @var array
     */
    protected $config;

    /**
     * Tourist
     *
     * @param Request $request
     * @param array $config
     */
    public function __construct(Request $request, $config)
    {
        $this->request = $request;
        $this->config = $config;
        $this->setTraveler($request->user());
        $this->setParser(new $config['parser']($request));
    }

    /**
     * Set parser.
     *
     * @param UserAgentInterface $parser
     * @return self
     */
    public function setParser(UserAgentParserInterface $parser)
    {
        $this->parser = $parser;

        return $this;
    }

    /**
     * Set traveler.
     *
     * @param Model|null $traveler
     * @return self
     */
    public function setTraveler(?Model $traveler)
    {
        $this->traveler = $traveler;

        return $this;
    }

    /**
     * Get traveler.
     *
     * @return Model|null
     */
    public function getTraveler(): ?Model
    {
        return $this->traveler;
    }

    /**
     * The browser family (e.g. Chrome, Safari).
     *
     * @return string|null
     */
    protected function browser(): string
    {
        return $this->parser->browser();
    }

    /**
     * The platform family (e.g. Mac, Linux).
     *
     * @return string|null
     */
    protected function platform(): string
    {
        return $this->parser->platform();
    }

    /**
     * The device family (e.g. Mac, ?).
     *
     * @return string|null
     */
    protected function device(): string
    {
        return $this->parser->device();
    }

    /**
     * The IP address
     *
     * @return string|null
     */
    protected function ip(): ?string
    {
        return $this->request->ip();
    }

    /**
     * The origin of the request (referer)
     *
     * @return string|null
     */
    protected function referer(): ?string
    {
        if (!empty($_SERVER['HTTP_REFERER'])) {
            return $_SERVER['HTTP_REFERER'];
        }

        return $this->utm('source');
    }

    /**
     * The brwoser's User Agent
     *
     * @return string|null
     */
    protected function userAgent(): string
    {
        return $this->request->userAgent() ?? '';
    }

    /**
     * UTM paramaters fromthe request.
     *
     * @param string $metric UTM paramater
     * @return string|null
     */
    protected function utm(string $metric): ?string
    {
        return $this->request->has('utm_' . $metric)
            ? $this->request->get('utm_' . $metric)
            : null;
    }

    /**
     * Issue passport.
     *
     * Attempts to create a fingerprint that is unique to the session
     * to void duplicate records.
     *
     * @return string
     */
    public function issuePassport(): string
    {
        return sha1($this->userAgent() . $this->ip() . optional($this->getTraveler())->getKey());
    }

    /**
     * Create tour_session record.
     *
     * @param string $passport
     * @return TourSession
     */
    public function startTour(string $passport)
    {
        if ($this->config['ignore_bots'] === true && $this->isBot() === true) {
            return;
        }

        if (TourSession::where('passport', $passport)->exists()) return;

        return TourSession::create([
            'passport'        => $passport,
            'user_agent'      => $this->userAgent(),
            'device'          => $this->device(),
            'browser'         => $this->browser(),
            'platform'        => $this->platform(),
            'referrer'        => $this->referer(),
            'is_bot'          => '',
            'utm_source'      => $this->utm('source'),
            'utm_medium'      => $this->utm('medium'),
            'utm_campaign'    => $this->utm('campaign'),
            'utm_term'        => $this->utm('term'),
            'utm_content'     => $this->utm('content'),
            'tour_started_at' => Carbon::now(),
        ]);
    }

    /**
     * Log a visit on the traveler's tour.
     *
     * @param Model $model
     * @param string|null $passport
     * @throws \Exception
     * @return void
     */
    public function visit(Model $model, string $passport = null): void
    {
        if ($this->config['ignore_bots'] === true && $this->isBot() === true) {
            return;
        }

        $passport = !empty($passport) ? $passport : $this->fetchPassport();

        if (empty($passport)) {
            Log::error('Unable to log visit due to missing passport value.');
            throw new \Exception('Passport must not be null. Make sure to enable Tourism middleware');
        }

        TourVisit::create([
            'passport'      => $passport,
            'tourable_type' => get_class($model),
            'tourable_id'   => $model->id,
            'visited_at'    => Carbon::now(),
        ]);
    }

    /**
     * Fetch passport from the session.
     *
     * @return string
     */
    private function fetchPassport(): ?string
    {
        return session()->get('tourist_passport');
    }

    /**
     * Check if "bot term" is in UserAgent string.
     *
     * @return bool
     */
    private function isBot(): bool
    {
        foreach ($this->config['bot_terms'] as $term) {
            if (strpos($this->userAgent(), $term) !== false) {
                return true;
            }
        }

        return false;
    }
}
