<?php

namespace App\Http\Controllers;

use App\Events\User\UserUpdatedEvent;
use App\Http\Controllers\Controller;
use App\Models\Email;
use App\Models\User;
use App\Models\License;
use App\Models\Setting;
use App\Models\Template;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;

class UserController extends Controller {

  public function __construct() {
    $this->middleware('auth:sanctum', ['except' => [
      'register',
      'login',
      'passwordForget',
    ]]);
  }

  public function register(Request $request) {
    $validator = Validator::make($request->all(), [
      'username'      => 'required|string|between:5,30|unique:users,username',
      'email'         => 'required|email|unique:users,email',
      'phone_country' => 'required|string|exists:phone_countries,code',
      'phone'         => 'required|numeric',
      'password'      => 'required|string|min:6',
    ]);

    if ($validator->fails())
      return $this->apiInvalidValuesResponse($validator->errors()->toArray());

    $phone = User::wherePhoneCountryId($request->phone_country)
                 ->wherePhone($request->phone)
                 ->first();

    if (!is_null($phone)) {
      $errors['phone'] = __('user.register-error-phone_used');

      return $this->apiInvalidValuesResponse($errors, __('user.register-error-already_registered'));
    }

    $license = License::create([
      'name' => 'user',
      'status' => 'checking',
      'statuses' => ['active', 'blocked', 'checking'],
    ]);

    $user = User::create([
      'username'         => $request->username,
      'email'            => $request->email,
      'phone_country_id' => $request->phone_country,
      'phone'            => $request->phone,
      'password'         => bcrypt($request->password),
      'license_id'       => $license->id,
    ]);

    $verifyCode = random_int(100000, 999999);

    $token = $user->createToken('email_verify', ['email-verify'], [
      'function' => 'register',
      'code' => $verifyCode,
      'send_count' => 1,
    ]);

    $email = Email::create([
      'name'        => 'user_email_verification',
      'title'       => 'Email Verification',
      'template_id' => Template::getEmailVerificationTemplate('register')->name,
      'data'        => [
        'username' => $user->username,
        'email'    => $user->email,
        'code'     => $verifyCode
      ],
      'targets'     => [$user->email],
    ]);

    return $this->apiSuccessResponse(__('user.register-success'), [
      'token'           => $token->plainTextToken,
      'date'            => $email->created_at,
      'expire_duration' => Setting::getEmailResendDuration(),
    ]);
  }

  public function emailResend(Request $request) {
    /**
     * @var User $user
     */
    $user = $request->user();

    $token = $user->tokens->last();

    if (now()->diffInSeconds($token->created_at) < Setting::getEmailResendDuration())
      return $this->apiErrorResponse(__('user.email_resend-error-time_expire'));

    $tokenData = $token->data;
    $sCount = $tokenData['send_count'];

    $token->delete();

    if ($sCount >= 5) return $this->apiErrorResponse(__('user.email_resend-error-many_resend'));

    $verifyCode = random_int(100000, 999999);
    $tokenData['code'] = $verifyCode;
    $tokenData['send_count'] = $sCount + 1;
    $token = $user->createToken('email_verify', ['email-verify'], $tokenData);

    $email = Email::create([
      'name' => 'user_email_verification',
      'title' => 'Email Verification',
      'template_id' => Template::getEmailVerificationTemplate([
        'register' => 'register',
        'change_phone' => 'change_phone',
        'change_email/verify_old' => 'change_email_old',
        'change_email/verify_new' => 'change_email_new',
        'change_password' => 'change_password',
        'password_forget' => 'password_forget',
      ][$tokenData['function']])->name,
      'data' => [
        'username' => $user->username,
        'email' => $user->email,
        'code' => $verifyCode,
      ],
      'targets' => [$user->email],
    ]);

    return $this->apiSuccessResponse(__('user.email_resend-success'), [
      'token' => $token->plainTextToken,
      'date' => $email->created_at,
      'expire_duration' => Setting::getEmailResendDuration(),
    ]);
  }

