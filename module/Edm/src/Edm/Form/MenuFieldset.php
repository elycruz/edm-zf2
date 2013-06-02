<?php

/*
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 */

namespace Edm\Form;

use Zend\Form\Fieldset,
    Edm\Model\Menu;

/**
 * Description of MenuFieldset
 * @author ElyDeLaCruz
 */
class MenuFieldset extends Fieldset {

    public function __construct($name = 'menu-fieldset', $options = array()) {

        parent::__construct($name, $options);

        // Post Object
        $this->setObject(new Menu());

        // Min Depth
        $this->add(array(
            'name' => 'minDepth',
            'type' => 'text',
            'options' => array(
                'label' => 'Minimum Depth',
            ),
            'attributes' => array(
                'value' => 0,
                'id' => 'minDepth',
                'required' => false,
            )
        ));
        
        // Max Depth
        $this->add(array(
            'name' => 'maxDepth',
            'type' => 'text',
            'options' => array(
                'label' => 'Maximum Depth'
            ),
            'attributes' => array(
                'value' => 0,
                'id' => 'maxDepth',
                'required' => false,
            )
        ));
        
        // Only Active Branch
        $this->add(array(
            'name' => 'onlyActiveBranch',
            'type' => 'checkbox',
            'options' => array(
                'label' => 'Only Active Branch'
            ),
            'attributes' => array(
                'value' => 0,
                'id' => 'onlyActiveBranch',
                'required' => false,
            )
        ));
        
        // Render Parents
        $this->add(array(
            'name' => 'renderParents',
            'type' => 'checkbox',
            'options' => array(
                'label' => 'Render Parents'
            ),
            'attributes' => array(
                'value' => 0,
                'id' => 'renderParents',
                'required' => false,
            )
        ));
        
        // Is Main Menu
        $this->add(array(
            'name' => 'isMainMenu',
            'type' => 'checkbox',
            'options' => array(
                'label' => 'Is Main Menu'
            ),
            'attributes' => array(
                'value' => 0,
                'id' => 'isMainMenu',
                'required' => false,
            )
        ));
        
        // Use Module Helper
        $this->add(array(
            'name' => 'useModuleHelper',
            'type' => 'checkbox',
            'options' => array(
                'label' => 'Use Module Helper'
            ),
            'attributes' => array(
                'value' => 0,
                'id' => 'useModuleHelper',
                'required' => false,
            )
        ));
        
        // Ul Class
        $this->add(array(
            'name' => 'ulClass',
            'type' => 'text',
            'options' => array(
                'label' => 'Ul Class'
            ),
            'attributes' => array(
                'id' => 'ulClass',
                'required' => false,
            )
        ));
        
        // Menu Partial Script
        $this->add(array(
            'name' => 'menuPartialScript',
            'type' => 'text',
            'options' => array(
                'label' => 'Menu Partial Script'
            ),
            'attributes' => array(
                'id' => 'menuPartialScript',
                'required' => false,
            )
        ));
        
        // Menu Helper
        $this->add(array(
            'name' => 'menuHelper',
            'type' => 'text',
            'options' => array(
                'label' => 'Menu Helper'
            ),
            'attributes' => array(
                'id' => 'menuHelper',
                'required' => false,
            )
        ));
     
    }
    
}
