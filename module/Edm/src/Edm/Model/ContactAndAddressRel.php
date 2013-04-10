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

    public function __construct($data = null) {
        if (is_array($data)) {
            $this->exchangeArray($data);
        }
    }
}