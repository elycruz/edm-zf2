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
        'children'
    );

    public function __construct($data = null) {
        if (is_array($data)) {
            $this->exchangeArray($data);
        }
        $this->getInputFilter();
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
        $inputFilter->add($factory->createInput(
           self::getDefaultInputOptionsByKey('short-alias', array(
                'name' => 'taxonomy',
                'required' => true,
            )
        )));

        // Description
        $inputFilter->add($factory->createInput(
                self::getDefaultInputOptionsByKey('description', array(
                    'name' => 'description',
                    'required' => false
                )
        )));

        // Parent Id
        $inputFilter->add($factory->createInput(
                self::getDefaultInputOptionsByKey('id', array(
                    'name' => 'parent_id',
                    'required' => false
                )
        )));

        return $this->inputFilter = $inputFilter;
    }

}