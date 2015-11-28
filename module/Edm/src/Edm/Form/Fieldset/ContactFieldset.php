<?php

namespace Edm\Form\Fieldset;

use Zend\Form\Fieldset,
    Edm\Db\ResultSet\Proto\ContactProto;

/**
 * @author ElyDeLaCruz
 */
class ContactFieldset extends Fieldset {

    public function __construct($name = 'contact', $options = array()) {

        parent::__construct($name, $options);

        // Term Taxonomy Object
        $this->setObject(new ContactProto());

        // Name
        $this->add(array(
            'name' => 'name',
            'type' => 'Zend\Form\Element\Text',
            'options' => array(
                'label' => 'Name'
            ),
            'attributes' => array(
                'id' => 'name',
                'required' => false,
            )
        ));
        
        // First Name
        $this->add(array(
            'name' => 'firstName',
            'type' => 'Zend\Form\Element\Text',
            'options' => array(
                'label' => 'First Name'
            ),
            'attributes' => array(
                'id' => 'firstName',
                'required' => false,
            )
        ));

        // Middle Name
        $this->add(array(
            'name' => 'middleName',
            'type' => 'Zend\Form\Element\Text',
            'options' => array(
                'label' => 'Middle Name'
            ),
            'attributes' => array(
                'id' => 'middleName',
                'required' => false,
            )
        ));

        // Last Name
        $this->add(array(
            'name' => 'lastName',
            'type' => 'Zend\Form\Element\Text',
            'options' => array(
                'label' => 'Last Name'
            ),
            'attributes' => array(
                'id' => 'lastName',
                'required' => false,
            )
        ));

        // Email
        $this->add(array(
            'name' => 'email',
            'type' => 'Zend\Form\Element\Email',
            'options' => array(
                'label' => 'Email'
            ),
            'attributes' => array(
                'id' => 'email',
                'required' => true,
            )
        ));

        // Alt Email
        $this->add(array(
            'name' => 'altEmail',
            'type' => 'Zend\Form\Element\Email',
            'options' => array(
                'label' => 'Alternate Email'
            ),
            'attributes' => array(
                'id' => 'altEmail'
            )
        ));
        
        // Contact Type
        $this->add(array(
            'name' => 'type',
            'type' => 'Zend\Form\Element\Select',
            'options' => array(
                'label' => 'Contact Type',
                'value_options' => array(
                    'type' => '-- Select a Contact Type --'
                )
            ),
            'attributes' => array(
                'id' => 'type',
                'required' => false
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

    }

}
