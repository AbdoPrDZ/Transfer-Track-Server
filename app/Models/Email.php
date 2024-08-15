<?php

namespace App\Models;

use App\Events\Email\EmailCreatedEvent;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

/**
 * 
 *
 * @property int $id
 * @property string $name
 * @property string $title
 * @property string $template_id
 * @property array $data
 * @property array $targets
 * @property int|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property-read \App\Models\Template $template
 * @method static \Illuminate\Database\Eloquent\Builder|Email newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|Email newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|Email query()
 * @method static \Illuminate\Database\Eloquent\Builder|Email whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Email whereData($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Email whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Email whereName($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Email whereTargets($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Email whereTemplateId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Email whereTitle($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Email whereUpdatedAt($value)
 * @mixin \Eloquent
 */
class Email extends Model {

  use HasFactory;

  /**
   * The attributes that are mass assignable.
   *
   * @var array
   */
  protected $fillable = [
    "name",
    "title",
    "template_id",
    "data",
    "targets",
  ];

  protected $casts = [
    'data'       => 'array',
    'targets'    => 'array',
    'created_at' => 'timestamp',
  ];

  protected $dispatchesEvents = [
    'created' => EmailCreatedEvent::class,
  ];

  /**
   * belongs to email template
   *
   * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
   */
  public function template() {
    return $this->belongsTo(Template::class, 'template_id', 'name');
  }

  public function toArray(?array $fields = null) {
    $array = parent::toArray();

    if (!$fields || in_array('template', $fields))
      $array['template'] = $this->template->toArray();

    $fArray = $fields ? [] : $array;

    foreach (($fields ?? []) as $field)
      $fArray[$field] = isset($array[$field]) ? $array[$field] : null;

    return $fArray;
  }

  /**
   * Render the email template
   *
   * @return string
   */
  public function renderTemplate() {
    $template = $this->template;

    return $template->render($this->data);
  }

}
