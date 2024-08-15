<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class OnlyLocalhostMiddleware {

  /**
   * Handle an incoming request.
   *
   * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
   */
  public function handle(Request $request, Closure $next): Response {
    // the allowed ips
    $allowedIps = ['127.0.0.1', '::1', env('SERVER_HOST', 'localhost')];

    // check if the request ip is on the allowed ips
    if (!in_array($request->ip(), $allowedIps))
      // return with abort error with code 404
      return response()->abort(404);

    // goto the next
    return $next($request);
  }

}
