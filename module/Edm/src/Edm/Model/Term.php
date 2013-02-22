<?php

namespace Edm\Model;

use Zend\InputFilter\Factory as InputFactory,
    Zend\InputFilter\InputFilter,
    Zend\InputFilter\InputFilterAwareInterface,
    Zend\InputFilter\InputFilterInterface;

class Term implements InputFilterAwareInterface {

    protected $inputFilter = null;
    public $term_id;
    public $term_group_alias;
    public $alias;
    public $name;

    public function exchangeArray(array $data) {
        $this->term_id = isset($data['term_id']) ? $data['term_id'] : null;
        $this->name = isset($data['name']) ? $data['name'] : null;
        $this->alias = isset($data['alias']) ? $data['alias'] : null;
        $this->term_group_alias =
                isset($data['term_group_alias']) ?
                $data['term_group_alias'] : null;
    }

    public function setInputFilter(InputFilterInterface $inputFilter) {
        throw new \Exception('Not used');
    }

    public function getInputFilter() {

        if ($this->inputFilter !== null) {
            return $this->inputFilter;
        }

        $retVal =
                $this->inputFilter =
                new InputFilter();
        $factory = new InputFactory();

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
                    'required' => true,
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

        // Term Group Alias
        $retVal->add($factory->createInput(array(
                    'name' => 'term_group_alias',
                    'required' => false,
//                    'filters' => array(
//                        array(
//                            'name' => 'StringToLower'),
//                    ),
//                    'validators' => array(
//                        array(
//                            'name' => 'Regex',
//                            'options' => array(
//                                'pattern' => APPVAR_NAME_ALIAS_REGEX)
//                        ),
//                    )
                )));

        $this->inputFilter = $retVal;

        return $retVal;
    }

    public function toArray() {
        return array('alias' => $this->alias,
            'name' => $this->name,
            'term_group_alias' => $this->term_group_alias);
    }

}