<?php

namespace Edm\Db\TableGateway;

class FlagTable extends BaseTableGateway {

    protected $alias = 'flag';
    protected $table = 'flags';
    protected $modelClass = \Edm\Model\Flag;

}