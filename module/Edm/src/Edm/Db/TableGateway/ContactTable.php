<?php

namespace Edm\Db\TableGateway;

class ContactTable extends BaseTableGateway {

    protected $alias = 'contact';
    protected $table = 'contacts';
    protected $modelClass = 'Edm\Db\ResultSet\Proto\ContactProto';

}
