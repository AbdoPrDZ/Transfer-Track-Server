<?php

namespace App\Events\Notification;

use App\Models\Notification;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class NotificationCreatedEvent implements ShouldBroadcast {

  use Dispatchable, InteractsWithSockets, SerializesModels;

  /**
   * @var Notification $notification the notification instance
   */
  private Notification $notification;

  /**
   * Create a new event instance.
   *
   * @param Notification $notification the notification instance
   */
  public function __construct(Notification $notification) {
    // set the notification instance
    $this->notification = $notification;
  }

  /**
   * The event's broadcast name.
   *
   * @return string
   */
  public function broadcastAs(): string {
    return 'NotificationEvent';
  }

  /**
   * Get the channels the event should broadcast on.
   *
   * @return array<int, \Illuminate\Broadcasting\Channel>
   */
  public function broadcastOn(): array {
    // create empty array for channels
    $channels = [];

    // check if the app users tokens is more then 0
    if (count($this->notification->getTokens()) > 0)
      // add the push_notification channel to channels array
      $channels[] = new PrivateChannel('push_notifications');

    // lopping into notification targets_ids
    foreach ($this->notification->targets_ids as $id)
      // add the the app and target id channel
      $channels[] = new PrivateChannel(
        "User.$id.Notification"
      );

    // return the channels array
    return $channels;
  }

  /**
   * The event's broadcast name.
   *
   * @return array
   */
  public function broadcastWith(): array {
    $notification = (object) $this->notification->toArray();

    // create a notification cast
    $cast = [
      'tokens'       => $this->notification->getTokens(),
      'notification' => [
        'title' => $notification->title_en,
        'body'  => $notification->body_en,
      ],
      'data' => (array) $notification,
      // TODO: search more about notification parameters
      'android'      => [
        'notification' => [
          'priority' => "high",
          'icon'     => 'stock_ticker_update',
          'sound'    => "default",
          'color'    => '#7e55c3',
        ],
      ],
    ];

    // check if the notification have image
    if ($this->notification->image)
      // add the image to the notification cast
      $cast['notification']['image'] = $this->notification->image;

      // looping into the notification data
      foreach($cast['data'] as $key => $val)
        // check if the val is not a string
        if (!is_string($val))
          // convert the val to string by encode it to json string
          $cast['data'][$key] = json_encode($val);

    // return the notification data
    return $cast;
  }

}
