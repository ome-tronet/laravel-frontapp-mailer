<?php

namespace tronet\FrontappMailer\Api;

use Closure;
use Illuminate\Http\Client\Response;
use Illuminate\Support\Facades\Http;
use tronet\FrontappMailer\Exceptions\ApiLimitExceededException;

/**
 * Frontapp http client wrapper with rate limit observation
 */

class HttpClient
{
    public function __construct(
        protected string $baseUrl,
        protected string $apiToken,
    ) {}

    public function post(string $endpoint, array $body): Response
    {
        return $this->rateLimited(function() use ($endpoint, $body) {
            return Http::withToken($this->apiToken)
                ->post($this->baseUrl . '/' . $endpoint, $body)
                ->throw();
        });
    }

    protected function rateLimited(Closure $apiResponse): Response
    {
        if ($this->getRateLimitRemaining() <= 0 && $this->getRateLimitTimestampOfReset() > now()->timestamp){
            throw new ApiLimitExceededException(
                'Frontapp API limit exceeded until '
                . date('Y-m-d H:i:s', $this->getRateLimitTimestampOfReset())
            );
        }
        return $this->updateRateLimitFromResponse($apiResponse());
    }

    protected function updateRateLimitFromResponse(Response $response): Response
    {
        cache()->put('front_ratelimit_remaining', $response->header('x-ratelimit-remaining'));
        cache()->put('front_ratelimit_reset', $response->header('x-ratelimit-reset'));
        return $response;
    }

    protected function getRateLimitRemaining(): int
    {
        return cache()->get('front_ratelimit_remaining') ?? 1;
    }

    protected function getRateLimitTimestampOfReset(): ?float
    {
        return cache()->get('front_ratelimit_reset');
    }

}