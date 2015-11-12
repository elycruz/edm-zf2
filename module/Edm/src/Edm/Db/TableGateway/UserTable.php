<?php

namespace Edm\Db\TableGateway;

class UserTable extends BaseTableGateway {

    protected $table = 'user';
    protected $alias = 'users';
    protected $modelClass = \Edm\Model\User;

}
