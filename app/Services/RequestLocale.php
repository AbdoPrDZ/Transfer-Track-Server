<?php

namespace App\Services;

use Illuminate\Http\Request;

class RequestLocale {

  public function __construct(private string $code) {}

  public function __toString() {
    return $this->code;
  }

  public static function fromString(string $code) {
    switch ($code) {
      case 'ar':
      case 'ar-DZ':
        return self::ar();
      case 'fr':
      case 'fr-FR':
        return self::fr();
      default:
        return self::en();
    }
  }

  public static function fromRequest(Request $request) {
    return self::fromString($request->server('HTTP_ACCEPT_LANGUAGE', 'en'));
  }

  public static function en() {
    return new RequestLocale('en');
  }

  public static function ar() {
    return new RequestLocale('ar');
  }

  public static function fr() {
    return new RequestLocale('fr');
  }

}
