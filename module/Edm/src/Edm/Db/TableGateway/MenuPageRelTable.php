<?php

namespace Edm\Db\TableGateway;

class MenuPageRelTable extends BaseTableGateway {

    protected $alias = 'menuPageRel';
    protected $table = 'page_menu_relationships'; // @todo rename this table here and in db.
    protected $modelClass = \Edm\Model\MenuPageRel;

}