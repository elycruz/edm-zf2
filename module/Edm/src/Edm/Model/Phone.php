<?php

namespace Edm\Model;

use Zend\InputFilter\Factory as InputFactory,
    Zend\InputFilter\InputFilter,
    Zend\InputFilter\InputFilterAwareInterface,
    Zend\InputFilter\InputFilterInterface,
    Edm\Model\AbstractModel;

class Term extends AbstractModel 
implements InputFilterAwareInterface {
    
    protected $inputFilter = null;
    
    public $validKeys = array(
        'phone_id',
        'name',
        'country_code',
        'area_code',
        'state_code',
        'number'
    );

    public function setInputFilter(InputFilterInterface $inputFilter) {
        $this->inputFilter = $inputFilter;
    }

    public function getInputFilter() {

        if ($this->inputFilter !== null) {
            return $this->inputFilter;
        }

        $retVal = 
            $this->inputFilter = 
                new InputFilter();
        
//        $factory = new InputFactory();
//
//        $this->inputFilter = $retVal;

        return $retVal;
    }

}