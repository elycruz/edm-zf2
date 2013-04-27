<?php

/*
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 */

namespace Edm\Form;

use Zend\Form\Fieldset,
    Edm\Model\User;

/**
 * Description of TermFieldset
 *
 * @author ElyDeLaCruz
 */
class UserFieldset extends Fieldset {

    public function __construct($name = 'user', $options = array()) {

        parent::__construct($name, $options);

        // Term Taxonomy Object
        $this->setObject(new User());
        
        // Screen Name
        $this->add(array(
            'name' => 'screenName',
            'type' => 'Zend\Form\Element\Text',
            'options' => array(
                'label' => 'Screen Name'
            ),
            'attributes' => array(
                'required' => false,
                'id' => 'screenName'
            )
        ));
        

        // Role
        $this->add(array(
            'name' => 'role',
            'type' => 'Zend\Form\Element\Select',
            'options' => array(
                'label' => 'Role',
                'value_options' => array(
                    'null' => '-- Select a Taxonomy --'
                )
            ),
            'attributes' => array(
                'id' => 'role',
                'required' => true
            )
        ));

        // Status
        $this->add(array(
            'name' => 'status',
            'type' => 'Zend\Form\Element\Select',
            'options' => array(
                'label' => 'Status',
                'value_options' => array(
                    'null' => '-- Select a Status --'
                )
            ),
            'attributes' => array(
                'id' => 'status',
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
                    'null' => '-- Select a Access Group --'
                )
            ),
            'attributes' => array(
                'id' => 'accessGroup',
                'required' => true
            )
        ));

    }

}
