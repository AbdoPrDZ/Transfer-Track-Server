<?php

namespace App\Models;

use App\Events\User\UserUpdatedEvent;
use App\Models\Traits\CustomID;
use App\Models\Traits\Joiner;
use App\Models\Traits\JoinerItem;
use App\Models\Traits\Search;
use App\Models\Traits\SearchFieldItem;
use App\Models\Traits\SearchFieldsItems;
use App\Models\Traits\Seenes;
use DateTimeInterface;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Http\Request;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;
use Laravel\Sanctum\NewAccessToken;
use Str;

/**
 *
 *
 * @property string $id
 * @property string $username
 * @property string $email
 * @property \Illuminate\Support\Carbon|null $email_verified_at
 * @property string $phone_country_id
 * @property string $phone
 * @property string $password
 * @property string|null $remember_token
 * @property string|null $messaging_token
 * @property string $status
 * @property string $setting
 * @property string $seenes_ids
 * @property string $license_id
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property-read \App\Models\License $license
 * @property-read \Illuminate\Notifications\DatabaseNotificationCollection<int, \Illuminate\Notifications\DatabaseNotification> $notifications
 * @property-read int|null $notifications_count
 * @property-read \App\Models\PhoneCountry|null $phoneCountry
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \Laravel\Sanctum\PersonalAccessToken> $tokens
 * @property-read int|null $tokens_count
 * @method static \Database\Factories\UserFactory factory($count = null, $state = [])
 * @method static \Illuminate\Database\Eloquent\Builder|User newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|User newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|User query()
 * @method static \Illuminate\Database\Eloquent\Builder|User whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|User whereEmail($value)
 * @method static \Illuminate\Database\Eloquent\Builder|User whereEmailVerifiedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|User whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|User whereLicenseId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|User whereMessagingToken($value)
 * @method static \Illuminate\Database\Eloquent\Builder|User wherePassword($value)
 * @method static \Illuminate\Database\Eloquent\Builder|User wherePhone($value)
 * @method static \Illuminate\Database\Eloquent\Builder|User wherePhoneCountryId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|User whereRememberToken($value)
 * @method static \Illuminate\Database\Eloquent\Builder|User whereSeenesIds($value)
 * @method static \Illuminate\Database\Eloquent\Builder|User whereSetting($value)
 * @method static \Illuminate\Database\Eloquent\Builder|User whereStatus($value)
 * @method static \Illuminate\Database\Eloquent\Builder|User whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|User whereUsername($value)
 * @mixin \Eloquent
 */
class User extends Authenticatable {

  use HasApiTokens, HasFactory, Notifiable, CustomID, Seenes, Joiner, Search;

  public $incrementing = false;

  /**
   * The attributes that are mass assignable.
   *
   * @var array<int, string>
   */
  protected $fillable = [
    'username',
    'email',
    'email_verified_at',
    'password',
    'phone_country_id',
    'phone',
    'profile_image_id',
    'messaging_token',
    'status',
    'setting',
    'seenes_ids',
    'license_id',
  ];

  /**
   * The attributes that should be hidden for serialization.
   *
   * @var array<int, string>
   */
  protected $hidden = [
    'password',
    'remember_token',
  ];

  /**
   * The attributes that should be cast.
   *
   * @var array<string, string>
   */
  protected $casts = [
    'email_verified_at' => 'datetime',
    'setting' => 'array',
  ];

  protected $dispatchesEvents = [
    'updated' => UserUpdatedEvent::class,
  ];

  /**
   * Get the phone country that owns the user.
   */
  public function phoneCountry() {
    return $this->belongsTo(PhoneCountry::class);
  }

  /**
   * Get the license that owns the user.
   */
  public function license() {
    return $this->belongsTo(License::class);
  }

  public function toArray(?array $fields = null) {
    $array = parent::toArray();

    if (!$fields || in_array('license', $fields))
      $array['license'] = $this->license->toArray();

    if (!$fields || in_array('status', $fields))
      $array['status'] = $this->license->status;

    $fArray = $fields ? [] : $array;

    foreach (($fields ?? []) as $field)
      $fArray[$field] = isset($array[$field]) ? $array[$field] : null;

    return $fArray;
  }


