<?php

declare(strict_types=1);

namespace Edm\Db\ResultSet\Proto;

use Zend\InputFilter\Factory as InputFactory,
    Zend\InputFilter\InputFilter;

class PostCategoryRelProto extends AbstractProto {

    protected $_allowedKeysForProto = [
        'post_id',
        'term_taxonomy_id'
    ];

    protected $_formKey = 'postCategoryRel';

    protected $_notAllowedKeysForUpdate = [
        'post_id'
    ];
    
    public function getInputFilter() {
     
        // Return input filter if exists
        if ($this->_inputFilter !== null) {
            return $this->_inputFilter;
        }
        // Return value (input filter)
        $inputFilter = 
            $this->_inputFilter = new InputFilter();
        
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
