<?php namespace Expresser\Taxonomy;

use InvalidArgumentException;

use Expresser\Type\Post;

class Query extends \Expresser\Support\Builder {

  protected $params = [];

  protected $exclude = [];

  protected $include = [];

  public function find($id) {

    return $this->whereTerm($id)->first();
  }

  public function findAll(array $ids) {

    return $this->whereTerms($ids)->get();
  }

  public function findBySlug($slug) {

    return $this->whereTerm($slug)->first();
  }

  public function first() {

    return $this->limit(1)->get()->first();
  }

  public function get() {

    $terms = get_terms($this->model->taxonomy, $this->params);

    return $this->getModels($terms);
  }

  public function limit($number) {

    if (is_integer($number)) {

      $this->params['number'] = $number;
    }
    else {

      throw new InvalidArgumentException;
    }

    return $this;
  }

  public function orderBy($orderby = 'name', $order = 'ASC') {

    $this->params['orderby'] = $orderby;
    $this->params['order'] = $order;

    return $this;
  }

  public function whereChildOf($id) {

    if (is_integer($id)) {

      $this->params['child_of'] = $id;
    }
    else {

      throw new InvalidArgumentException;
    }

    return $this;
  }

  public function whereEmpty($empty) {

    if (is_bool($empty)) {

      $this->params['hide_empty'] = !$empty;
    }
    else {

      throw new InvalidArgumentException;
    }

    return $this;
  }

  public function whereParent($id) {

    if (is_integer($id)) {

      $this->params['parent'] = $id;
    }
    else {

      throw new InvalidArgumentException;
    }

    return $this;
  }

  public function wherePost($postId) {

    if (is_integer($postId)) {

      $this->wherePosts(array($postId));
    }
    else {

      throw new InvalidArgumentException;
    }

    return $this;
  }

  public function wherePosts(array $postIds) {

    $ids = wp_get_object_terms($postIds, $this->model->taxonomy, array('fields' => 'ids'));

    return $this->whereTerms($ids);
  }

  public function wherePostType($postType) {

    $ids = Post::whereType($postType)->get()->lists('ID');

    return $this->wherePosts($ids);
  }

  public function whereSlug($slug) {

    if (is_string($slug)) {

      $this->params['slug'] = $slug;
    }
    else {

      throw new InvalidArgumentException;
    }

    return $this;
  }

  public function whereSlugs(array $slugs) {

    $this->params['slug'] = $slugs;

    return $this;
  }

  public function whereTerm($id) {

    if (is_integer($id)) {

      $ids = [];

      if ($id > 0) $ids[] = $id;

      $this->whereTerms($ids);
    }
    else if (is_string($id)) {

      $this->whereSlug($id);
    }
    else {

      throw new InvalidArgumentException;
    }

    return $this;
  }

  public function whereTerms(array $ids, $operator = 'IN') {

    $ids = count($ids) > 0 ? $ids : array(mt_getrandmax());

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
}
