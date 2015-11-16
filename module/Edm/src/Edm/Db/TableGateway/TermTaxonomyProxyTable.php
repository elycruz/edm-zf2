<?php

namespace Edm\Db\TableGateway;

class TermTaxonomyProxyTable extends BaseTableGateway {

    protected $table = 'term_taxonomies_proxy';
    protected $alias = 'termTaxonomyProxy';
    protected $modelClass = \Edm\Db\ResultSet\Proto\TermTaxonomyProxyProto;

}
