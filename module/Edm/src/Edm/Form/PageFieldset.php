<?php

/*
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 */

namespace Edm\Form;

use Zend\Form\Fieldset,
    Edm\Model\Page;

/**
 * Description of PageFieldset
 * @author ElyDeLaCruz
 */
class PageFieldset extends Fieldset {

    public function __construct($name = 'page-fieldset', $options = array()) {

        parent::__construct($name, $options);

        // Page Object
        $this->setObject(new Page());

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
                'label' => 'Page Status',
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
                'label' => 'Page Type',
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
