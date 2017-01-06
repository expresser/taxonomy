<?php

namespace Expresser\Taxonomy;

use Expresser\Support\Query as BaseQuery;
use InvalidArgumentException;
use WP_Term_Query;

class Query extends BaseQuery
{
    public function __construct(WP_Term_Query $query)
    {
        $this->include = [];
        $this->exclude = [];

        parent::__construct($query);
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

    public function terms(array $ids, $operator = 'IN')
    {
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

    public function taxonomy($taxonomy)
    {
        $this->taxonomy = $taxonomy;

        return $this;
    }

    public function orderBy($orderby = 'name', $order = 'ASC')
    {
        $this->orderby = $orderby;
        $this->order = $order;

        return $this;
    }

    public function hideEmpty($empty = true)
    {
        if (is_bool($empty)) {
            $this->hide_empty = $empty;
        } else {
            throw new InvalidArgumentException();
        }

        return $this;
    }

    public function number($number)
    {
        if (is_int($number)) {
            $this->number = $number;
        } else {
            throw new InvalidArgumentException();
        }

        return $this;
    }

    public function slug($slug)
    {
        if (is_string($slug)) {
            $this->slug = $slug;
        } else {
            throw new InvalidArgumentException();
        }

        return $this;
    }

    public function slugs(array $slugs)
    {
        $this->slug = $slugs;

        return $this;
    }

    public function childOf($id)
    {
        if (is_int($id)) {
            $this->child_of = $id;
        } else {
            throw new InvalidArgumentException();
        }

        return $this;
    }

    public function parent($id)
    {
        if (is_int($id)) {
            $this->parent = $id;
        } else {
            throw new InvalidArgumentException();
        }

        return $this;
    }

    public function limit($number)
    {
        return $this->number($number);
    }
}
