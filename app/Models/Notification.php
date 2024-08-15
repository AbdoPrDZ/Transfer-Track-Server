<?php

namespace App\Models;

use App\Events\Notification\NotificationCreatedEvent;
use App\Models\Traits\CustomID;
use App\Models\Traits\Seenes;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

/**
 * 
 *
 * @property string $id
 * @property string $name
 * @property string $title
 * @property string $body
 * @property string|null $image
 * @property array $data
 * @property string $status
 * @property array $targets_ids
 * @property array $hides_ids
 * @property array $seenes_ids
 * @property int|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @method static \Illuminate\Database\Eloquent\Builder|Notification newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|Notification newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|Notification query()
 * @method static \Illuminate\Database\Eloquent\Builder|Notification whereBody($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Notification whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Notification whereData($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Notification whereHidesIds($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Notification whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Notification whereImage($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Notification whereName($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Notification whereSeenesIds($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Notification whereStatus($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Notification whereTargetsIds($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Notification whereTitle($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Notification whereUpdatedAt($value)
 * @mixin \Eloquent
 */
class Notification extends Model {

  use HasFactory, CustomID, Seenes;

  public $incrementing = false;

  protected $fillable = [
    'name',
    'title',
    'body',
    'data',
    'image',
    'targets_ids',
    'status',
    'hides_ids',
    'seenes_ids',
  ];

  protected $casts = [
    'data'        => 'array',
    'targets_ids' => 'array',
    'hides_ids'   => 'array',
    'seenes_ids'  => 'array',
    'created_at'  => 'timestamp',
  ];

  protected $dispatchesEvents = [
    'created' => NotificationCreatedEvent::class,
  ];

  public function targets(): array {
    $items = [];

    foreach ($this->targets_ids as $target)
      $items[] = User::find($target);

    return $items;
  }

  public function toArray(?array $fields = null) {
    $array = parent::toArray();

    $data_en = $this->data;
    $data_ar = $this->data;
    $data_fr = $this->data;

    foreach ($data_en as $key => $value)
      if (is_array($value) && isset($value['en']) && isset($value['ar']) && isset($value['fr'])) {
        $data_en[$key] = $value['en'];
        $data_ar[$key] = $value['ar'];
        $data_fr[$key] = $value['fr'];
      }

    if (!$fields || in_array('title_en', $fields))
      $array['title_en'] = __($this->title, $data_en, 'en');
    if (!$fields || in_array('title_ar', $fields))
      $array['title_ar'] = __($this->title, $data_ar, 'ar');
    if (!$fields || in_array('title_fr', $fields))
      $array['title_fr'] = __($this->title, $data_fr, 'fr');

    if (!$fields || in_array('body_en', $fields))
      $array['body_en'] = __($this->body, $data_en, 'en');
    if (!$fields || in_array('body_en', $fields))
      $array['body_ar'] = __($this->body, $data_ar, 'ar');
    if (!$fields || in_array('body_en', $fields))
      $array['body_fr'] = __($this->body, $data_fr, 'fr');

    $fArray = $fields ? [] : $array;

    foreach (($fields ?? []) as $field)
      $fArray[$field] = isset($array[$field]) ? $array[$field] : null;

    return $fArray;
  }

  public $idGeneratorPrefix = 'ntf';

  public $idGeneratorData = [
    '->name',
    'CURRENT_TIME',
  ];

  /**
   * Get if this item is hidden
   *
   * @param string $id
   * @return boolean
   */
  public function isHidden(string $id): bool {
    return in_array($id, $this->hides_ids);
  }

  /**
   * jide the item
   *
   * @param string $id
   * @return bool
   */
  public function hideIt(string $id) {
    if ($this->isHidden($id))
      return false;

    $ids = $this->hides_ids;
    $ids[] = $id;
    $this->hides_ids = $ids;

    return $this->save();
  }

  /**
   * Get Notification targets messaging_tokens
   *
   * @return array
   */
  public function getTokens() {
    $tokens = [];

    foreach ($this->targets() as $target)
      if ($target->messaging_token && $target->setting['push_notifications'])
        $tokens[] = $target->messaging_token;

    return $tokens;
  }


  /**
   * Send a notification to app users
   *
   * @param array $data
   * @param array $targets
   * @return Model|Notification|null
   */
  public static function sendAll(array $data, array $targets) {
    if (count($targets) > 0) {
      $data['targets_ids'] = $targets;

      return Notification::create($data);
    }
  }

  /**
   * Send a notification to app user
   *
   * @param array $data
   * @param string $target
   * @return Model|Notification|null
   */
  public static function sendSingle(array $data, string $target) {
    self::sendAll($data, [$target]);
  }

}
