<?php

namespace Edm\Db\TableGateway;

class PostTable extends BaseTableGateway {

    protected $table = 'post';
    protected $alias = 'posts';
    protected $modelClass = \Edm\Model\Post;

}
