<?php

namespace Edm\Model;

use Edm\Model\AbstractModel;

class ContactAndPhoneRel extends AbstractModel {

    /**
     * Valid Keys for Model
     * @var array
     */
    protected $validKeys = array(
        'contact_id',
        'phone_id'
    );

}