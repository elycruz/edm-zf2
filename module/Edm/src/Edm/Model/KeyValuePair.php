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
    
     /**
     * Input filter
     * @var Zend\InputFilter\InputFilter
     */
    protected $inputFilter = null;
    
    public function __construct($data = null) {
        if (is_array($data)) {
            $this->exchangeArray($data);
        }
    }

    public function setInputFilter(InputFilterInterface $inputFilter) {
        $this->inputFilter = $inputFilter;
    }

    public function getInputFilter() {

        // Return input filter if exists
        if ($this->inputFilter !== null) {
            return $this->inputFilter;
        }

        // Return value (input filter)
        $retVal =
                $this->inputFilter =
                new InputFilter();

        // Input factory
        $factory = new InputFactory();

        // Screen Name
        $retVal->add($factory->createInput(
            self::getDefaultInputOptionsByKey('screen-name', array(
                'name' => 'screenName',
                'required' => false
        ))));

        // Password
        $retVal->add($factory->createInput(
            self::getDefaultInputOptionsByKey('password', array(
                'name' => 'password',
                'required' => true
        ))));

        $this->inputFilter = $retVal;

        return $retVal;
    }
    
}

