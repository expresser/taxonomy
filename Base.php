<?php namespace Expresser\Taxonomy;

use Exception;

use WP_Term;

abstract class Base extends \Expresser\Support\Model {

  protected $fieldPrefix = 'term_';

  protected $term;

  public function __construct(WP_Term $term = null) {

    $this->term = $term ?: new WP_Term((object)[
      'taxonomy' => $this->taxonomy,
    ]);

    parent::__construct($this->term->to_array());
  }

  public function getIdAttribute($value) {

    return (int)$value;
  }

  public function newQuery() {

    $query = (new Query)->setModel($this);

    return $query->taxonomy($this->taxonomy)->hideEmpty(false);
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

    add_action('init', [$class, 'registerTaxonomy'], -PHP_INT_MAX);
  }

  public static function registerTaxonomy() {

    throw new Exception('A new taxonomy must override registerTaxonomy.');
  }

  public abstract function taxonomy();
}
