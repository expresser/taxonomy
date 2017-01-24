<?php

namespace Expresser\Taxonomy;

use Expresser\Support\Query as BaseQuery;
use InvalidArgumentException;
use WP_Term_Query;

class Query extends BaseQuery
{
    public function __construct(WP_Term_Query $query)
    {
        parent::__construct($query);
    }

    public function execute()
    {
        $terms = $this->query->get_terms();

        return $terms;
    }

    public function terms(array $ids, $operator = 'IN')
    {
        $ids = count($ids) > 0 ? $ids : [PHP_INT_MAX];

        switch ($operator) {
            case 'IN':
                $exclude = $this->getQueryVar('exclude');
                $this->setQueryVar('exclude', array_diff($exclude, $ids));
                $this->setQueryVar('include', $ids);
                break;
            case 'NOT IN':
                $include = $this->getQueryVar('include');
                $this->setQueryVar('include', array_diff($include, $ids));
                $this->setQueryVar('exclude', $ids);
                break;
        }

        return $this;
    }

    public function term($id)
    {
        if (is_int($id)) {
            $ids = [];

            if ($id > 0) {
                $ids[] = $id;
            }

            $this->terms($ids);
        } elseif (is_string($id)) {
            $this->slug($id);
        } else {
            throw new InvalidArgumentException();
        }

        return $this;
    }

    public function taxonomy($taxonomy)
    {
        $this->setQueryVar('taxonomy', $taxonomy);

        return $this;
    }

    public function orderBy($orderby = 'name', $order = 'ASC')
    {
        $this->setQueryVar('orderby', $orderby);
        $this->setQueryVar('order', $order);

        return $this;
    }

    public function hideEmpty($empty = true)
    {
        if (is_bool($empty)) {
            $this->setQueryVar('hide_empty', $empty);
        } else {
            throw new InvalidArgumentException();
        }

        return $this;
    }

    public function number($number)
    {
        if (is_int($number)) {
            $this->setQueryVar('number', $number);
        } else {
            throw new InvalidArgumentException();
        }

        return $this;
    }

    public function limit($number)
    {
        return $this->number($number);
    }

    public function slug($slug)
    {
        if (is_string($slug)) {
            $this->setQueryVar('slug', $slug);
        } else {
            throw new InvalidArgumentException();
        }

        return $this;
    }

    public function slugs(array $slugs)
    {
        $this->setQueryVar('slug', $slugs);

        return $this;
    }

    public function childOf($id)
    {
        if (is_int($id)) {
            $this->setQueryVar('child_of', $id);
        } else {
            throw new InvalidArgumentException();
        }

        return $this;
    }

    public function parent($id)
    {
        if (is_int($id)) {
            $this->setQueryVar('parent', $id);
        } else {
            throw new InvalidArgumentException();
        }

        return $this;
    }

    protected function initQueryVars()
    {
        $this->setQueryVar('exclude', []);
        $this->setQueryVar('include', []);
    }
}
