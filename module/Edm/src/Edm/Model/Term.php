<?php

namespace Edm\Model;

use Zend\InputFilter\Factory as InputFactory,
    Zend\InputFilter\InputFilter,
    Zend\InputFilter\InputFilterAwareInterface,
    Zend\InputFilter\InputFilterInterface,
    Edm\Model\AbstractModel;

class Term extends AbstractModel implements InputFilterAwareInterface {

    protected $inputFilter = null;
    public $validKeys = array(
        'term_group_alias',
        'alias',
        'name',
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

        $retVal = $this->inputFilter = new InputFilter();
        $factory = new InputFactory();

        // Name
        $retVal->add($factory->createInput(
            self::getDefaultInputOptionsByKey('name', array(
                'name' => 'name',
                'required' => true,
                    )
        )));

        // Alias
        $retVal->add($factory->createInput(
            self::getDefaultInputOptionsByKey('alias', array(
                'name' => 'alias',
                'required' => true
                    )
        )));

        // Term Group Alias
        $retVal->add($factory->createInput(
            self::getDefaultInputOptionsByKey('alias', array(
                'name' => 'term_group_alias',
                'required' => false
                    )
        )));

        $this->inputFilter = $retVal;

        return $retVal;
    }

}