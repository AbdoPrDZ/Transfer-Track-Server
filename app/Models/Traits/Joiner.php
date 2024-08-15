<?php

namespace App\Models\Traits;

use Eloquent;
use \Illuminate\Contracts\Database\Query\Expression;
use Illuminate\Database\Query\Builder;

trait Joiner {

  /**
   * with joins
   *
   * @return Builder|Eloquent;
   */
  public static function withJoins() {
    /**
     * @var JoinerItem[] $joiners
     */
    $joiners = method_exists(app(self::class), 'getJoiners') ? app(self::class)->getJoiners() : [];

    $count = count($joiners);

    if ($count > 0) {
      if ($joiners[0]->isSub)
        $query = self::leftJoinSub(
          $joiners[0]->query,
          $joiners[0]->as,
          $joiners[0]->first,
          $joiners[0]->operator,
          $joiners[0]->second,
        );
      else
        $query = self::leftJoin(
          $joiners[0]->table,
          $joiners[0]->first,
          $joiners[0]->operator,
          $joiners[0]->second,
        );

      if ($count > 1) for ($i=1; $i < count($joiners); $i++)
        if ($joiners[$i]->isSub)
          $query = $query->leftJoinSub(
            $joiners[$i]->query,
            $joiners[$i]->as,
            $joiners[$i]->first,
            $joiners[$i]->operator,
            $joiners[$i]->second,
          );
        else
          $query = $query->leftJoin(
            $joiners[$i]->table,
            $joiners[$i]->first,
            $joiners[$i]->operator,
            $joiners[$i]->second,
          );

      return $query;
    }

    return app(self::class);
  }

}

class JoinerItem {

  public function __construct(
    public bool $isSub,
    public Expression|string|null $table,
    public \Closure|Builder|\Illuminate\Database\Eloquent\Builder|string|null $query,
    public string|null $as,
    public \Closure|Expression|string $first,
    public string|null $operator = null,
    public Expression|string|null $second = null,
  ) {}

  public static function simple(
    Expression|string|null $table,
    \Closure|Expression|string $first,
    string|null $operator = null,
    Expression|string|null $second = null,
  ): JoinerItem {
    return new JoinerItem(
      false,
      $table,
      null,
      null,
      $first,
      $operator,
      $second,
    );
  }

  public static function sub(
    \Closure|Builder|\Illuminate\Database\Eloquent\Builder|string $query,
    string|null $as,
    \Closure|Expression|string $first,
    string|null $operator = null,
    Expression|string|null $second = null,
  ): JoinerItem {
    return new JoinerItem(
      true,
      null,
      $query,
      $as,
      $first,
      $operator,
      $second,
    );
  }

}
