<?php namespace Expresser\Taxonomy;

use Exception;

abstract class Native extends Base {

  public static function registerTaxonomy() {

    // Do not implement registerTaxonomy for native or exisitng terms
  }
}
