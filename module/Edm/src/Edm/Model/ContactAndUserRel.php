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

}