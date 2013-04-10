<?php

namespace Edm\Model;

use Edm\Model\AbstractModel;

class ContactAndUserRel extends AbstractModel {

    /**
     * Valid Keys for Model
     * @var array
     */
    protected $validKeys = array(
        'screeName',
        'email'
    );

    public function __construct($data = null) {
        if (is_array($data)) {
            $this->exchangeArray($data);
        }
    }
}