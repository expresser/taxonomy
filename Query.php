<?php namespace Expresser\Taxonomy;

use InvalidArgumentException;

use Expresser\PostType\Post;

class Query extends \Expresser\Support\Builder {

  protected $params = [];

  protected $exclude = [];

  protected $include = [];

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

    $this->number($number);

    return $this;
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

      if ($id > 0) $ids[] = $id;

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

    $this->params['include'] = $this->include;
    $this->params['exclude'] = $this->exclude;

    return $this;
  }

  public function get() {

    $terms = get_terms($this->params);

    return $this->getModels($terms);
  }

  public function taxonomy($taxonomy) {

    $this->params['taxonomy'] = $taxonomy;

    return $this;
  }

  public function orderBy($orderby = 'name', $order = 'ASC') {

    $this->params['orderby'] = $orderby;
    $this->params['order'] = $order;

    return $this;
  }

  public function hideEmpty($empty = true) {

    if (is_bool($empty)) {

      $this->params['hide_empty'] = $empty;
    }
    else {

      throw new InvalidArgumentException;
    }

    return $this;
  }

  public function number($number) {

    if (is_integer($number)) {

      $this->params['number'] = $number;
    }
    else {

      throw new InvalidArgumentException;
    }

    return $this;
  }

  public function slug($slug) {

    if (is_string($slug)) {

      $this->params['slug'] = $slug;
    }
    else {

      throw new InvalidArgumentException;
    }

    return $this;
  }

  public function slugs(array $slugs) {

    $this->params['slug'] = $slugs;

    return $this;
  }

  public function childOf($id) {

    if (is_integer($id)) {

      $this->params['child_of'] = $id;
    }
    else {

      throw new InvalidArgumentException;
    }

    return $this;
  }

  public function parent($id) {

    if (is_integer($id)) {

      $this->params['parent'] = $id;
    }
    else {

      throw new InvalidArgumentException;
    }

    return $this;
  }
}
