<?php namespace Expresser\Taxonomy;

use InvalidArgumentException;

use Expresser\PostType\Post;

use WP_Term_Query;

class Query extends \Expresser\Support\Query {

  public function __construct(WP_Term_Query $query) {

    $this->include = [];
    $this->exclude = [];

    parent::__construct($query);
  }

  public function find($id) {

    return $this->term($id)->first();
  }

  public function findAll(array $ids) {

    return $this->terms($ids)->get();
  }

  public function findBySlug($slug) {

    return $this->term($slug)->first();
  }

  public function first() {

    return $this->limit(1)->get()->first();
  }

  public function limit($number) {

    return $this->number($number);
  }

  public function post($postId) {

    if (is_integer($postId)) {

      $this->posts([$postId]);
    }
    else {

      throw new InvalidArgumentException;
    }

    return $this;
  }

  public function posts(array $postIds) {

    $ids = wp_get_object_terms($postIds, $this->model->taxonomy, ['fields' => 'ids']);

    return $this->terms($ids);
  }

  public function postType($postType) {

    $ids = Post::query()->type($postType)->get()->lists('ID');

    return $this->posts($ids);
  }

  public function term($id) {

    if (is_integer($id)) {

      $ids = [];

      if ($id > 0) {

        $ids[] = $id;
      }

      $this->terms($ids);
    }
    else if (is_string($id)) {

      $this->slug($id);
    }
    else {

      throw new InvalidArgumentException;
    }

    return $this;
  }

  public function terms(array $ids, $operator = 'IN') {

    $ids = count($ids) > 0 ? $ids : [PHP_INT_MAX];

    switch ($operator) {

      case 'IN':

        $this->exclude = array_diff($this->exclude, $ids);
        $this->include = $ids;

        break;

      case 'NOT IN':

        $this->include = array_diff($this->include, $ids);
        $this->exclude = $ids;

        break;
    }

    return $this;
  }

  public function taxonomy($taxonomy) {

    $this->taxonomy = $taxonomy;

    return $this;
  }

  public function orderBy($orderby = 'name', $order = 'ASC') {

    $this->orderby = $orderby;
    $this->order = $order;

    return $this;
  }

  public function hideEmpty($empty = true) {

    if (is_bool($empty)) {

      $this->hide_empty = $empty;
    }
    else {

      throw new InvalidArgumentException;
    }

    return $this;
  }

  public function number($number) {

    if (is_integer($number)) {

      $this->number = $number;
    }
    else {

      throw new InvalidArgumentException;
    }

    return $this;
  }

  public function slug($slug) {

    if (is_string($slug)) {

      $this->slug = $slug;
    }
    else {

      throw new InvalidArgumentException;
    }

    return $this;
  }

  public function slugs(array $slugs) {

    $this->slug = $slugs;

    return $this;
  }

  public function childOf($id) {

    if (is_integer($id)) {

      $this->child_of = $id;
    }
    else {

      throw new InvalidArgumentException;
    }

    return $this;
  }

  public function parent($id) {

    if (is_integer($id)) {

      $this->parent = $id;
    }
    else {

      throw new InvalidArgumentException;
    }

    return $this;
  }
}
