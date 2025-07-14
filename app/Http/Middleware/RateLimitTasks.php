<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\RateLimiter;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response;

class RateLimitTasks
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        $key = $this->resolveRequestSignature($request);
        $maxAttempts = $this->getMaxAttempts($request);
        $decayMinutes = $this->getDecayMinutes($request);

        if (RateLimiter::tooManyAttempts($key, $maxAttempts)) {
            return $this->buildResponse($key, $maxAttempts);
        }

        RateLimiter::hit($key, $decayMinutes * 60);

        $response = $next($request);

        return $this->addHeaders(
            $response,
            $maxAttempts,
            RateLimiter::retriesLeft($key, $maxAttempts),
            RateLimiter::availableIn($key)
        );
    }

    /**
     * Resolve request signature.
     */
    protected function resolveRequestSignature(Request $request): string
    {
        if ($user = Auth::user()) {
            return 'task-api:' . $user->id;
        }

        return 'task-api:' . $request->ip();
    }

    /**
     * Get the maximum number of attempts allowed.
     */
    protected function getMaxAttempts(Request $request): int
    {
        // Different limits for different operations
        if ($request->isMethod('POST')) {
            return 30; // 30 creates per minute
        }

        if ($request->isMethod('PUT') || $request->isMethod('PATCH')) {
            return 50; // 50 updates per minute
        }

        if ($request->isMethod('DELETE')) {
            return 20; // 20 deletes per minute
        }

        return 100; // 100 reads per minute
    }

    /**
     * Get the decay time in minutes.
     */
    protected function getDecayMinutes(Request $request): int
    {
        return 1; // 1 minute window
    }

    /**
     * Build the rate limit response.
     */
    protected function buildResponse(string $key, int $maxAttempts): Response
    {
        $retryAfter = RateLimiter::availableIn($key);

        return response()->json([
            'success' => false,
            'message' => 'Too many requests. Please try again later.',
            'retry_after' => $retryAfter,
            'error' => 'Rate limit exceeded'
        ], 429)->withHeaders([
            'X-RateLimit-Limit' => $maxAttempts,
            'X-RateLimit-Remaining' => 0,
            'X-RateLimit-Reset' => now()->addSeconds($retryAfter)->timestamp,
            'Retry-After' => $retryAfter,
        ]);
    }

    /**
     * Add rate limit headers to the response.
     */
    protected function addHeaders(Response $response, int $maxAttempts, int $remainingAttempts, int $retryAfter): Response
    {
        $response->headers->add([
            'X-RateLimit-Limit' => $maxAttempts,
            'X-RateLimit-Remaining' => $remainingAttempts,
            'X-RateLimit-Reset' => now()->addSeconds($retryAfter)->timestamp,
        ]);

        return $response;
    }
}
