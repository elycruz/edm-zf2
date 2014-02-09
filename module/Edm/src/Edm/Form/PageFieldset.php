<?php

namespace Edm\Form;

use Zend\Form\Fieldset,
    Edm\Model\Page;

/**
 * Description of PageFieldset
 * @todo separate mvc config section into it's own fieldset.
 * @todo separate uri config section into it's own fieldset.
 * @todo have a create a page category  on post category create.
 * @author ElyDeLaCruz
 */
class PageFieldset extends Fieldset {

    public function __construct($name = 'page-fieldset', $options = array()) {
        parent::__construct($name, $options);
        $this->init();
    }

    public function init() {

        // Page Object
        $this->setObject(new Page());

        // Term Taxonomy Service
        // Label
        $this->add(array(
            'name' => 'label',
            'type' => 'Text',
            'options' => array(
                'label' => 'Label'
            ),
            'attributes' => array(
                'id' => 'label',
                'required' => true
            )
        ));

        // Alias
        $this->add(array(
            'name' => 'alias',
            'type' => 'Text',
            'options' => array(
                'label' => 'Alias'
            ),
            'attributes' => array(
                'id' => 'alias',
                'required' => true
            )
        ));

        // 
        // For Zend_Navigation_Page_Mvc
        // ---------------------------------------
        // Module
        $this->add(array(
            'name' => 'mvc_module',
            'type' => 'Text',
            'options' => array(
                'label' => 'Module'
            ),
            'attributes' => array(
                'id' => 'mvc_module'
            )
        ));

        // Controller
        $this->add(array(
            'name' => 'mvc_controller',
            'type' => 'Text',
            'options' => array(
                'label' => 'Controller'
            ),
            'attributes' => array(
                'id' => 'mvc_controller'
            )
        ));

        // Action
        $this->add(array(
            'name' => 'mvc_action',
            'type' => 'Text',
            'options' => array(
                'label' => 'Action'
            ),
            'attributes' => array(
                'id' => 'mvc_action'
            )
        ));

        // Route
        $this->add(array(
            'name' => 'mvc_route',
            'type' => 'Text',
            'options' => array(
                'label' => 'Route'
            ),
            'attributes' => array(
                'id' => 'mvc_route'
            )
        ));

        // ResetParamsOnRender
        $this->add(array(
            'type' => 'checkbox',
            'name' => 'mvc_resetParamsOnRender',
            'options' => array(
                'value' => '1',
                'label' => 'Reset params on render'
            ),
            'attributes' => array(
                'id' => 'mvc_resetParamsOnRender'
            )
        ));

        // Visible
        $this->add(array(
            'type' => 'checkbox',
            'name' => 'visible',
            'options' => array(
                'value' => '1',
                'label' => 'Visible'
            ),
            'attributes' => array(
                'id' => 'visible'
            )
        ));

        // Uri
        $this->add(array(
            'name' => 'acl_uri',
            'type' => 'Text',
            'options' => array(
                'label' => 'Uri/Url'
            ),
            'attributes' => array(
                'id' => 'acl_uri',
                'required' => true
            )
        ));

        // ---------------------------------------
        // Acl elements
        // ---------------------------------------
        // Privilege
        $this->add(array(
            'name' => 'acl_privilege',
            'type' => 'Text',
            'options' => array(
                'label' => 'Privilege',
                'required' => false,
            ),
            'attributes' => array(
                'id' => 'acl_privilege',
                'required' => false,
            )
        ));

        // Resource
        $this->add(array(
            'name' => 'acl_resource',
            'type' => 'Text',
            'options' => array(
                'label' => 'Resource',
                'required' => false,
            ),
            'attributes' => array(
                'id' => 'acl_resource',
                'required' => false,
            )
        ));

        // Category Id
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

        // Parent Id
        $this->add(array(
            'name' => 'parent_id',
            'type' => 'Zend\Form\Element\Select',
            'options' => array(
                'label' => 'Parent Page',
                'placeholder' => 'Parent'
            ),
            'attributes' => array(
                'id' => 'parent_id'
            )
        ));

        // Description
        $this->add(array(
            'name' => 'description',
            'type' => 'TextArea',
            'options' => array(
                'label' => 'Description'
            ),
            'attributes' => array(
                'id' => 'content',
                'placeholder' => 'Description',
                'cols' => 72,
                'rows' => 5,
            )
        ));

        // Type
        $this->add(array(
            'name' => 'type',
            'type' => 'Zend\Form\Element\Select',
            'options' => array(
                'label' => 'Page Type',
                'value_options' => array(
                    'null' => '-- Select a Page Type --'
                )
            ),
            'attributes' => array(
                'id' => 'type',
                'required' => false
            )
        ));
    }

}
