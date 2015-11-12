<?php

namespace Edm\Db\TableGateway;

class TermTable extends BaseTableGateway {

    protected $alias = 'term';
    protected $table = 'terms';
    protected $modelClass = '\\Edm\\Db\\ResultSet\\Proto\\TermProto';

}
