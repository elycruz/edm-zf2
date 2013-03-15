<?php

namespace Edm\Model;

use Zend\InputFilter\Factory as InputFactory,
    Zend\InputFilter\InputFilter,
    Zend\InputFilter\InputFilterAwareInterface,
    Zend\InputFilter\InputFilterInterface,
    Edm\Model\AbstractModel;

class TermTaxonomy extends AbstractModel implements InputFilterAwareInterface {

    protected $inputFilter = null;
    
    protected $validKeys = array(
        'term_taxonomy_id',
        'term_alias',
        'taxonomy',
        'description',
        'accessGroup',
        'childCount',
        'assocItemCount',
        'listOrder',
        'parent_id',
        
        // Joined keys
        'term_name',
        'term_group_alias',
        'taxonomy_name',
        'parent_name',
        'parent_alias',
        
        // Custom keys
        'descendants'
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

        $inputFilter = new InputFilter();
        $factory = new InputFactory();

        // Taxonomy
        $inputFilter->add($factory->createInput(array(
                    'name' => 'taxonomy',
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

        // Description
        $inputFilter->add($factory->createInput(array(
                    'name' => 'description',
                    'required' => true,
                    'filters' => array(
                        array('name' => 'StringTrim')
                    )
        )));

        // Parent Id
        $inputFilter->add($factory->createInput(array(
                    'name' => 'parent_id',
                    'required' => false,
                    'validators' => array(
                        array(
                            'name' => 'Regex',
                            'options' => array(
                                'pattern' => APPVAR_NAME_ALIAS_REGEX)
                        ),
                    )
        )));

        // Access Group
        $inputFilter->add($factory->createInput(array(
                    'name' => 'accessGroup',
                    'required' => false,
                    'validators' => array(
                        array(
                            'name' => 'Regex',
                            'options' => array(
                                'pattern' => APPVAR_NAME_ALIAS_REGEX)
                        ),
                    )
        )));

        return $this->inputFilter = $inputFilter;
    }

}