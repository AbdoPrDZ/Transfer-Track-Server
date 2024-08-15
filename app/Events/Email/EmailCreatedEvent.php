<?php

namespace App\Events\Email;

use App\Models\Email;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;
use Mail;

class EmailCreatedEvent {

  use Dispatchable, InteractsWithSockets, SerializesModels;

  /**
   * Create a new event instance.
   */
  public function __construct(public Email $email) {
    foreach ($email->targets as $target)
      try {
        Mail::raw($email->title, function ($message) use ($email, $target) {
          $message->to($target);
          $message->subject($email->title);
          $message->html($email->renderTemplate());
        });
      } catch (\Throwable $th) {
        \Log::error('EmailCreatedEvent-Send-Error', [
          $th->getMessage(),
          $email,
        ]);
      }
  }

}
