<?php

namespace App\Http\Controllers;

use App\Models\File;
use App\Services\RequestLocale;
use Carbon\Carbon;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Foundation\Validation\ValidatesRequests;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller as BaseController;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\Storage;
use Validator;

class Controller extends BaseController {

  use AuthorizesRequests, ValidatesRequests;

  /**
   * Create api response
   *
   * @param boolean $success
   * @param string $message
   * @param array $data
   * @param int $code
   * @return \Illuminate\Http\JsonResponse
   */
  public static function apiResponse(bool $success, string $message, array $data = [], int $code = 200) {
    return response()->json([
      'success' => $success,
      'message' => $message,
      ...$data,
    ], $code);
  }

  /**
   * Create success api response
   *
   * @param string $message
   * @param array $data
   * @param int $code
   * @return \Illuminate\Http\JsonResponse
   */
  public static function apiSuccessResponse(string $message, array $data = [], int $code = 200) {
    return self::apiResponse(true, $message, $data, $code);
  }

  /**
   * Create error api response
   *
   * @param string $message
   * @param array $data
   * @param int $code
   * @return \Illuminate\Http\JsonResponse
   */
  public static function apiErrorResponse(string $message, array $data = [], int $code = 200) {
    return self::apiResponse(false, $message, $data, $code);
  }

  /**
   * Create invalid values error response
   *
   * @param array $errors
   * @param array $data
   * @param int $code
   * @return \Illuminate\Http\JsonResponse
   */
  public static function apiInvalidValuesResponse(array $errors = [], ?string $message = null, array $data = [], int $code = 200) {
    foreach ($errors as $key => $value)
      if (is_array($value))
        $errors[$key] = isset($value[0]) ? $value[0] : '';

    if (!$message && count($errors) == 1)
      $message = array_values($errors)[0];

    return self::apiErrorResponse($message ?? __('controller.api_response-invalid_values'), [
      ...$data,
      "errors" => $errors,
    ], $code);
  }

  /**
   * Create single error api response
   *
   * @param string $field
   * @param string $message
   * @param array $data
   * @param int $code
   * @return \Illuminate\Http\JsonResponse
   */
  public static function apiSingleErrorResponse(string $field, string $message, array $data = [], int $code = 200) {
    return self::apiInvalidValuesResponse([ $field => $message ], $message, $data, $code);
  }

  /**
   * try to decode array
   *
   * @param string|array|null $text
   * @return array|string
   */
  public function tryDecodeArray(string|array|null $text) {
    try {
      return (array) json_decode($text);
    } catch (\Throwable $th) {
      return $text;
    }
  }

  /**
   * Convert a list map to map
   *
   * @param array $listMap
   * @param string $keyField
   * @return array
   */
  public function listMap2Map(array $listMap, $keyField = 'name') {
    $map = [];

    foreach ($listMap as $item)
      $map[$item->{$keyField}] = $item;

    return $map;
  }

  /**
   * moveFile - move the file to storage and create a file record
   *
   * @param Request $request
   * @param string $field request field
   * @param string $disk storage disk
   * @param string $dirPath storage directory path
   * @param string $name file name
   * @return File the file record
   */
  public function moveFile(Request $request, string $field, string $disk, string $dirPath, string $name) {
    $pathArray = explode('/', $dirPath); // split the path
    $pathArray = array_filter($pathArray); // remove empty strings

    $nDirPath = ""; // new dir path
    foreach($pathArray as $item) {
      $nDirPath = "$nDirPath/$item";
      if(!Storage::disk($disk)->exists($nDirPath)) // check if directory is exists
        Storage::disk($disk)->makeDirectory($nDirPath); // make it it
    }

    // get the request file
    $reqFile = $request->file($field);

    // check if the file is exists
    if (!$reqFile)
      throw new \Exception("Undefined file with name $field, on request $request");
    // check the file type
    elseif (get_class($reqFile) != \Illuminate\Http\UploadedFile::class)
      throw new \Exception("Unexpected file type, the file type must be \\Illuminate\\Http\\UploadedFile but " . get_class($reqFile) . " given");

    // move the uploaded image to dir
    $reqFile->move(Storage::disk($disk)->path($nDirPath), $name);

    /**
     * @var File create file record
     */
    $file = File::create([
      'name' => $name,
      'disk' => $disk,
      'path' => "$nDirPath/$name",
    ]);

    return $file;
  }