  public function emailVerify(Request $request) {
    $validator = Validator::make($request->all(), [
      'code' => 'required|string|size:6',
    ]);

    if ($validator->fails())
      return $this->apiInvalidValuesResponse($validator->errors()->toArray());

    /**
     * @var User $user
     */
    $user = $request->user();

    $license = $user->license;

    $token = $user->tokens->last();
    $tokenData = $token->data;

    if ($tokenData['code'] != $request->code)
      return $this->apiSingleErrorResponse('code', __('user.email_verify-error-invalid_code'));

    $token->delete();

    $user->email_verified_at = now();
    $user->save();

    if ($tokenData['function'] == 'register') {
      $license->status = 'active';
      $license->checked_at = now();
      $license->save();

      $token = $user->createToken('access_token')->plainTextToken;

      return $this->apiSuccessResponse(__('user.email_verify-success-register'), [
        'token' => $token
      ]);
    } elseif ($tokenData['function'] == 'password_forget') {
      $token = $user->createToken('password_reset', ['password-reset']);

      return $this->apiSuccessResponse(__('user.email_verify-success-password_forget'), [
        'token' => $token->plainTextToken,
      ]);
    } elseif ($tokenData['function'] == 'change_phone') {
        $phone_country_id = $tokenData['new_phone_country_id'];
        $phone = $tokenData['new_phone'];

        $user->phone_country_id = $phone_country_id;
        $user->phone = $phone;
        $user->save();

        $license = $user->license;
        $license->status = 'active';
        $license->checked_at = now();
        $license->save();

        return $this->apiSuccessResponse(__('user.phone_verify-success-change_phone'));
    } elseif ($tokenData['function'] == 'change_email/verify_old') {
      $verifyCode = random_int(100000, 999999);

      $token = $user->createToken('new_email_verify', ['email-verify'], [
        'function' => 'change_email/verify_new',
        'code' => $verifyCode,
        'send_count' => 1,
        'new_email' => $tokenData['new_email'],
      ]);

      $email = Email::create([
        'name' => 'user_email_verification',
        'title' => 'Email Verification',
        'template_id' => Template::getEmailVerificationTemplate('change_email_new')->name,
        'data' => [
          'username' => $user->username,
          'email'    => $tokenData['new_email'],
          'code'     => $verifyCode
        ],
        'targets' => [$tokenData['new_email']],
      ]);

      return $this->apiSuccessResponse(__('user.email_verify-success-change_email-old'), [
        'token' => $token->plainTextToken,
        'date' => $email->created_at,
        'expire_duration' => Setting::getEmailResendDuration(),
      ]);
    } elseif ($tokenData['function'] == 'change_email/verify_new') {
      $user->email = $tokenData['new_email'];
      $user->save();

      $license->status = 'active';
      $license->checked_at = now();
      $license->save();

      return $this->apiSuccessResponse(__('user.email_verify-success-change_email-new'));
    } elseif ($tokenData['function'] == 'change_password') {
      $user->password = $tokenData['new_password'];
      $user->save();

      $license->status = 'active';
      $license->checked_at = now();
      $license->save();

      $token = $user->createToken('access_token')->plainTextToken;

      return $this->apiSuccessResponse(__('user.email_verify-success-change_password'));
    }

    \Log::error(self::class . '::emailVerify - Invalid token function name', [$token, $user, $request]);

    return $this->apiErrorResponse('Somethings wrong');
  }

  public function login(Request $request) {
    $validator = Validator::make($request->all(), [
      'email'    => 'required|email',
      'password' => 'required|string',
    ]);

    if ($validator->fails())
      return $this->apiInvalidValuesResponse($validator->errors()->toArray());

    $authed = auth()->attempt(['email' => $request->email, 'password' => $request->password]);
    if (!$authed)
      return $this->apiSingleErrorResponse('password', __('user.login-error-invalid_password'));

    $user = $request->user();

    $licenseResponse = $user->verifyLicense($request);

    if (!$licenseResponse->success)
      return $licenseResponse->apiResponse();

    foreach($user->tokens->where('name', 'access_token') as $token)
      $token->delete();

    $token = $user->createToken('access_token')->plainTextToken;

    return $this->apiSuccessResponse(__('user.login-success'), [
      'token' => $token,
    ]);
  }

  public function passwordForget(Request $request) {
    $validator = Validator::make($request->all(), [
      'email' => 'required|email',
    ]);

    if ($validator->fails())
      return $this->apiInvalidValuesResponse($validator->errors()->toArray());

      $user = User::whereEmail($request->email)
                  ->first();
      if (is_null($user))
        return $this->apiSingleErrorResponse('email', __('user.password_forget-error-invalid_email'));

    $licenseResponse = $user->verifyLicense($request);

    if (!$licenseResponse->success)
      return $licenseResponse->apiResponse();

    $verifyCode = random_int(100000, 999999);

    $token = $user->createToken(
      'pf_email_verify',
      ['email-verify'],
    [
      'function' => 'password_forget',
      'code' => $verifyCode,
      'send_count' => 1,
    ]);

      $email = Email::create([
        'name' => 'user_email_verification',
        'title' => 'Email Verification',
        'template_id' => Template::getEmailVerificationTemplate('password_forget')->name,
        'data' => [
          'username' => $user->username,
          'email' => $user->email,
          'code' => $verifyCode
        ],
        'targets' => [$user->email],
      ]);
      $sended_at = $email->created_at;
      $expire_duration = Setting::getEmailResendDuration();

    $license = $user->license;
    $license->status = 'checking';
    $license->save();

    return $this->apiSuccessResponse(__('user.password_forget-success-email'), [
      'token' => $token->plainTextToken,
      'date' => $sended_at,
      'expire_duration' => $expire_duration,
    ]);
  }

