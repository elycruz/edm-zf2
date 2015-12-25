<?php

namespace Edm\Db\TableGateway;

class PostTable extends BaseTableGateway {

    protected $alias = 'post';
    protected $table = 'posts';
    protected $modelClass = 'Edm\Db\ResultSet\Proto\PostProto';

}
