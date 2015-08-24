<?php

namespace Edm\Model;

use Zend\InputFilter\Factory as InputFactory,
    Zend\InputFilter\InputFilter,
    Zend\InputFilter\InputFilterAwareInterface,
    Zend\InputFilter\InputFilterInterface,
    Edm\Model\PostTermRel,
    Edm\Model\AbstractModel;

class Post extends AbstractModel implements InputFilterAwareInterface {
    
    public $content = '';
    
    public $excerpt = '';
    
    public $userParams = '';
    
    /**
     * Valid keys for model
     * @var array
     */
    public $validKeys = array(
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
        'userParams'
    );
    
    /**
     * Input filter
     * @var Zend\InputFilter\InputFilter
     */
    protected $inputFilter = null;
    
    /**
     * PostTermRel Proto Object
     * @var Edm\Model\PostTermRel
     */
    protected $postTermRelProto;

    public function setInputFilter(InputFilterInterface $inputFilter) {
        $this->inputFilter = $inputFilter;
    }

    public function getInputFilter() {

        // Return input filter if exists
        if ($this->inputFilter !== null) {
            return $this->inputFilter;
        }

        // Return value (input filter)
        $retVal =
                $this->inputFilter =
                new InputFilter();

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
        
        $this->inputFilter = $retVal;

        return $retVal;
    }
    
    /**
     * Gets PostTermRel Proto
     * @param mixed array $data
     * @return Edm\Model\PostTermRel
     */
    public function getPostTermRelProto($data = null) {
        if (empty($this->postTermRelProto)) {
            $this->postTermRelProto = new PostTermRel($data);
        }
        return $this->postTermRelProto;
    }

    /**
     *  Sets Post Term Rel Proto
     * @param array | \Edm\Model\AbstractModel $data
     */
    public function setPostTermRelProto($data) {
        if (is_array($data)) {
            $this->postTermRelProto = new PostTermRel($data);
        }
        else if ($data instanceof AbstractModel) {
            $this->postTermRelProto = $data;
        }
    }
    
    public function getDateInfoProto($data = null) {
        if (empty($this->dateInfoProto)) {
            $this->dateInfoProto = new DateInfo($data);
        }
        return $this->dateInfoProto;
    }
    
    public function setDateInfoProto($data) {
        if (is_array($data)) {
            $this->dateInfoProto = new DateInfo($data);
        }
        else if ($data instanceof AbstractModel) {
            $this->dateInfoProto = $data;
        }
    }
    
}