<?php

/*
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 */

namespace Edm\Form;

use Zend\Form\Fieldset,
    Edm\Model\Post;

/**
 * Description of PostTermRelFieldset
 * @author ElyDeLaCruz
 */
class PostTermRelFieldset extends Fieldset {

    public function __construct($name = 'post-term-rel-fieldset', $options = array()) {

        parent::__construct($name, $options);

        // Post Object
        $this->setObject(new Post());

        // Term Taxonomy Id
        $this->add(array(
            'name' => 'term_taxonomy_id',
            'type' => 'Zend\Form\Element\Select',
            'options' => array(
                'label' => 'Category',
                'placeholder' => 'Category'
            ),
            'attributes' => array(
                'id' => 'term_taxonomy_id'
            )
        ));
    }
    
}
