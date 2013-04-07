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
        'screenName',
        'password',
        'role',
        'accessGroup',
        'status',
        'lastLogin',
        'activationKey',
        'registeredDate',
        'registeredById',
        'lastUpdated',
        'lastUpdatedById',
        'checkedInDate',
        'checkedOutDate',
        'checkedOutById'
    );

    public function __construct ($data = null) {
        if (is_array($data)) {
            $this->exchangeArray($data);
        }
    }
    
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
        $factory = new InputFactory();

        // First Name
        $retVal->add($factory->createInput(array(
                    'name' => 'firstName',
                    'required' => true,
                    'filters' => array(
                        array('name' => 'StripTags'),
                        array('name' => 'StringTrim')
                    ),
                    'validators' => array(
                        array('name' => 'StringLength',
                            'options' => array(
                                'min' => 1,
                                'max' => 55
                        ))
                    )
                )));

        // Last Name
        $retVal->add($factory->createInput(array(
                    'name' => 'lastName',
                    'required' => true,
                    'filters' => array(
                        array('name' => 'StripTags'),
                        array('name' => 'StringTrim')
                    ),
                    'validators' => array(
                        array('name' => 'StringLength',
                            'options' => array(
                                'min' => 1,
                                'max' => 55
                        ))
                    )
                )));

        // Role
        $retVal->add($factory->createInput(array(
                    'name' => 'role',
                    'required' => false,
                    'filters' => array(
                        array(
                            'name' => 'StringToLower'),
                    ),
                    'validators' => array(
                        array(
                            'name' => 'Regex',
                            'options' => array(
                                'pattern' => APPVAR_NAME_ALIAS_REGEX)
                        ),
                    )
                )));


        $this->inputFilter = $retVal;

        return $retVal;
    }

}