<?php

namespace App\Models\Traits;

use Exception;
use Illuminate\Database\Query\Builder as QueryBuilder;
use Illuminate\Database\Eloquent\Builder as EloquentBuilder;

trait Search {

  /**
   * Get search builder
   *
   * @param ?string $searchText the search text
   * @param array $values the search values array
   * @param ?array $fields the fields array
   * @param bool $withJoins search with joins
   * @return QueryBuilder | EloquentBuilder
   */
  public static function search(?string $searchText, array $values = [], ?array $fields = null, bool $withJoins = true) {
    $model = app(self::class);
    $haveCustomSearcher = method_exists($model, 'customSearch');

    return (
      $withJoins ? $model->withJoins() : $model
    )->where(function(QueryBuilder | EloquentBuilder $query) use ($searchText, $values, $model, $fields, $haveCustomSearcher) {
      if ($fields == null || count($fields) > 0) {

        $getSearchText = function (int $i) use ($searchText, $values, $fields): ?string {
          return $fields == null ? $searchText : $values[$fields[$i]];
        };

        $searchFields =  $fields;

        if (!$searchFields)
          $searchFields = method_exists(self::class, 'getSearchFields') ? self::getSearchFields()->getNames() : [];

        for ($i=0; $i < count($searchFields); $i++)
          if ($haveCustomSearcher && self::checkCustomSearchFields($searchFields[$i]))
            $model->customSearch($query, $searchFields[$i], $getSearchText($i));
          else
            $query->orWhere($model->getTable() . ".$searchFields[$i]", 'LIKE', "%" . $getSearchText($i) . "%");
      }
    });
  }

  /**
   * Check if the field is exists on the model search fields
   *
   * @param string $field the field
   * @return bool
   * @throws Exception
   */
  public static function checkCustomSearchFields(string $field) {
    $class = self::class;

    if (!method_exists(app($class), 'getSearchFields'))
      throw new Exception("Undefined method getSearchFields (model: $class)");

    $fields = self::getSearchFields();

    return $fields->checkFieldIsCustom($field);
  }

}

class SearchFieldItem {
  private string $name;
  private bool $isCustom;

  /**
   * Create SearchFieldItem instance
   *
   * @param string $name the field name
   * @param boolean $isCustom if the field is custom search
   */
  public function __construct(string $name, bool $isCustom = false) {
    $this->name = $name;
    $this->isCustom = $isCustom;
  }

  public function getName() {
    return $this->name;
  }

  public function isCustom() {
    return $this->isCustom;
  }

}

class SearchFieldsItems
{
  /**
   * Undocumented variable
   *
   * @var SearchFieldItem[]
   */
  private array $fields;

  /**
   * Create SearchFieldsItems instance
   *
   * @param SearchFieldItem[] $fields the fields
   */
  public function __construct(array $fields) {
    $this->fields = $fields;
  }

  /**
   * Get the fields names
   *
   * @return string[]
   */
  public function getNames(bool $justCustom = false) {
    $names = [];

    foreach ($this->fields as $field)
      if (!$justCustom || ($justCustom && $field->isCustom()))
        $names[] = $field->getName();

    return $names;
  }

  /**
   * Check if the field name exists
   *
   * @param string $name the field name
   * @return bool
   */
  public function checkField(string $name) {
    return in_array($name, $this->getNames());
  }

  /**
   * Check if custom field name is exists
   *
   * @param string $name
   * @return bool
   */
  public function checkFieldIsCustom(string $name) {
    return in_array($name, $this->getNames(true));
  }

}