  /**
   * Data collect request
   *
   * @param Request $request the request
   * @param string $modelClass the model class
   * @param string $responseField the response field
   * @param ?\Closure $where custom where function
   * @param bool $searchable enable|disable search
   * @param ?\Closure $itemsMapper custom items mapper function
   * @return \Illuminate\Http\JsonResponse
   */
  public function dataCollectRequest(Request $request, string $modelClass, string $responseField,
                                     ?\Closure $where = null, bool $searchable = true, ?\Closure $itemsMapper = null,
                                     bool $withoutArchived = true, bool $withJoins = true) {
    $model = app($modelClass);
    $tableName = $model->getTable();

    $sortByColumns = [];
    if (method_exists($model, 'getSortByColumns'))
     $sortByColumns = $model->getSortByColumns();
    else
      foreach (Schema::getColumnListing($tableName) as $col)
        $sortByColumns[$col] = "$tableName.$col";

    $modelSearchFields = method_exists($model, 'getSearchFields') ? $model->getSearchFields()->getNames() : [];

    $itemsCount = $model::count();

    $request->merge(['page'         => $request->page ?? 1]);
    $request->merge(['itemsPerPage' => $request->itemsPerPage ?? 10]);
    $request->merge(['sortBy'       => $request->sortBy ?? 'created_at']);
    $request->merge(['sortType'     => $request->sortType ?? 'asc']);
    $request->merge(['dateCompare'  => $request->dateCompare ?? 'at']);

    $rules = [
      'page'         => "required|integer|min:1",
      'itemsPerPage' => 'required|integer|min:10',
      'sortBy'       => 'required|in:' . implode(",", array_keys($sortByColumns)),
      'sortType'     => 'required|in:asc,desc',
      'date'         => $request->date ? 'required|integer' : '',
      'dateCompare'  => $request->date ? 'required|in:at,before,after' : '',
    ];

    $fields = [];
    if ($searchable)
      if ($request->searchFields) {
        if ($request->searchFields == '*') {
          $rules['searchText'] = 'required|string';
          $fields = null;
        } else {
          $rules['fields'] = 'required|array';
          try {
            $fields = explode(',', $request->searchFields);

            foreach ($fields as $field)
              $rules[$field] = 'required|string';

          } catch (\Throwable $th) {}
        }

        $request->merge(['fields' => $fields]);
      }

    $validator = Validator::make($request->all(), $rules);

    if ($validator->fails())
      return $this->apiInvalidValuesResponse($validator->errors()->toArray(), null, [
        'all' => $request->all(),
      ]);

    if ($fields) foreach ($fields as $field)
      if (!in_array($field, $modelSearchFields))
        return $this->apiSingleErrorResponse($field, __('controller.data_collection-error-field', [
          'name' => $field,
        ]));

    if ($withoutArchived && method_exists($model->query(), 'withoutArchived'))
      $model = $model->query()->withoutArchived();

    /**
     * @var \Illuminate\Database\Query\Builder
     */
    $query = ($searchable && ($request->searchText || $fields) ? $model->search($request->searchText, $request->all(), $fields, $withJoins) : ($withJoins ? $model->withJoins() : $model))
                ->orderBy($sortByColumns[$request->sortBy], $request->sortType);

    if ($request->date)
      $query = $query->whereDate(
        'created_at',
        ['at' => '=', 'before' => '<', 'after' => '>'][$request->dateCompare],
        Carbon::createFromTimestamp(intVal($request->date))->startOfDay()
      );

    if ($where) $query = $query->where($where);

    $totalCount = $query->count();

    $skip = $request->itemsPerPage * ($request->page - 1);

    $take = min($request->itemsPerPage, $totalCount);

    $items = $query->take($take)
                   ->skip($skip < $itemsCount ? $skip : 0)
                   ->select("$tableName.*")
                   ->get()
                   ->map($itemsMapper ? $itemsMapper : function ($item) {
                     return $item->toArray();
                   });

    return $this->apiSuccessResponse(__('controller.data_collection-success'), [
      'total_count' => $totalCount,
      'items_count' => count($items),
      'page' => $skip < $itemsCount ? $request->page * 1 : 1,
      'pages_count' => ceil($totalCount / max($request->itemsPerPage, 1)),
      $responseField => $items,
    ]);
  }

  /**
   * Detect the request locale
   *
   * @return RequestLocale
   */
  public static function detectRequestLocale(Request $request) {
    $locale = RequestLocale::fromRequest($request);
    app()->setLocale("$locale");
    return $locale;
  }

  public function wantsJson() {
    $acceptable = request(null)->getAcceptableContentTypes();

    return isset($acceptable[0]) && $acceptable[0] == 'application/json';
  }

}
