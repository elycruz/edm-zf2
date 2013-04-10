<?php

namespace Edm\Model;

use Zend\InputFilter\Factory as InputFactory,
    Zend\InputFilter\InputFilter,
    Zend\InputFilter\InputFilterAwareInterface,
    Zend\InputFilter\InputFilterInterface,
    Edm\Model\AbstractModel;

class User extends AbstractModel 
implements InputFilterAwareInterface {
    
    /**
     * Input filter
     * @var Zend\InputFilter\InputFilter
     */
    protected $inputFilter = null;
    
    /**
     * Valid keys for model
     * @var array
     */
    public $validKeys = array(
        'user_id',
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
        $retVal->add($factory->createInput(array(
                    'name' => 'screenName',
                    'required' => true,
                    'validators' => array(
                        array('name' => 'Alnum')
                    )
                )));

        // Password
        $retVal->add($factory->createInput(array(
                    'name' => 'password',
                    'required' => true,
                    'validators' => array(
                        array('name' => 'Regex',
                            'options' => array(
                                'pattern' => ''
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

        // Access Group
        
        // Status
        
        // Last Login
        
        // Activation Key
        
        // Registered Date
        
        // Registered By Id
        
        // Last Updated 
        
        // Last Updated By Id
        
        // Checked In Date
        
        // Checked Out Date
        
        // Checked Out By Id
        
        $this->inputFilter = $retVal;

        return $retVal;
    }

}