<?php namespace Expresser\Taxonomy;

use Exception;

abstract class Base extends \Expresser\Support\Model {

  protected $fieldPrefix = 'term_';

  public function getIdAttribute($value) {

    return (int)$value;
  }

  public function newQuery() {

    return (new Query)->setModel($this)->hideEmpty(false);
  }

  public function permalink() {

    $permalink = get_term_link($this->id, $this->taxonomy);

    if (!is_wp_error($permalink)) return $this->permalink = $permalink;
  }

  public function url() {

    return $this->permalink;
  }

  public static function in(array $ids) {

    return count(array_intersect($ids, self::get()->lists('id'))) > 0;
  }

  public static function registerHooks($class) {

    add_action('init', [$class, 'registerTaxonomy']);
  }

  public static function registerTaxonomy() {

    throw new Exception('A new taxonomy must override registerTaxonomy.');
  }

  public abstract function taxonomy();
}
