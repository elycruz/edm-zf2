<?php

namespace Edm\Model;

use Edm\Model\AbstractModel;

class ContactAndAddressRel extends AbstractModel {

    /**
     * Valid Keys for Model
     * @var array
     */
    protected $validKeys = array(
        'contact_id',
        'address_id'
    );

}