  public function passwordReset(Request $request) {
    $validator = Validator::make($request->all(), [
      'new_password' => 'required|string|min:6',
    ]);

    if ($validator->fails())
      return $this->apiInvalidValuesResponse($validator->errors()->toArray());

    $user = $request->user();

    if (Hash::check($request->new_password, $user->password))
      return $this->apiSingleErrorResponse('new_password', __('user.password_reset-error-invalid_new_password'));

    $token = $user->tokens->last();
    $token->delete();

    $user->password = bcrypt($request->new_password);
    $user->save();

    $license = $user->license;
    $license->status = 'active';
    $license->checked_at = now();
    $license->save();

    return $this->apiSuccessResponse(__('user.password_reset-success'));
  }

  public function getUser(Request $request) {
    /**
     * @var User $user
     */
    $user = $request->user();

    if (!$user) return $this->logout($request);

    foreach($user->tokens->where('name', 'echo_token') as $token)
      $token->delete();
    $echoToken = $user->createToken('echo_token', ["echo"])->plainTextToken;

    return $this->apiSuccessResponse(__('user.get_user-success'), [
      'user'       => $user->toArray(),
      'echo_token' => $echoToken,
    ]);
  }

  public function editAccount(Request $request) {
    $validator = Validator::make($request->all(), [
      'username'            => 'nullable|string|between:10,30|unique:users,username,' . $request->user()->id,
      'profile_image'       => 'nullable|file|mimes:png,jpg,jpeg,gif,bmp',
      'messaging_token'     => 'nullable|string',
      'theme_mode'          => 'nullable|in:light,dark',
      'language'            => 'nullable|in:en,ar,fr',
      'push_notifications'  => 'nullable|boolean',
      'email_notifications' => 'nullable|boolean',
    ]);

    if ($validator->fails())
      return $this->apiInvalidValuesResponse($validator->errors()->toArray());

    /**
     * @var User $user
     */
    $user = $request->user();

    $user->username        = $request->username        ?? $user->username;
    $user->messaging_token = $request->messaging_token ?? $user->messaging_token;

    $setting = $user->setting;

    if ($request->theme_mode)
      $setting['theme_mode']          = $request->theme_mode;

    if ($request->language)
      $setting['language']            = $request->language;

    if ($request->push_notifications)
      $setting['push_notifications']  = $request->push_notifications;

    if ($request->email_notifications)
      $setting['email_notifications'] = $request->email_notifications;

    $user->setting = $setting;

    if ($request->file('profile_image'))
      $user->profile_image_id = $this->moveFile(
        $request,
        'profile_image',
        'api',
        "users/$user->id/",
        "$user->id-profile_image-" . now()->timestamp,
      )->name;

    $user->save();

    event(new UserUpdatedEvent($user));

    return $this->apiSuccessResponse(__('user.edit_user-success'));
  }

  public function changePhone(Request $request) {
    $validator = Validator::make($request->all(), [
      'phone_country' => 'required|string|exists:phone_countries,code',
      'phone'            => 'required|numeric',
    ]);

    if ($validator->fails())
      return $this->apiInvalidValuesResponse($validator->errors()->toArray());

    /**
     * @var User $user
     */
    $user = $request->user();

    if ($request->phone_country == $user->phone_country_id && $request->phone == $user->phone)
      return $this->apiSingleErrorResponse('phone', __('user.change_phone-error-phone'));

    $phone = User::wherePhoneCountryId($request->phone_country)
                 ->wherePhone($request->phone)
                 ->first();

    if ($phone)
      return $this->apiSingleErrorResponse('phone', __('validation.unique', ['attribute' => 'phone']));

    $verifyCode = random_int(100000, 999999);

    $token = $user->createToken('email_verify', ['email-verify'], [
      'function'             => 'change_phone',
      'code'                 => $verifyCode,
      'send_count'           => 1,
      'new_phone_country_id' => $request->phone_country,
      'new_phone'            => $request->phone,
    ]);

    $email = Email::create([
      'name'        => 'user_email_verification',
      'title'       => 'Email Verification',
      'template_id' => Template::getEmailVerificationTemplate('change_phone')->name,
      'data'        => [
        'username' => $user->username,
        'email'    => $user->email,
        'code'     => $verifyCode
      ],
      'targets'     => [$user->email],
    ]);

    $license = $user->license;
    $license->status = 'checking';
    $license->save();

    return $this->apiSuccessResponse(__('user.change_phone-success')." ($verifyCode)", [
      'token'           => $token->plainTextToken,
      'date'            => $email->created_at,
      'expire_duration' => Setting::getEmailResendDuration(),
    ]);
  }

