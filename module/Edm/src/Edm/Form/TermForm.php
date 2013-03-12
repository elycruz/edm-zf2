<?php

/*
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 * @todo add searching cabpability
 */

namespace Edm\Form;

/**
 * Description of TermForm
 *
 * @author ElyDeLaCruz
 */
class TermForm extends EdmForm {

    public function __construct() {
        parent::__construct('term-form');
        $this->setAttribute('method', 'post');

        // Name
        $this->add(array(
            'options' => array(
                'label' => 'Name'
            ),
            'name' => 'name',
            'attributes' => array(
                'id' => 'name',
                'required' => true,
                'placeholder' => 'Name',
                'type' => 'text'
            )
        ));

        // Alias
        $this->add(array(
            'options' => array(
                'label' => 'Alias'
            ),
            'name' => 'alias',
            'attributes' => array(
                'id' => 'alias',
                'required' => true,
                'placeholder' => 'Alias',
                'type' => 'text'
            )
        ));

        // Term Group Alias
        $this->add(array(
            'options' => array(
                'label' => 'Term Group Alias'
            ),
            'name' => 'term_group_alias',
            'attributes' => array(
                'id' => 'term_group_alias',
                'placeholder' => 'Term Group Alias',
                'type' => 'text'
            )
        ));

        $this->add(array(
            'name' => 'reset',
            'attributes' => array(
                'type' => 'reset',
                'value' => 'Reset',
                'class' => 'btn big-btn'
            )
        ));

        $this->add(array(
            'name' => 'submit',
            'attributes' => array(
                'type' => 'submit',
                'value' => 'Submit',
                'class' => 'btn big-btn'
            )
        ));
    }

}
