<?php

namespace Edm\Db\TableGateway;

class ContactUserRelTable extends BaseTableGateway {

    protected $alias = 'contactUserRel';
    protected $table = 'user_contact_relationships'; // @todo rename this table here and in db.
    protected $modelClass = \Edm\Model\ContactAndUserRel;

}