  public function changeEmail(Request $request) {
    $validator = Validator::make($request->all(), [
      'email' => 'required|email',
    ]);

    if ($validator->fails())
      return $this->apiInvalidValuesResponse($validator->errors()->toArray());

    /**
     * @var User $user
     */
    $user = $request->user();

    if ($request->email == $user->email)
      return $this->apiSingleErrorResponse('email',  __('user.change_email-error-email'));

    $email = User::whereEmail($request->email)
                 ->first();

    if ($email)
      return $this->apiSingleErrorResponse('email', __('validation.unique', ['attribute' => 'email']));

    $verifyCode = random_int(100000, 999999);

    $token = $user->createToken('old_email_verify', ['email-verify'], [
      'function'   => 'change_email/verify_old',
      'code'       => $verifyCode,
      'send_count' => 1,
      'new_email'  => $request->email,
    ]);

    $email = Email::create([
      'name'        => 'user_email_verification',
      'title'       => 'Email Verification',
      'template_id' => Template::getEmailVerificationTemplate('change_email_old')->name,
      'data'        => [
        'username' => $user->username,
        'email'    => $user->email,
        'code'     => $verifyCode
      ],
      'targets'     => [$user->email],
    ]);

    $license = $user->license;
    $license->status = 'checking';
    $license->save();

    return $this->apiSuccessResponse(__('user.change_email-success') . " ($verifyCode)", [
      'token'           => $token->plainTextToken,
      'date'            => $email->created_at,
      'expire_duration' => Setting::getEmailResendDuration(),
    ]);
  }

  public function changePassword(Request $request) {
    $validator = Validator::make($request->all(), [
      'old_password' => 'required|string',
      'new_password' => 'required|string|min:6',
    ]);

    if ($validator->fails())
      return $this->apiInvalidValuesResponse($validator->errors()->toArray());

    /**
     * @var User $user
     */
    $user = $request->user();

    if (!Hash::check($request->old_password, $user->password))
      return $this->apiSingleErrorResponse('old_password', __('user.change_password-error-invalid_old_password'));

    if (Hash::check($request->new_password, $user->password))
      return $this->apiSingleErrorResponse('new_password', __('user.change_password-error-invalid_new_password'));

    $verifyCode = random_int(100000, 999999);

    $token = $user->createToken('old_email_verify', ['email-verify'], [
      'function' => 'change_password',
      'code' => $verifyCode,
      'send_count' => 1,
      'new_password' => bcrypt($request->new_password),
    ]);

    $email = Email::create([
      'name' => 'user_email_verification',
      'title' => 'Email Verification',
      'template_id' => Template::getEmailVerificationTemplate('change_password')->name,
      'data' => [
        'username' => $user->username,
        'email'    => $user->email,
        'code'     => $verifyCode
      ],
      'targets' => [$user->email],
    ]);

    $license = $user->license;
    $license->status = 'checking';
    $license->checked_at = now();
    $license->save();

    return $this->apiSuccessResponse(__('user.change_password-success') . " ($verifyCode)", [
      'token' => $token->plainTextToken,
      'date' => $email->created_at,
      'expire_duration' => Setting::getEmailResendDuration(),
    ]);
  }

  public function setAppStatus(Request $request, $status) {
    if (!in_array($status, ['down', 'up']))
      return $this->apiErrorResponse('Invalid status');

    /**
     * @var User $user
     */
    $user = $request->user();

    $user->status = $status == 'down' ? 'online_down' : 'online';
    $user->save();

    return $this->apiSuccessResponse(__('user.set_app_status-success'));
  }

  public function getPrivacyPolicy() {
    return $this->apiSuccessResponse(__('user.get_privacy_policy-success'), [
      'privacy_policy' => Template::getPrivacyPolicyTemplate()->content,
    ]);
  }

  public function logout(Request $request) {
    $user = $request->user();

    $user->tokens()->delete();

    auth()->logout();

    return $this->apiSuccessResponse(__('user.logout-success'));
  }

}
