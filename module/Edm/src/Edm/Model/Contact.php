<?php

namespace Edm\Model;

use Zend\InputFilter\Factory as InputFactory,
    Zend\InputFilter\InputFilter,
    Zend\InputFilter\InputFilterAwareInterface,
    Zend\InputFilter\InputFilterInterface,
    Edm\Model\AbstractModel;

class Contact extends AbstractModel implements InputFilterAwareInterface {

    /**
     * Input Filter
     * @var Zend\InputFilter\Filter
     */
    protected $inputFilter = null;
    
    /**
     * Contact type
     * @var string
     */
    public $type = 'user';
    
    public $userParams = '';
    
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

        // Parent Id
        $retVal->add($factory->createInput(
            self::getDefaultInputOptionsByKey('id', array(
                'name' => 'parent_id',
                'required' => false
        ))));

        // Name
        $retVal->add($factory->createInput(
            self::getDefaultInputOptionsByKey('name', array(
                'name' => 'name',
                'required' => false
        ))));

        // First Name
        $retVal->add($factory->createInput(
            self::getDefaultInputOptionsByKey('short-name', array(
                'name' => 'firstName',
                'required' => false
        ))));
        
        // Middle Name
        $retVal->add($factory->createInput(
            self::getDefaultInputOptionsByKey('name', array(
                'name' => 'middleName',
                'required' => false
        ))));
        
        // Last Name
        $retVal->add($factory->createInput(
            self::getDefaultInputOptionsByKey('short-name', array(
                'name' => 'lastName',
                'required' => false
        ))));
        
        // Email
        $retVal->add($factory->createInput(
            self::getDefaultInputOptionsByKey('email', array(
                'name' => 'email',
                'required' => true
        ))));
        
        // Alternate Email
        $retVal->add($factory->createInput(
            self::getDefaultInputOptionsByKey('email', array(
                'name' => 'altEmail',
                'required' => false
        ))));
        

        // Type
        $retVal->add($factory->createInput(
            self::getDefaultInputOptionsByKey('short-alias', array(
                'name' => 'type',
                'required' => false
        ))));
        
//         User Params
//        $retVal->add($factory->createInput(
//            self::getDefaultInputOptionsByKey('short-alias', array(
//                'name' => 'contact-type',
//                'required' => false
//        ))));

                $this->inputFilter = $retVal;

        return $retVal;
    }

}