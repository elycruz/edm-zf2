<?php

/*
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 */

namespace Edm\Form;

use Zend\Form\Fieldset,
    Edm\Model\ViewModule;

/**
 * Description of ViewModuleFieldset
 * @author ElyDeLaCruz
 */
class ViewModuleFieldset extends Fieldset {

    public function __construct($name = 'view-module-fieldset', $options = array()) {

        parent::__construct($name, $options);

        // ViewModule Object
        $this->setObject(new ViewModule());

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
//        $this->add(array(
//            'name' => 'parent_id',
//            'type' => 'Zend\Form\Element\Select',
//            'options' => array(
//                'label' => 'Parent',
//                'placeholder' => 'Parent'
//            ),
//            'attributes' => array(
//                'id' => 'parent_id'
//            )
//        ));

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

        // Type
        $this->add(array(
            'name' => 'type',
            'type' => 'Zend\Form\Element\Select',
            'options' => array(
                'label' => 'Type',
                'value_options' => array(
                    'html' => 'Html'
                )
            ),
            'attributes' => array(
                'id' => 'type',
                'required' => true
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

        // Helper Name
        $this->add(array(
            'name' => 'helperName',
            'type' => 'text',
            'options' => array(
                'label' => 'Helper Name'
            ),
            'attributes' => array(
                'id' => 'helperName',
                'required' => true,
                'value' => 'Html'
            )
        ));        
        
        // Helper Type
        $this->add(array(
            'name' => 'helperType',
            'type' => 'Zend\Form\Element\Select',
            'options' => array(
                'label' => 'Helper Type',
                'value_options' => array(
                    'view' => 'View',
                    'action' => 'Action'
                )
            ),
            'attributes' => array(
                'id' => 'helperType',
                'required' => true
            )
        ));
        
        // Partial Script
        $this->add(array(
            'name' => 'partialScript',
            'type' => 'Text',
            'options' => array(
                'label' => 'Partial Script'
            ),
            'attributes' => array(
                'id' => 'partialScript',
                'size' => 72,
                'maxlength' => 255,
                'required' => false
            )
        ));
        
        
        // Allowed on Pages
        $this->add(array(
            'name' => 'allowedOnPages',
            'type' => 'Zend\Form\Element\Collection',
            'options' => array(
                'count' => 3,
                'allow_add' => true,
                'should_create_template' => true,
                'target_element' => array(
                    'type' => 'checkbox',
                    'name' => 'keyName',
                    'options' => array(
                        'value' => '*',
                        'label' => 'Select All'
                    )),
                
            ),
//            'attributes' => array(
//                'required' => false,
//                'id' => 'userParams'
//            ),
        ));
    }
}
