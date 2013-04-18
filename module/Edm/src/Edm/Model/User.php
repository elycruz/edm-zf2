<?php

namespace Edm\Model;

use Zend\InputFilter\Factory as InputFactory,
    Zend\InputFilter\InputFilter,
    Zend\InputFilter\InputFilterAwareInterface,
    Zend\InputFilter\InputFilterInterface,
    Edm\Model\AbstractModel;

class User extends AbstractModel implements InputFilterAwareInterface {

    /**
     * Input filter
     * @var Zend\InputFilter\InputFilter
     */
    protected $inputFilter = null;

    // Defaults
    public $role        = 'user';
    public $status      = 'pending-activation';
    public $accessGroup = 'cms-manager';

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
                'required' => true
        ))));

        // Password
        $retVal->add($factory->createInput(
            self::getDefaultInputOptionsByKey('password', array(
                'name' => 'password',
                'required' => true
        ))));

        // Role
        $retVal->add($factory->createInput(
            self::getDefaultInputOptionsByKey('short-alias', array(
                    'name' => 'role',
                    'required' => false
        ))));

        // Access Group
        $retVal->add($factory->createInput(
            self::getDefaultInputOptionsByKey('short-alias', array(
                    'name' => 'accessGroup',
                    'required' => false
        ))));
        
        // Status
        $retVal->add($factory->createInput(
            self::getDefaultInputOptionsByKey('short-alias', array(
                    'name' => 'status',
                    'required' => false
        ))));
        
        
        // Activation Key
        $retVal->add($factory->createInput(
            self::getDefaultInputOptionsByKey('activation-key', array(
                    'name' => 'status',
                    'required' => false
        ))));
        
        // Last Login
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