<?php

namespace Edm\Db\TableGateway;

class ViewModuleTable extends BaseTableGateway {

    protected $table = 'viewModule';
    protected $alias = 'view_modules';
    protected $modelClass = \Edm\Model\ViewModule;

}
