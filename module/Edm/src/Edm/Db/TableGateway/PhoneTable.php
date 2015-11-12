<?php

namespace Edm\Db\TableGateway;

class PhoneTable extends BaseTableGateway {

    protected $table = 'phone_numbers';
    protected $alias = 'phone';
    protected $modelClass = \Edm\Model\Phone;

}
