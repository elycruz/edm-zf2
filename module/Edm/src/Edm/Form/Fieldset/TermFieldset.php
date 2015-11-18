<?php

namespace Edm\Form\Fieldset;

use Zend\Form\Fieldset,
    Edm\Db\ResultSet\Proto\TermProto;

/**
 * Description of TermFieldset
 *
 * @author ElyDeLaCruz
 */
class TermFieldset extends Fieldset {

    public function __construct($name = 'term', $options = array()) {

        // Parent construct
        parent::__construct($name, $options);

        // Term object
        $term = new TermProto();

        // Set object to bind fieldset to
        $this->setObject($term);

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
    }

}
