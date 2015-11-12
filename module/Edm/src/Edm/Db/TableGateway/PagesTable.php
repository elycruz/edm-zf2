<?php

namespace Edm\Db\TableGateway;

class PageTable extends BaseTableGateway {

    protected $table = 'page';
    protected $alias = 'pages';
    protected $modelClass = \Edm\Model\Page;

}
