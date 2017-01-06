<?php

namespace Expresser\Taxonomy;

use Exception;
use Expresser\Support\Model;
use WP_Term;
use WP_Term_Query;

abstract class Base extends Model
{
    protected $term;

    public function __construct(WP_Term $term = null)
    {
        $this->term = $term ?: new WP_Term((object) [
            'taxonomy' => $this->taxonomy,
        ]);

        parent::__construct($this->term->to_array());
    }

    public function getIdAttribute($value)
    {
        return (int) $value;
    }

    public function newQuery()
    {
        $query = (new Builder(new Query(new WP_Term_Query)))->setModel($this);

        return $query->taxonomy($this->taxonomy)->hideEmpty(false);
    }

    public function permalink()
    {
        $permalink = get_term_link($this->term_id, $this->taxonomy);

        if (!is_wp_error($permalink)) {
            return $this->permalink = $permalink;
        }
    }

    public function url()
    {
        return $this->permalink;
    }

    public static function in(array $ids)
    {
        return count(array_intersect($ids, self::get()->lists('term_id'))) > 0;
    }

    public static function registerHooks($class)
    {
        add_action('init', [$class, 'registerTaxonomy'], -PHP_INT_MAX);
    }

    public static function registerTaxonomy()
    {
        throw new Exception('A new taxonomy must override registerTaxonomy.');
    }

    abstract public function taxonomy();
}
