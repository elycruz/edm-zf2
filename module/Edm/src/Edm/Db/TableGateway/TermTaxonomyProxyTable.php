<?php

namespace Edm\Db\TableGateway;

class TermTaxonomyProxyTable extends BaseTableGateway {

    protected $table = 'termTaxonomyProxy';
    protected $alias = 'term_taxonomies_proxy';
    protected $modelClass = \Edm\Model\TermTaxonomyProxy;

}