  public $idGeneratorPrefix = 'usr';

  public $idGeneratorData = [
    '->username',
    '->email',
  ];

  /**
   * Get the full phone number.
   *
   * @return string
   */
  public function getFullPhoneNumber(): string {
    return "+{$this->phoneCountry->call_code}{$this->phone}";
  }

  /**
   * Create a new personal access token for the user.
   *
   * @param  string $name
   * @param  array $abilities
   * @param  array $data
   * @param  DateTimeInterface|null  $expiresAt
   * @return NewAccessToken
   */
  public function createToken(string $name, array $abilities = ['*'], $data = [], DateTimeInterface $expiresAt = null) {
    $plainTextToken = sprintf(
      '%s%s%s',
      config('sanctum.token_prefix', ''),
      $tokenEntropy = Str::random(40),
      hash('crc32b', $tokenEntropy)
    );

    $token = $this->tokens()->create([
      'name'       => $name,
      'token'      => hash('sha256', $plainTextToken),
      'abilities'  => $abilities,
      'data'       => $data,
      'expires_at' => $expiresAt,
    ]);

    return new NewAccessToken($token, $token->getKey().'|'.$plainTextToken);
  }

  public static function getSortByColumns() {
    return [
      'id'              => 'app_users.id',
      'username'        => 'app_users.username',
      'phone'           => 'users.phone',
      'email'           => 'users.email',
      'app_status'      => 'users.status',
      'license_status'  => 'licenses.status',
      'created_at'      => 'app_users.created_at',
    ];
  }

  /**
   * get joiners items
   *
   * @return JoinerItem[]
   */
  public static function getJoiners() {
    return [
      JoinerItem::simple('licenses', 'users.license_id', '=', 'licenses.id'),
    ];
  }

  /**
   * Get search fields items
   *
   * @return SearchFieldsItems
   */
  public static function getSearchFields() {
    return new SearchFieldsItems([
      new SearchFieldItem('id'),
      new SearchFieldItem('username'),
      new SearchFieldItem('phone', true),
      new SearchFieldItem('email', true),
      new SearchFieldItem('license_status', true),
      new SearchFieldItem('app_status', true),
      new SearchFieldItem('created_at'),
    ]);
  }

  /**
   * Custom search
   *
   * @param Request $request
   * @param string $field
   * @param string $searchText
   * @return \Closure|array|\Illuminate\Contracts\Database\Query\Expression
   */
  public static function customSearch(string $field, string $searchText) {
    switch ($field) {
      case 'license_status':
        return [['licenses.status', 'LIKE', "%$searchText%"]];
      default:
        return [];
    }
  }

  /**
   * Verify AppUser License
   *
   * @param Request $request
   * @return LicenseVerifyResponse
   */
  public function verifyLicense(Request $request) {
    $route = $request->route()->getName();

    $license = $this->license;

    if ($license && $license->name == 'user')
      if (str_starts_with($route, 'user.') && $license->status == 'blocked')
        return LicenseVerifyResponse::failed(__('license-user.error-blocked'));

      elseif ($route == 'admin.user.block' && $license->status == 'blocked')
        return LicenseVerifyResponse::failed(__('license-user.error-block'));

      elseif ($route == 'admin.user.unblock' && $license->status == 'active')
        return LicenseVerifyResponse::failed(__('license-user.error-unblock'));

      elseif (str_starts_with($route, 'user.') && $license->status == 'checking' && !in_array($route, [
        'user.auth.email_resend',
        'user.auth.email_verify',
        'user.auth.password_reset',
      ]))

        if (is_null($this->email_verified_at))
            return LicenseVerifyResponse::failedEmail($this);

        else
          return LicenseVerifyResponse::failed(__('license-user.error-checking'));

      elseif ($route == 'user.transaction.create' && $this->identity_license->checked_at == null)
        return LicenseVerifyResponse::failed(__('license-user.error-identity-checking'));

      else
        return LicenseVerifyResponse::success();

    return LicenseVerifyResponse::invalidLicense($request, self::class, $license);
  }

}
