<?php
/**
 * Created by IntelliJ IDEA.
 * User: Ely
 * Date: 11/12/2015
 * Time: 1:15 PM
 */

namespace Edm\Db\TableGateway;

class ContactAddressRelTable extends BaseTableGateway {

    protected $alias = 'contactAddressRel';
    protected $table = 'contacts_address_relationships';
    protected $modelClass = \Edm\Model\ContactAndAddressRel;

}