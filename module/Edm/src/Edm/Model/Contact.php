<?php

namespace Edm\Model;

use Zend\InputFilter\Factory as InputFactory,
    Zend\InputFilter\InputFilter,
    Zend\InputFilter\InputFilterAwareInterface,
    Zend\InputFilter\InputFilterInterface,
    Edm\Model\AbstractModel;

class Term extends AbstractModel implements InputFilterAwareInterface {

    /**
     * Input Filter
     * @var Zend\InputFilter\Filter
     */
    protected $inputFilter = null;
    
    /**
     * Valid keys for model
     * @var array
     */
    public $validKeys = array(
        'contact_id',
        'parent_id',
        'name',
        'firstName',
        'middleName',
        'lastName',
        'email',
        'altEmail',
        'type',
        'userParams'
    );

    public function __construct($data = null) {
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

        // Contact Id
         
        // Parent Id
         
        // Name
         
        // First Name
         
        // Middle Name
         
        // Last Name
         
        // Email
         
        // Alternate Email
         
        // Type
         
        // User Params
         
        // Name
        $retVal->add($factory->createInput(array(
                    'name' => 'name',
                    'required' => true,
                    'filters' => array(
                        array('name' => 'StripTags'),
                        array('name' => 'StringTrim')
                    ),
                    'validators' => array(
                        array('name' => 'StringLength',
                            'options' => array(
                                'min' => 1,
                                'max' => 255
                            ))
                    )
        )));

        // Alias
        $retVal->add($factory->createInput(array(
                    'name' => 'alias',
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