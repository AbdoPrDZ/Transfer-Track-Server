<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Models\Notification;
use Illuminate\Http\Request;

class NotificationController extends Controller {

  public function __construct() {
    $this->middleware('auth:sanctum');
  }

  public function all(Request $request) {
    /**
     * @var User $user
     */
    $user = $request->user();

    return $this->dataCollectRequest($request, Notification::class, 'notifications', function ($query) use ($user) {
      $query->whereJsonContains('targets_ids', $user->id)
            ->whereJsonDoesntContain('hides_ids', $user->id);
    }, false, function (Notification $notification) use ($user) {
      $item            = (object) $notification->toArray();
      $item->is_seen   = $notification->isSeen($user->id);
      $item->is_hidden = $notification->isHidden($user->id);

      return $item;
    }, true, false);
  }

  public function countNews(Request $request) {
    /**
     * @var User $user the app user
     */
    $user = $request->user();

    $count = Notification::news($user->id)
                         ->whereJsonContains('targets_ids', $user->id)
                         ->whereJsonDoesntContain('hides_ids', $user->id)
                         ->count();

    return $this->apiSuccessResponse(__('notification.count_new-success'), [
      'count' => $count,
    ]);

  }

  public function find(Request $request, Notification $notification) {
    /**
     * @var User $user
     */
    $user = $request->user();

    $item            = (object) $notification->toArray();
    $item->is_seen   = $notification->isSeen($user->id);
    $item->is_hidden = $notification->isHidden($user->id);

    return $this->apiSuccessResponse(__('notification.find-success'), [
      'notification' => $item,
    ]);
  }

  public function see(Request $request, Notification $notification) {
    if (!$notification->seeIt($request->user()->id))
      return $this->apiErrorResponse(__('notification.see-error'));

    return $this->apiSuccessResponse(__('notification.see-success'));
  }

  public function hide(Request $request, Notification $notification) {
    if (!$notification->hideIt($request->user()->id))
      return $this->apiErrorResponse(__('notification.hide-error'));

    return $this->apiSuccessResponse(__('notification.hide-success'));
  }

}
