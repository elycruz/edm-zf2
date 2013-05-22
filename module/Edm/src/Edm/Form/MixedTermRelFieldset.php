<?php

/*
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 */

namespace Edm\Form;

use Zend\Form\Fieldset,
    Edm\Model\MixedTermRel;

/**
 * Description of MixedTermRelFieldset
 * @author ElyDeLaCruz
 */
class MixedTermRelFieldset extends Fieldset {

    public function __construct($name = 'mixed-term-rel-fieldset', $options = array()) {

        parent::__construct($name, $options);

        // Post Object
        $this->setObject(new MixedTermRel());

        // Term Taxonomy Id
        $this->add(array(
            'name' => 'term_taxonomy_id',
            'type' => 'Zend\Form\Element\Select',
            'options' => array(
                'label' => 'Term Taxonomy Id'
            ),
            'attributes' => array(
                'id' => 'term_taxonomy_id',
                'required' => true,
            )
        ));

        // Status
        $this->add(array(
            'name' => 'status',
            'type' => 'Zend\Form\Element\Select',
            'options' => array(
                'label' => 'Status'
            ),
            'attributes' => array(
                'id' => 'status',
                'required' => true,
            )
        ));

        // Object Type
        $this->add(array(
            'name' => 'objectType',
            'type' => 'Zend\Form\Element\Select',
            'options' => array(
                'label' => 'Object Type'
            ),
            'attributes' => array(
                'id' => 'objectType',
                'required' => true,
            )
        ));

        // Access Group
        $this->add(array(
            'name' => 'accessGroup',
            'type' => 'Zend\Form\Element\Select',
            'options' => array(
                'label' => 'Access Group'
            ),
            'attributes' => array(
                'id' => 'accessGroup',
                'required' => true,
            )
        ));
    }
    
}
