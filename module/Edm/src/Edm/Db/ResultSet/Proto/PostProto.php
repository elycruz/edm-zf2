<?php

namespace Edm\Db\ResultSet\Proto;

use Zend\InputFilter\Factory as InputFactory,
    Zend\InputFilter\InputFilter;

class PostProto extends AbstractProto {
    
    /**
     * @var string
     */
    public $content = '';
    
    /**
     * @var string
     */
    public $excerpt = '';
    
    /**
     * @var string
     */
    public $userParams = '';
    
    /**
     * Valid keys for model
     * @var array
     */
    protected $_allowedKeysForProto = [
        'post_id',
        'parent_id',
        'title',
        'alias',
        'content',
        'excerpt',
        'hits',
        'listOrder',
        'commenting',
        'commentCount',
        'type',
        'accessGroup',
        'status',
        'userParams',
        'date_info_id'
    ];

    /**
     * Keys not allowed for update.
     * @var array
     */
    protected $_notAllowedKeysForUpdate = array(
        'post_id',
        'date_info_id'
    );

    /**
     * @var string
     */
    protected $_formKey = 'post';

    /**
     * @var array
     */
    protected $_subProtoGetters = [
        'getPostCategoryRelProto',
        'getDateInfoProto'
    ];

    /**
     * Date Info Proto.
     * @var DateInfoProto
     */
    protected $dateInfoProto;

    /**
     * Returns our input filter.
     * @return \Zend\InputFilter\InputFilter
     */
    public function getInputFilter() {

        // Return input filter if exists
        if ($this->_inputFilter !== null) {
            return $this->_inputFilter;
        }
        
        // Return value (input filter)
        $retVal = $this->_inputFilter = new InputFilter();
        
        // Input factory
        $factory = new InputFactory();
        // Post Id
        $retVal->add($factory->createInput(
                self::getDefaultInputOptionsByKey('id', array(
                    'name' => 'post_id',
                    'required' => false
                )
        )));
        // Parent Id
        $retVal->add($factory->createInput(
                self::getDefaultInputOptionsByKey('id', array(
                    'name' => 'parent_id',
                    'required' => false
                )
        )));
        
        // Alias
        $retVal->add($factory->createInput(
            self::getDefaultInputOptionsByKey('alias', array(
                    'name' => 'alias',
                    'required' => false
        ))));
        
        // Content
        $retVal->add($factory->createInput(
            self::getDefaultInputOptionsByKey('description', array(
                    'name' => 'content',
                    'required' => false
        ))));
        
        // Excerpt
        $retVal->add($factory->createInput(
            self::getDefaultInputOptionsByKey('description', array(
                    'name' => 'excerpt',
                    'required' => false
        ))));
        // Hits
        $retVal->add($factory->createInput(
            self::getDefaultInputOptionsByKey('int', array(
                    'name' => 'hits',
                    'required' => false
        ))));
        // List Order
        $retVal->add($factory->createInput(
            self::getDefaultInputOptionsByKey('int', array(
                    'name' => 'listOrder',
                    'required' => false
        ))));
        
        // Commenting
        $retVal->add($factory->createInput(
            self::getDefaultInputOptionsByKey('short-alias', array(
                    'name' => 'commenting',
                    'required' => false
        ))));
        
        // Post Status
        $retVal->add($factory->createInput(
            self::getDefaultInputOptionsByKey('short-alias', array(
                    'name' => 'status',
                    'required' => false
        ))));
        
        // Post Type
        $retVal->add($factory->createInput(
            self::getDefaultInputOptionsByKey('short-alias', array(
                    'name' => 'type',
                    'required' => false
        ))));
        
        // Access Group
        $retVal->add($factory->createInput(
            self::getDefaultInputOptionsByKey('short-alias', array(
                    'name' => 'accessGroup',
                    'required' => false
        ))));
        
        // Comment Count
        $retVal->add($factory->createInput(
            self::getDefaultInputOptionsByKey('int', array(
                    'name' => 'commentCount',
                    'required' => false
        ))));
                
        // User Params
        
        return $retVal;

    }
   
    /**
     * Gets our Date Info Proto.
     * @param array|null $data - Default `null`.
     * @return DateInfoProto
     */
    public function getPostCategoryRelProto($data = null) {
        if (empty($this->postCategoryRelProto)) {
            $this->postCategoryRelProto = new PostCategoryRelProto($data);
        }
        return $this->postCategoryRelProto;
    }
   
    /**
     * Gets our Date Info Proto.
     * @param array|null $data - Default `null`.
     * @return DateInfoProto
     */
    public function getDateInfoProto($data = null) {
        if (empty($this->dateInfoProto)) {
            $this->dateInfoProto = new DateInfoProto($data);
        }
        return $this->dateInfoProto;
    }
    
}
