<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

/**
 * 
 *
 * @property string $name
 * @property string|null $value
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @method static \Illuminate\Database\Eloquent\Builder|Setting newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|Setting newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|Setting query()
 * @method static \Illuminate\Database\Eloquent\Builder|Setting whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Setting whereName($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Setting whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Setting whereValue($value)
 * @mixin \Eloquent
 */
class Setting extends Model {

  use HasFactory;

  public $incrementing = false;

  protected $primaryKey = 'name';

  protected $fillable = [
    'name',
    'value',
  ];

  /**
   * Get Setting item value by name
   *
   * @param string $name
   * @return string
   */
  public static function getSetting(string $name) {
    return self::whereName($name)->first()?->value;
  }

  /**
   * Set Setting value by name
   *
   * @param string $name
   * @param string $value
   * @return bool
   */
  public static function setSetting(string $name, string $value): bool {
    return self::whereName($name)->update(['value' => $value]);
  }

  /**
   * Get Email expire duration value
   *
   * @return int
   */
  public static function getEmailResendDuration(): int {
    return (int) (self::getSetting('email_resend_duration') ?? 60 * 3);
  }

  /**
   * Set email expire duration value
   *
   * @param int $value
   * @return bool
   */
  public static function setEmailResendDuration($value): bool {
    return self::setSetting('email_resend_duration', $value);
  }

  /**
   * Get SMS expire duration value
   *
   * @return int
   */
  public static function getSMSResendDuration(): int {
    return (int) (self::getSetting('sms_resend_duration') ?? 60 * 3);
  }

  /**
   * Set SMS expire duration value
   *
   * @param int $value
   * @return bool
   */
  public static function setSMSResendDuration($value): bool {
    return self::setSetting('sms_resend_duration', $value);
  }

  /**
   * Get Transaction expire duration
   *
   * @return int
   */
  public static function getTransactionExpiredDuration(): int {
    return (int) self::getSetting('transaction_expire_duration') ?? (60 * 3);
  }

  /**
   * Set Transaction expire duration value
   *
   * @param int $value
   * @return bool
   */
  public static function setTransactionExpiredDuration($value): bool {
    return self::setSetting('transaction_expire_duration', $value);
  }

}
