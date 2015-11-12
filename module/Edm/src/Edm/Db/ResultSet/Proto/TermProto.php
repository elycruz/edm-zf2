<?php

namespace Edm\Db\ResultSet\Proto;

use Zend\InputFilter\Factory as InputFactory,
    Zend\InputFilter\InputFilter,
    Zend\InputFilter\InputFilterAwareInterface,
    Zend\InputFilter\InputFilterInterface;

class TermProto extends AbstractProto implements InputFilterAwareInterface {

    protected $inputFilter = null;

    public $validKeys = array(
        'term_group_alias',
        'alias',
        'name',
    );

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