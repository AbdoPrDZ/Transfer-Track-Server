<?php

namespace App\Events\User;

use App\Models\User;
use Illuminate\Broadcasting\Channel;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Broadcasting\PresenceChannel;
use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class UserUpdatedEvent implements ShouldBroadcast {

  use Dispatchable, InteractsWithSockets, SerializesModels;

  /**
   * Create a new event instance.
   *
   * @param User $user the user instance
   */
  public function __construct(private User $user) { }

  /**
  * The event's broadcast name.
  *
  * @return string
  */
  public function broadcastAs(): string {
    return 'UserUpdatedEvent';
  }

  /**
   * Get the channels the event should broadcast on.
   *
   * @return array<int, \Illuminate\Broadcasting\Channel>
   */
  public function broadcastOn(): array {
    return [
      // send in the user User.{this user id} channel
      new PrivateChannel("User.{$this->user->id}"),
      new PresenceChannel("User.{$this->user->id}"),
    ];
  }

  /**
   * The event's broadcast name.
   *
   * @return array
   */
  public function broadcastWith(): array {
    return [
      'user' => $this->user->toArray()
    ];
  }

}
