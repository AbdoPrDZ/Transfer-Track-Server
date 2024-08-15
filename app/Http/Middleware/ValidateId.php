<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;
use Illuminate\Support\Facades\Schema;

// TODO: remove throws
class ValidateId {

  /**
   * Handle an incoming request.
   *
   * @param  Request  $request
   * @param  Closure $next
   * @param  string $model
   * @param  string $indexModel
   * @param  boolean $multiIds
   * @return Response
   */
  public function handle(Request $request,
                         Closure $next,
                         string $model,
                         string $indexName = 'id',
                         bool $multiIds = false): Response {
    // get if the multiIds is true or not
    $multiIds = $multiIds == 'true';
    // get the id from te request route parameters
    $id = $request->route()->parameter($indexName);

    // check if the id is exists
    if (!$id)
      // return abort error with code 500
      return response()->abort(500, "Undefined route parameter '$indexName'");

    // check if the model class is exists
    if (!class_exists($model))
      // return abort error with code 500
      return response()->abort(500, "Undefined model '$model'");

    // get the model table and check if have the index name
    if (!Schema::hasColumn((new $model)->getTable(), $indexName))
      // return abort error with code 500
      return response()->abort(500, "Undefined column '$indexName'");

    // check if is multi ids and the id have chat ','
    if ($multiIds && str_contains($id, ',')) {
      // split the id by char ','
      $ids = explode(',', $id);

      // create the empty array for the items
      $items = [];

      // looping into the ids
      foreach($ids as $id) {
        // get the item and check if is exists
        if (!$item = app($model)->where([$indexName => $id])->first())
          // return abort error with code 404
          // return abort(404, "Undefined item $id");
        throw new \Exception("Undefined item $id");

        // add the item to the items array
        $items[] = $item;
      }

      // set the items with the route parameters
      $request->route()->setParameter($indexName, $items);
    } else {
      // get the item and check if is exists
      if (!$item = app($model)->where([$indexName => $id])->first())
        // return abort error with code 404
        // return abort(404, "Undefined item $id");
        throw new \Exception("Undefined item $id");

      // set the item with the route parameters
      $request->route()->setParameter($indexName, $item);
    }

    // goto the next
    return $next($request);
  }

}
