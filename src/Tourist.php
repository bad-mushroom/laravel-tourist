<?php

namespace BadMushroom\Tourist;

use BadMushroom\Tourist\Models\TourSession;
use BadMushroom\Tourist\Models\TourVisit;
use BadMushroom\Tourist\Parsers\UserAgentParserInterface;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Http\Request;

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
     * Tourist
     *
     * @param Request $request
     * @param array $config
     */
    public function __construct(Request $request, $config)
    {
        $this->request = $request;
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
     * @return void
     */
    public function visit(Model $model, string $passport = null): void
    {
        if (!$passport) {
            $passport = $this->fetchPassport();
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
    private function fetchPassport(): string
    {
        return session()->get('tourist_passport');
    }
}
