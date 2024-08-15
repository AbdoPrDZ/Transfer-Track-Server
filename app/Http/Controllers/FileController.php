<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Models\File;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class FileController extends Controller {

  public function __construct() {
    $this->middleware('auth:sanctum');
  }

  public function find(Request $request, File $file) {
    // check if file disk is api or public
    if ($file->disk == 'api' || $file->disk == 'public')
      return response()->download(
        Storage::disk($file->disk)->path($file->path),
        $file->name,
      );
    else // if not abort 401
      return abort(401);
  }

}
