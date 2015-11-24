<?php

namespace Edm\Db\TableGateway;

class TermTaxonomyTable extends BaseTableGateway {

    protected $table = 'term_taxonomies';
    protected $alias = 'termTaxonomy';
    protected $modelClass = '\\Edm\\Db\\ResultSet\\Proto\\TermTaxonomyProto';

}
