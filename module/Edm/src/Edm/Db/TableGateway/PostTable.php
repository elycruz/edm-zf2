<?php

namespace Edm\Db\TableGateway;

class PostTable extends BaseTableGateway {

    protected $table = 'posts';
    protected $alias = 'post';
    protected $modelClass = \Edm\Db\ResultSet\Proto\PostProto;

}
