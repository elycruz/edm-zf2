<?php

/*
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 */

namespace Edm\Form;

use Zend\Form\Fieldset,
    Edm\Model\Post;

/**
 * Description of PostFieldset
 * @author ElyDeLaCruz
 */
class PostFieldset extends Fieldset {

    public function __construct($name = 'post', $options = array()) {

        parent::__construct($name, $options);

        // Post Object
        $this->setObject(new Post());

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
                'label' => 'Alias'
            ),
            'attributes' => array(
                'id' => 'alias',
                'required' => true
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

        // Description
        $this->add(array(
            'options' => array(
                'label' => 'Content'
            ),
            'name' => 'content',
            'type' => 'Zend\Form\Element\TextArea',
            'attributes' => array(
                'id' => 'description',
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
            'name' => 'content',
            'type' => 'Zend\Form\Element\TextArea',
            'attributes' => array(
                'id' => 'description',
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
                'label' => 'Status',
                'value_options' => array(
                    '*' => '-- Select a Status --'
                )
            ),
            'attributes' => array(
                'id' => 'status',
                'required' => true
            )
        ));
        
        // Commenting
        $this->add(array(
            'name' => 'commenting',
            'type' => 'Zend\Form\Element\Select',
            'options' => array(
                'label' => 'Commenting',
                'value_options' => array(
                    'disabled' => '-- Select a Commenting Status --'
                )
            ),
            'attributes' => array(
                'id' => 'commenting',
                'required' => true
            )
        ));

        // Access Group
        $this->add(array(
            'name' => 'accessGroup',
            'type' => 'Zend\Form\Element\Select',
            'options' => array(
                'label' => 'Access Group',
                'value_options' => array(
                    '*' => '-- Select a Access Group --'
                )
            ),
            'attributes' => array(
                'id' => 'accessGroup',
                'required' => true
            )
        ));

        // Type
        $this->add(array(
            'name' => 'type',
            'type' => 'Zend\Form\Element\Select',
            'options' => array(
                'label' => 'Type',
                'value_options' => array(
                    '*' => '-- Select a Type --'
                )
            ),
            'attributes' => array(
                'id' => 'type',
                'required' => true
            )
        ));

    }
}
