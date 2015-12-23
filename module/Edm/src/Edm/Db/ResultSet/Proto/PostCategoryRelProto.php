<?php

namespace Edm\Db\ResultSet\Proto;

use Zend\InputFilter\Factory as InputFactory,
    Zend\InputFilter\InputFilter;

class PostCategoryRelProto extends AbstractProto {

    protected $_allowedKeysForProto = array(
        'post_id',
        'term_taxonomy_id'
    );

    protected $_formKey = 'postTermTaxonomyRel';

    public function getInputFilter() {
     
        // Return input filter if exists
        if ($this->inputFilter !== null) {
            return $this->inputFilter;
        }
        // Return value (input filter)
        $inputFilter = $this->inputFilter = new InputFilter();
        
        // Input factory
        $factory = new InputFactory();
        
        // Term Taxonomy Id
        $inputFilter->add($factory->createInput(
                self::getDefaultInputOptionsByKey('id', array(
                    'name' => 'term_taxonomy_id',
                    'required' => true
                )
        )));
        
        // Post Id
        $inputFilter->add($factory->createInput(
                self::getDefaultInputOptionsByKey('id', array(
                    'name' => 'post_id',
                    'required' => false
                )
        )));
        
        return $inputFilter;
    }

}
