<?php

namespace Edm\Db\TableGateway;

class UserTable extends BaseTableGateway {

    protected $alias = 'user';
    protected $table = 'users';
    protected $modelClass = 'Edm\Db\ResultSet\Proto\UserProto';

}
