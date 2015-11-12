<?php

namespace Edm\Db\TableGateway;

use BaseTableGateway;

class AddressTable extends BaseTableGateway {

    protected $alias = 'address';
    protected $table = 'addresses';
    protected $modelClass = \Edm\Model\Address;

}
