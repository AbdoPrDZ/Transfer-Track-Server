<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

/**
 * 
 *
 * @method static \Illuminate\Database\Eloquent\Builder|PhoneCountry newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|PhoneCountry newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|PhoneCountry query()
 * @property string $code
 * @property string $name
 * @property string $call_code
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @method static \Illuminate\Database\Eloquent\Builder|PhoneCountry whereCallCode($value)
 * @method static \Illuminate\Database\Eloquent\Builder|PhoneCountry whereCode($value)
 * @method static \Illuminate\Database\Eloquent\Builder|PhoneCountry whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|PhoneCountry whereName($value)
 * @method static \Illuminate\Database\Eloquent\Builder|PhoneCountry whereUpdatedAt($value)
 * @mixin \Eloquent
 */
class PhoneCountry extends Model {

  use HasFactory;

  public $incrementing = false;

  protected $primaryKey = 'code';

  /**
   * The attributes that are mass assignable.
   *
   * @var array<int, string>
   */
  protected $fillable = [
    'code',
    'name',
    'call_code',
  ];

  /**
   * The attributes that should be cast.
   *
   * @var array<string, string>
   */
  protected $casts = [
    'created_at' => 'datetime',
    'updated_at' => 'datetime',
  ];

}
