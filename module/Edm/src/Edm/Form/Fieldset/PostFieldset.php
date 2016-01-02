<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

namespace Edm\Form\Fieldset;

/**
 * Description of PostFieldset
 *
 * @author Ely
 */
use Zend\Form\Fieldset,
    Edm\Db\ResultSet\Proto\PostProto;

/**
 * Description of PostFieldset
 * @author ElyDeLaCruz
 */
class PostFieldset extends Fieldset {

    public function __construct($name = 'post-fieldset', $options = array()) {

        parent::__construct($name, $options);

        // Post Object
        $this->setObject(new PostProto());

        // Title
        $this->add(array(
            'name' => 'title',
            'type' => 'Text',
            'options' => array(
                'label' => 'Title'
            ),
            'attributes' => array(
                'id' => 'title',
                'required' => true
            )
        ));

        // Alias
        $this->add(array(
            'name' => 'alias',
            'type' => 'Text',
            'options' => array(
                'label' => 'Alias',
                'required' => false,
            ),
            'attributes' => array(
                'id' => 'alias',
                'required' => false,
            )
        ));
        
        // Parent Id
        $this->add(array(
            'name' => 'parent_id',
            'type' => 'Zend\Form\Element\Select',
            'options' => array(
                'label' => 'Parent',
                'placeholder' => 'Parent'
            ),
            'attributes' => array(
                'id' => 'parent_id'
            )
        ));

        // Content
        $this->add(array(
            'options' => array(
                'label' => 'Content'
            ),
            'name' => 'content',
            'type' => 'TextArea',
            'attributes' => array(
                'id' => 'content',
                'placeholder' => 'Content',
                'cols' => 72,
                'rows' => 5,
            )
        ));

        // Excerpt
        $this->add(array(
            'options' => array(
                'label' => 'Excerpt'
            ),
            'name' => 'excerpt',
            'type' => 'Zend\Form\Element\TextArea',
            'attributes' => array(
                'id' => 'excerpt',
                'placeholder' => 'Excerpt',
                'cols' => 72,
                'rows' => 5,
            )
        ));
        
        // Status
        $this->add(array(
            'name' => 'status',
            'type' => 'Zend\Form\Element\Select',
            'options' => array(
                'label' => 'Post Status',
                'value_options' => array(
                    'published' => 'Published'
                )
            ),
            'attributes' => array(
                'id' => 'status',
                'required' => false
            )
        ));
        
        // Commenting
        $this->add(array(
            'name' => 'commenting',
            'type' => 'Zend\Form\Element\Select',
            'options' => array(
                'label' => 'Commenting',
                'value_options' => array(
                    'enabled' => 'Enabled'
                )
            ),
            'attributes' => array(
                'id' => 'commenting',
                'required' => false
            )
        ));

        // Access Group
        $this->add(array(
            'name' => 'accessGroup',
            'type' => 'Zend\Form\Element\Select',
            'options' => array(
                'label' => 'Access Group',
                'value_options' => array(
                    'guest' => 'Guest'
                )
            ),
            'attributes' => array(
                'id' => 'accessGroup',
                'required' => false
            )
        ));

        // Type
        $this->add(array(
            'name' => 'type',
            'type' => 'Zend\Form\Element\Select',
            'options' => array(
                'label' => 'Post Type',
                'value_options' => array(
                    'blog' => 'Blog'
                )
            ),
            'attributes' => array(
                'id' => 'type',
                'required' => false
            )
        ));

    }
}