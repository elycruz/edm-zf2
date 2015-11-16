<?php

namespace Edm\Db\TableGateway;

use Edm\Db\ResultSet\Proto\TermTaxonomyProto;

class TermTaxonomyTable extends BaseTableGateway {

    protected $table = 'term_taxonomies';
    protected $alias = 'termTaxonomy';
    protected $modelClass = TermTaxonomyProto;

}
