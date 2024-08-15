<?php

namespace App\Http\Middleware;

use App\Models\LicenseVerifyResponse;
use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class ValidateUserLicense {

  /**
   * Handle an incoming request.
   *
   * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
   */
  public function handle(Request $request, Closure $next, ?string $guard = null): Response {
    $user = $request->user($guard);

    if (!method_exists($user, 'verifyLicense'))
      throw new \Exception("Method 'verifyLicense' is not defined on user object");

    /**
     * @var LicenseVerifyResponse $licenseResponse
     */
    $licenseResponse = $user->verifyLicense($request);

    if (!$licenseResponse->success)
      return $licenseResponse->apiResponse();

    return $next($request);
  }

}
