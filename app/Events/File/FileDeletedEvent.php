<?php

namespace App\Events\File;

use App\Models\File;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Storage;

class FileDeletedEvent {

  use Dispatchable, InteractsWithSockets, SerializesModels;

  /**
   * Create a new event instance.
   *
   * @var File $file the file instance
   */
  public function __construct(File $file) {
    // get the file and delete it from the storage
    Storage::disk($file->disk)->delete($file->path);
  }

}
