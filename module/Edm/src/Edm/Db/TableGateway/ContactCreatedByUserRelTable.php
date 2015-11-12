<?php

namespace Edm\Db\TableGateway;

class ContactCreatedByUserRelTable extends BaseTableGateway {

    protected $alias = 'contactCreatedByUserRel';
    protected $table = 'contact_created_by_user_relationships';
    protected $modelClass = \Edm\Model\Con;

}