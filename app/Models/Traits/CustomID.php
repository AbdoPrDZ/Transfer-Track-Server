<?php


namespace App\Models\Traits;

trait CustomID {

  /**
   * Generate a custom id
   *
   * @return string
   */
  public function generateId() {
    $prefix = property_exists($this, 'idGeneratorPrefix') ? ($this->idGeneratorPrefix . '-') : '';

    $hashedData = '';
    if (property_exists($this, 'idGeneratorData')) {
      $data = [];

      foreach ($this->idGeneratorData as $item) {
        if (is_string($item) && str_starts_with($item, '->')) {
          $col = str_replace('->', '', $item);
          $col = str_replace('?', '', $col);
          if ($this->offsetExists($col))
            $data[] = $this->$col;
          elseif (!str_ends_with($item, '?'))
            throw new \Exception("Invalid column name $col");
        } elseif ($item == 'CURRENT_TIME')
          $data[] = time();
        else
          $data[] = $item;
      }

      $hashedData = md5(implode('-', $data));
    }

    return $prefix . $hashedData;
  }

  public static function boot() {
    parent::boot();
    static::creating(function ($model) {
      $model->{$model->primaryKey} = $model->generateId();
    });
  }

}
