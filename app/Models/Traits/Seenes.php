<?php

namespace App\Models\Traits;

use Illuminate\Database\Eloquent\Builder;

trait Seenes {

  /**
   * Get if this item is seen
   *
   * @param string $id
   * @return boolean
   */
  public function isSeen(string $id): bool {
    return in_array($id, $this->seenes_ids);
  }

  /**
   * See the item
   *
   * @param string $id
   * @return bool
   */
  public function seeIt(string $id) {
    if ($this->isSeen($id))
      return false;

    $ids = $this->seenes_ids;
    $ids[] = $id;
    $this->seenes_ids = $ids;

    return $this->save();
  }

  /**
   * See all items
   *
   * @param string $id
   * @return void
   */
  public static function seeAll(string $id) {
    self::news($id)->each(function (self $item) use ($id) {
      $item->seeIt($id);
    });
  }

  /**
   * Get the news
   *
   * @param string $id
   * @return Builder
   */
  public static function news(string $id): Builder {
    return self::whereJsonDoesntContain('seenes_ids', $id);
  }

  /**
   * Undocumented function
   *
   * @param string $id
   * @return int
   */
  public static function newsCount(string $id) {
    return self::news($id)->count();
  }

}
