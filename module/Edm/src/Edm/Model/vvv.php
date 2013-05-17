<?php

/*
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 */

namespace Edm\Model;

use Edm\Model\AbstractModel;

/**
 * Description of KeyValuePair
 *
 * @author ElyDeLaCruz
 */
class KeyValuePair extends AbstractModel {
    protected $validKey = array(
        'key',
        'value'
    );
}

