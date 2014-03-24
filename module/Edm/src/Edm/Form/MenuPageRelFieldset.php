<?php

/*
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 */

namespace Edm\Form;

use Zend\Form\Fieldset,
    Edm\Model\MenuPageRel;

/**
 * Description of MenuPageRelFieldset
 * @author ElyDeLaCruz
 */
class MenuPageRelFieldset extends Fieldset {

    public function __construct($name = 'menu-page-rel-fieldset', $options = array()) {

        parent::__construct($name, $options);

        // Post Object
        $this->setObject(new MenuPageRel());

        // Menu Id
        $this->add(array(
            'name' => 'menu_id',
            'type' => 'Zend\Form\Element\Select',
            'options' => array(
                'label' => 'Menu Id'
            ),
            'attributes' => array(
                'id' => 'menu_id'
            )
        ));
    }
    
}
