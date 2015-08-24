<?php

namespace Edm\Model;

use Zend\InputFilter\Factory as InputFactory,
    Zend\InputFilter\InputFilter,
    Zend\InputFilter\InputFilterAwareInterface,
    Zend\InputFilter\InputFilterInterface,
    Edm\Model\AbstractModel;

class Address extends AbstractModel implements InputFilterAwareInterface {

    protected $inputFilter = null;
    
    protected $validKeys = array(
        'address_id',
        'name',
        'address',
        'zipcode',
        'city',
        'state',
        'country',
        'type',
        'userParams'
    );

    public function setInputFilter(InputFilterInterface $inputFilter) {
        $this->inputFilter = $inputFilter;
    }

    public function getInputFilter() {
        if ($this->inputFilter !== null) {
            return $this->inputFilter;
        }
        $inputFilter = new InputFilter();
        $factory = new InputFactory();
        return $this->inputFilter = $inputFilter;
    }

}