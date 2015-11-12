<?php

namespace Edm\Db\TableGateway;

class MixedTermRelTable extends BaseTableGateway {

    protected $table = 'mixed_term_relationships';
    protected $alias = 'mixedTermRel';
    protected $modelClass = \Edm\Model\MixedTermRel;

}
