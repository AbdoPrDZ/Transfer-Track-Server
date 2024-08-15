<?php

use App\Http\Controllers\FileController;
use App\Http\Controllers\NotificationController;
use App\Http\Controllers\UserController;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "api" middleware group. Make something great!
|
*/
Route::get('/check', function() {
  return response()->json([
    'success' => true,
    'message' => 'OK',
  ]);
});

Route::get('/generate-token', function() {
  $user = \App\Models\User::first();
  $token = $user->createToken('echo_token', ["echo"])->plainTextToken;
  return response()->json([
    'success' => true,
    'message' => 'OK',
    'data' => [
      'user' => $user,
      'token' => $token,
    ],
  ]);
});

Route::group(['prefix' => '/auth'], function () {
  Route::post('/register', UserController::class . '@register')
       ->name('user.auth.register');

  Route::get('/email_resend', UserController::class . '@emailResend')
       ->middleware(['auth:sanctum', 'ability:email-verify'])
       ->name('user.auth.email_resend');
  Route::post('/email_verify', UserController::class . '@emailVerify')
       ->middleware(['auth:sanctum', 'ability:email-verify'])
       ->name('user.auth.email_verify');

  Route::post('/login', UserController::class . '@login')
       ->name('user.auth.login');

  Route::get('/user', UserController::class . '@getUser')
       ->name('user.auth.get_user');
  Route::post('/user', UserController::class . '@editAccount')
       ->name('user.auth.edit_user');
  Route::put('/user/app_status/{status}', UserController::class . '@setAppStatus')
       ->name('user.auth.set_app_status');

  Route::post('/password_forget', UserController::class . '@passwordForget')
       ->name('user.auth.password_forget');
  Route::post('/password_reset', UserController::class . '@passwordReset')
       ->middleware(['auth:sanctum', 'ability:password-reset'])
       ->name('user.auth.password_reset');

  Route::post('/change_email', UserController::class . '@changeEmail')
       ->name('user.auth.change_email');
  Route::post('/change_password', UserController::class . '@changePassword')
       ->name('user.auth.change_password');

  Route::get('/logout', UserController::class . '@logout')
       ->name('user.auth.logout');
});

Route::group(['prefix' => 'notification'], function() {
  Route::get('/', NotificationController::class . '@all')
       ->name('user.notification.all');
  Route::get('/count', NotificationController::class . '@countNews')
       ->name('user.notification.count_news');
  Route::get('/{id}', NotificationController::class . '@find')
       ->middleware('valid.id:' . Notification::class)
       ->name('user.notification.find');
  Route::put('/{id}/see', NotificationController::class . '@see')
       ->middleware('valid.id:' . Notification::class)
       ->name('user.notification.see');
  Route::put('/{id}/hide', NotificationController::class . '@hide')
       ->middleware('valid.id:' . Notification::class)
       ->name('user.notification.hide');
});

Route::get('/file/{name}', FileController::class.'@find')
     ->middleware('valid.id:' . File::class . ',name');
