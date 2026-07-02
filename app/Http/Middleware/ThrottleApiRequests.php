<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\RateLimiter;

class ThrottleApiRequests
{
  public function handle(Request $request, Closure $next)
  {
    $maxAttempts = 40;
    $decaySeconds = 60;
    $key = $this->resolveKey($request);

    if (RateLimiter::tooManyAttempts($key, $maxAttempts)) {
      $seconds = RateLimiter::availableIn($key);

      return response()->json([
        'message' => 'Muitas requisições. Tente novamente em alguns instantes.',
      ], 429, [
        'Retry-After' => $seconds,
        'X-RateLimit-Limit' => $maxAttempts,
        'X-RateLimit-Remaining' => 0,
      ]);
    }

    RateLimiter::hit($key, $decaySeconds);

    $response = $next($request);

    if (method_exists($response, 'headers')) {
      $response->headers->set('X-RateLimit-Limit', $maxAttempts);
      $response->headers->set('X-RateLimit-Remaining', max(0, $maxAttempts - RateLimiter::attempts($key)));
    }

    return $response;
  }

  protected function resolveKey(Request $request): string
  {
    $userId = $request->user()?->id;

    return 'api:' . ($userId ?: $request->ip());
  }
}
