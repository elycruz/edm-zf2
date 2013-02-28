<?php

namespace Edm\Model;

use Zend\InputFilter\Factory as InputFactory,
    Zend\InputFilter\InputFilter,
    Zend\InputFilter\InputFilterAwareInterface,
    Zend\InputFilter\InputFilterInterface;

class TermTaxonomy implements InputFilterAwareInterface {

    protected $inputFilter = null;
    public $term_taxonomy_id;
    public $term_alias;
    public $taxonomy;
    public $description;
    public $accessGroup;
    public $childCount;
    public $assocItemCount;
    public $listOrder;
    public $parent_id;

    public function __construct ($data = null) {
        if ($data) {
            $this->exchangeArray($data);
        }
    }
    
    public function exchangeArray(array $data) {
//        $this->name = isset($data['name']) ? $data['name'] : null;
//    $this->term_taxonomy_id = isset($data['term_taxonomy_id']) ?
//                $data['term_taxonomy_id'] : null;
//    $this->term_alias = isset($data['term_alias']) ?
//                $data['term_alias'] : null;
//    $this->taxonomy = isset($data['taxonomy']) ?
//                $data['taxonomy'] : null;
//    $this->description = isset($data['description']) ?
//                $data['description'] : null;
//    $this->accessGroup = isset($data['accessGroup']) ?
//                $data['accessGroup'] : null;
//    $this->childCount = isset($data['description']) ?
//                $data['childCount'] : null;;
//    $this->assocItemCount = ;
//    $this->listOrder = ;
//    $this->parent_id = ;
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

        // Term Taxonomy Group Alias
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