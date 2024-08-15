<?php

use Illuminate\Support\Facades\Broadcast;

/*
|--------------------------------------------------------------------------
| Broadcast Channels
|--------------------------------------------------------------------------
|
| Here you may register all of the event broadcasting channels that your
| application supports. The given channel authorization callbacks are
| used to check if an authenticated user can listen to the channel.
|
*/

Route::post('broadcasting/client_connect', function(Request $request) {
  $success = false;

  /**
   * @var \App\Models\User $user
   */
  $user = $request->user();

  if ($user) {
    $user->status = 'online';
    $user->save();

    $success = true;
  }

  return response()->json([
    'success' => $success,
  ]);
})->middleware(['auth:sanctum', 'only.localhost'])
  ->name('broadcasting.client_connect');

Route::post('broadcasting/client_disconnect', function(Request $request) {
  $success = false;

  /**
   * @var \App\Models\User $user
   */
  $user = $request->user();

  if ($user) {
    $user->status = 'offline';
    $user->save();

    $success = true;
  }

  return response()->json([
    'success' => $success,
  ]);
})->middleware(['auth:sanctum', 'only.localhost'])
  ->name('broadcasting.client_disconnect');

Broadcast::channel('User.{id}.Notification', function ($user, $id) {
  return $user->id == $id;
});

Broadcast::channel('User.{id}', function($user, $id) {
  return $user->id == $id;
});

Broadcast::channel('Chat.{id}', function($user, $id) {
  return true;
});
