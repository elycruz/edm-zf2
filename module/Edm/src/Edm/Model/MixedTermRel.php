<?php

/*
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 */

/**
 * Description of MixedTermRel
 *
 * @author ElyDeLaCruz
 */

namespace Edm\Model;

use Zend\InputFilter\Factory as InputFactory,
    Zend\InputFilter\InputFilter,
    Zend\InputFilter\InputFilterAwareInterface,
    Zend\InputFilter\InputFilterInterface,
    Edm\Model\AbstractModel;

/**
 * Description of ViewModule
 *
 * @author ElyDeLaCruz
 */
class MixedTermRel extends AbstractModel 
implements InputFilterAwareInterface {

    protected $validKeys = array(
        'object_id',
        'objectType',
        'term_taxonomy_id',
        'status',
        'accessGroup',
        'listOrder'
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

        // Object Id
        $retVal->add($factory->createInput(
                self::getDefaultInputOptionsByKey('id', array(
                    'name' => 'object_id',
                    'required' => true
                )
        )));

        // Object Type
        $retVal->add($factory->createInput(
            self::getDefaultInputOptionsByKey('short-alias', array(
                    'name' => 'objectType',
                    'required' => true
        ))));
        
        // Term Taxonomy Id
        $retVal->add($factory->createInput(
            self::getDefaultInputOptionsByKey('id', array(
                    'name' => 'term_taxonomy_id',
                    'required' => true
        ))));
        
        // Status
        $retVal->add($factory->createInput(
            self::getDefaultInputOptionsByKey('short-alias', array(
                    'name' => 'status',
                    'required' => false
        ))));
        
        // Access Group
        $retVal->add($factory->createInput(
            self::getDefaultInputOptionsByKey('short-alias', array(
                    'name' =>  'access-group',
                    'required' => false
        ))));
        
        // List Order
        $retVal->add($factory->createInput(
            self::getDefaultInputOptionsByKey('int', array(
                    'name' => 'listOrder',
                    'required' => false
        ))));

        $this->inputFilter = $retVal;

        return $retVal;
    }

}
