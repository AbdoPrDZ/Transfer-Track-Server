<?php

namespace App\Models;

use App\Services\RequestLocale;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

/**
 * 
 *
 * @property string $name
 * @property string $content
 * @property array $args
 * @property int|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @method static \Illuminate\Database\Eloquent\Builder|Template newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|Template newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|Template query()
 * @method static \Illuminate\Database\Eloquent\Builder|Template whereArgs($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Template whereContent($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Template whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Template whereName($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Template whereUpdatedAt($value)
 * @mixin \Eloquent
 */
class Template extends Model {

  use HasFactory;

  public $incrementing = false;

  protected $primaryKey = 'name';

  /**
   * The attributes that are mass assignable.
   *
   * @var array
   */
  protected $fillable = [
    'name',
    'content',
    'args',
  ];

  protected $casts = [
    'args' => 'array',
    'created_at' => 'timestamp',
  ];

  /**
   * Render template content by data
   *
   * @param array $data
   * @return string
   */
  public function render(array $data) {
    /**
     * @var string $content
     */
    $content = $this->content;

    foreach ($this->args as $key)
      if (array_key_exists($key, $data))
        $content = str_replace("<-$key->", $data[$key], $content);

    return $content;
  }

  /**
   * Get email verification template
   *
   * @param string $function the function
   * @param ?RequestLocale $locale the language locale (en, ar, fr)
   * @return Template
   */
  static function getEmailVerificationTemplate(string $function, ?RequestLocale $locale = null) {
    $locale = $locale ?? RequestLocale::fromRequest(request());
    return Template::find("$function-email_verification-$locale");
  }

  /**
   * Get privacy policy template
   *
   * @param ?RequestLocale $locale the language locale (en, ar, fr)
   * @return Template
   */
  static function getPrivacyPolicyTemplate(?RequestLocale $locale = null) {
    $locale = $locale ?? RequestLocale::fromRequest(request());
    return Template::find("privacy_policy-$locale");
  }

}
