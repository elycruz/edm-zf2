<?php

namespace Edm\Db\TableGateway;

class TermTaxonomyTable extends BaseTableGateway {

    protected $table = 'termTaxonomy';
    protected $alias = 'term_taxonomies';
    protected $modelClass = \Edm\Model\TermTaxonomy;

}
