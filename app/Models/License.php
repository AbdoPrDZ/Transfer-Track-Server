<?php

namespace App\Models;

use App\Http\Controllers\Controller;
use App\Models\Traits\CustomID;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Http\Request;

/**
 * 
 *
 * @method static \Illuminate\Database\Eloquent\Builder|License newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|License newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|License query()
 * @property string $id
 * @property string $name
 * @property array $data
 * @property array $statuses
 * @property string $status
 * @property string|null $checker_id
 * @property int|null $checked_at
 * @property int|null $created_at
 * @property int|null $updated_at
 * @method static \Illuminate\Database\Eloquent\Builder|License whereCheckedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|License whereCheckerId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|License whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|License whereData($value)
 * @method static \Illuminate\Database\Eloquent\Builder|License whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|License whereName($value)
 * @method static \Illuminate\Database\Eloquent\Builder|License whereStatus($value)
 * @method static \Illuminate\Database\Eloquent\Builder|License whereStatuses($value)
 * @method static \Illuminate\Database\Eloquent\Builder|License whereUpdatedAt($value)
 * @mixin \Eloquent
 */
class License extends Model {

  use HasFactory, CustomID;

  public $incrementing = false;

  protected $fillable = [
    'name',
    'data',
    'statuses',
    'status',
    'checker_id',
    'checked_at',
  ];

  protected $casts = [
    'data' => 'json',
    'statuses' => 'json',
    'checked_at' => 'timestamp',
    'created_at' => 'timestamp',
    'updated_at' => 'timestamp',
  ];

  public function checker() {
    return User::find($this->checker_id);
  }

  public function toArray(?array $fields = null) {
    $array = parent::toArray();

    if (!$fields || in_array('checker', $fields))
      $array['checker'] = $this->checker();

    $fArray = $fields ? [] : $array;

    foreach (($fields ?? []) as $field)
      $fArray[$field] = isset($array[$field]) ? $array[$field] : null;

    return $fArray;
  }

  public $idGeneratorPrefix = 'lsn';

  public $idGeneratorData = [
    '->name',
    'CURRENT_TIME',
  ];

  /**
   * Check if the license is checked
   *
   * @return boolean
   */
  public function isChecked() {
    return !is_null($this->checked_at);
  }

}

class LicenseVerifyResponse
{

  /**
   * Create response instance
   *
   * @param bool $success
   * @param ?string $message
   * @param array $data
   * @param ?int $code
   * @param int $statusCode
   */
  public function __construct(
    public bool $success = false,
    public ?string $message = null,
    public array $data = [],
    public ?int $code = null,
    public int $statusCode = 200,
  ) {}

  /**
   * Create failed response instance
   *
   * @param string $message
   * @param array $data
   * @param ?int $code
   * @param int $statusCode
   * @return self
   */
  public static function failed(string $message, array $data = [], ?int $code = self::UN_HAVE_ACCESS, int $statusCode = 403): self {
    return new self(false, $message, $data, $code, $statusCode);
  }

  /**
   * Create email failed response instance
   *
   * @param string $message
   * @return self
   */
  public static function failedEmail(User $user, string $message = 'license.error-email') {
    // clear user tokens
    $user->tokens()->delete();

    $verifyCode = random_int(100000, 999999);

    $token = $user->createToken('email_verify', ['email-verify'], [
      'function' => 'register',
      'code' => $verifyCode,
      'send_count' => 1,
    ]);

    Email::create([
      'name' => 'user_email_verification',
      'title' => 'Email Verification',
      'template_id' => Template::getEmailVerificationTemplate('register')->name,
      'data' => [
        'username' => $user->username,
        'email'    => $user->email,
        'code'     => $verifyCode
      ],
      'targets' => [$user->email],
    ]);

    return self::failed(__($message) , [
      'redirect_to' => 'email_verify',
      'token' => $token->plainTextToken,
      'email' => $user->email,
    ], self::EMAIL_NOT_VERIFIED);
  }

  /**
   * Create success response instance
   *
   * @return self
   */
  public static function success(): self {
    return new self(true);
  }

  /**
   * Create invalid license response instance
   *
   * @param Request $request
   * @param string $model
   * @param ?License $license
   * @return self
   */
  public static function invalidLicense(Request $request, string $model, ?License $license): self {
    \Log::warning("Invalid License $license?->id.$license?->name for model $model, route {$request->route()?->getName()}", [
      $model, $request->route(), $license, $request
    ]);

    return self::failed(
      __('license.error', [
        'license_name' => $license?->name ?? 'None',
        'license_id' => $license?->id ?? 'None',
        'model_name' => $model,
        'route_name' => $request->route()?->getName() ?? 'None',
      ]),
      [],
      self::INVALID_LICENSE,
    );
  }

  // Failed codes
  public const INVALID_LICENSE = 0;
  public const UN_HAVE_ACCESS = 1;
  public const PHONE_NOT_VERIFIED = 2;
  public const EMAIL_NOT_VERIFIED = 3;

  /**
   * Get api response
   *
   * @return \Illuminate\Http\JsonResponse
   */
  public function apiResponse() {
    if ($this->success)
      return Controller::apiSuccessResponse(__($this->message), $this->data, $this->statusCode);

    elseif ($this->code == self::PHONE_NOT_VERIFIED)
      return Controller::apiSingleErrorResponse('phone', __($this->message), $this->data, $this->statusCode);

    elseif ($this->code == self::EMAIL_NOT_VERIFIED)
      return Controller::apiSingleErrorResponse('email', __($this->message), $this->data, $this->statusCode);

    else
      return Controller::apiErrorResponse(__($this->message), [], $this->statusCode);
  }

}
