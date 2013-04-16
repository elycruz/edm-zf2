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

    public function __construct($name = 'term-taxonomy', $options = array()) {

        parent::__construct($name, $options);

        // Term Taxonomy Object
        $this->setObject(new TermTaxonomy());

        // Taxonomy
        $this->add(array(
            'name' => 'taxonomy',
            'type' => 'Zend\Form\Element\Select',
            'options' => array(
                'label' => 'Taxonomy',
                'value_options' => array(
                    'taxonomy' => '-- Select a Taxonomy --'
                )
            ),
            'attributes' => array(
                'id' => 'taxonomy',
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
                'label' => 'Description'
            ),
            'name' => 'description',
            'type' => 'Zend\Form\Element\TextArea',
            'attributes' => array(
                'id' => 'description',
                'placeholder' => 'Description',
                'cols' => 72,
                'rows' => 5,
            )
        ));
        
    }

}
