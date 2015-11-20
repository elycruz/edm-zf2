<?php

namespace Edm\Db\ResultSet\Proto;

use Zend\InputFilter\Factory as InputFactory,
    Zend\InputFilter\InputFilter;

class TermProto extends AbstractProto {

    protected $allowedKeysForProto = array(
        'term_group_alias',
        'alias',
        'name',
    );

    protected $_formKey = 'term';

    public function getInputFilter() {
        // If input filter is set return it
        if ($this->inputFilter !== null) {
            return $this->inputFilter;
        }

        $retVal = new InputFilter();
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

        $this->setInputFilter($retVal);

        return $retVal;
    }

}
