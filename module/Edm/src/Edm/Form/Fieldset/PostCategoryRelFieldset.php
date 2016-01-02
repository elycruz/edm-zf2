<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

namespace Edm\Form\Fieldset;

use Zend\Form\Fieldset,
    Edm\Db\ResultSet\Proto\PostCategoryRelProto;

/**
 * Description of PostCategoryRelFieldset
 *
 * @author Ely
 */
class PostCategoryRelFieldset extends Fieldset {

    public function __construct($name = 'post-category-rel-fieldset', $options = array()) {

        parent::__construct($name, $options);

        // Post Object
        $this->setObject(new PostCategoryRelProto());

        // Term Taxonomy Id
        $this->add(array(
            'name' => 'term_taxonomy_id',
            'type' => 'Zend\Form\Element\Select',
            'options' => array(
                'label' => 'Category',
                'placeholder' => 'Category'
            ),
            'attributes' => array(
                'id' => 'term_taxonomy_id',
                'required' => true,
            )
        ));
    }
    
}