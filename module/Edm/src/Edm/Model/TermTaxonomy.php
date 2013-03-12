<?php

namespace Edm\Model;

use 
//    Zend\InputFilter\Factory as InputFactory,
//    Zend\InputFilter\InputFilter,
    Zend\InputFilter\InputFilterAwareInterface,
    Zend\InputFilter\InputFilterInterface,
    Edm\Model\AbstractModel;

class TermTaxonomy extends AbstractModel 
implements InputFilterAwareInterface {

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
    
    public $validKeys = array(
        'term_taxonomy_id',
        'term_alias',
        'taxonomy',
        'description',
        'accessGroup',
        'childCount',
        'assocItemCount',
        'listOrder',
        'parent_id',
    );

    public function __construct($data = null) {
        if ($data) {
            $this->exchangeArray($data);
        }
    }

    public function setInputFilter(InputFilterInterface $inputFilter) {
        $this->inputFilter = $inputFilter;
    }

    public function getInputFilter() {

//        if ($this->inputFilter !== null) {
//            return $this->inputFilter;
//        }
//
//        $retVal =
//                $this->inputFilter =
//                new InputFilter();
//        $factory = new InputFactory();
//
//        // Term Name
//        $retVal->add($factory->createInput(array(
//                    'name' => 'name',
//                    'required' => true,
//                    'filters' => array(
//                        array('name' => 'StripTags'),
//                        array('name' => 'StringTrim')
//                    ),
//                    'validators' => array(
//                        array('name' => 'StringLength',
//                            'options' => array(
//                                'min' => 1,
//                                'max' => 255
//                        ))
//                    )
//                )));
//
//        // Term Alias
//        $retVal->add($factory->createInput(array(
//                    'name' => 'term_alias',
//                    'required' => false,
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
//                )));
//
//        // Term Taxonomy Group Alias
//        $retVal->add($factory->createInput(array(
//                    'name' => 'term_group_alias',
//                    'required' => false,
////                    'filters' => array(
////                        array(
////                            'name' => 'StringToLower'),
////                    ),
////                    'validators' => array(
////                        array(
////                            'name' => 'Regex',
////                            'options' => array(
////                                'pattern' => APPVAR_NAME_ALIAS_REGEX)
////                        ),
////                    )
//                )));
//
//        $this->inputFilter = $retVal;
//
//        return $retVal;
    }

}