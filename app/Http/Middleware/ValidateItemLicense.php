<?php

namespace App\Http\Middleware;

use App\Models\LicenseVerifyResponse;
use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class ValidateItemLicense {

  /**
   * Handle an incoming request.
   *
   * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
   */
  public function handle(Request $request, Closure $next, ?string $itemName): Response {
    if (is_null($itemName))
      throw new \Exception("Item name is required");

    $item = $request->route()->getParameter($itemName);

    if (is_null($item))
      throw new \Exception("Undefined item '$itemName' on route parameters");

    if (!method_exists($item, 'verifyLicense'))
      throw new \Exception("Method 'verifyLicense' is not defined on item '$itemName'");

    /**
     * @var LicenseVerifyResponse $licenseResponse
     */
    $licenseResponse = $item->verifyLicense($request);

    if (!$licenseResponse->success)
      return $licenseResponse->apiResponse();

    return $next($request);
  }

}
