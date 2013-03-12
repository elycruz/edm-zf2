<?php

/*
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 */

namespace Edm\Form;

use Zend\Form\Fieldset,
Edm\Model\TermTaxonomy;

/**
 * Description of TermFieldset
 *
 * @author ElyDeLaCruz
 */
class TermTaxonomyFieldset extends Fieldset {

    public function __construct($name = null, $options = array()) {
        
        $term = new TermTaxonomy();
        $this->setObject($term);
        
// Taxonomy
        $this->add(array(
            'options' => array(
                'label' => 'Taxonomy'
            ),
            'name' => 'taxonomy',
            'attributes' => array(
                'id' => 'taxonomy',
                'required' => true,
                'placeholder' => 'Taxonomy',
                'type' => 'text'
            )
        ));

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

        // Description
        $this->add(array(
            'options' => array(
                'label' => 'Description'
            ),
            'name' => 'description',
            'type' => 'Zend\Form\Element\TextArea',
            'attributes' => array(
                'id' => 'description',
                'cols' => 72,
                'rows' => 5,
            )
        ));
        
        // Access Group
        $this->add(array(
            'options' => array(
                'label' => 'Access Group'
            ),
            'name' => 'accessGroup',
            'type' => 'Zend\Form\Element\Select',
            'attributes' => array(
                'id' => 'accessGroup',
                'value_options' => array(
                    'value' => 'Label'
                )
            )
        ));
    }

}
