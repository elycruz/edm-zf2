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
        $termTax = new TermTaxonomy();
        $this->setObject($termTax);

        // Taxonomy
        $this->add(array(
            'name' => 'taxonomy',
            'type' => 'Zend\Form\Element\Select',
            'options' => array(
                'label' => 'Taxonomy',
                'value_options' => array(
                    'null' => '-- Select a Taxonomy --'
                )
            ),
            'attributes' => array(
                'id' => 'taxonomy',
                'required' => true
            )
        ));

        // Parent Id
        $this->add(array(
            'options' => array(
                'label' => 'Parent'
            ),
            'name' => 'parent_id',
            'attributes' => array(
                'id' => 'parent_id',
                'placeholder' => 'Parent',
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
                'placeholder' => 'Description',
                'cols' => 72,
                'rows' => 5,
            )
        ));

        // Access Group
        $this->add(array(
            'name' => 'accessGroup',
            'type' => 'Zend\Form\Element\Select',
            'options' => array(
                'label' => 'Access Group',
                'value_options' => array(
                    'null' => '-- Select an Access Group --'
                )
            ),
            'attributes' => array(
                'id' => 'accessGroup',
                'required' => true,
            )
        ));
    }

}
