<?php
/**
 * Created by IntelliJ IDEA.
 * User: Ely
 * Date: 11/15/2015
 * Time: 7:10 PM
 */

namespace Edm\Db\ResultSet\Proto;

use Zend\InputFilter\Factory as InputFactory,
    Zend\InputFilter\InputFilter;

class TermTaxonomyProto extends AbstractProto  {

    protected $validKeys = array(
        'term_taxonomy_id',
        'term_alias',
        'taxonomy',
        'description',
        'childCount',
        'assocItemCount',
        'listOrder',
        'parent_id',
        // Joined keys
        'term_name',
        'term_group_alias',
        'taxonomy_name',
        'parent_name',
        'parent_alias',
        // Custom keys
        'children'
    );

    public function getInputFilter() {

        if ($this->inputFilter !== null) {
            return $this->inputFilter;
        }

        $inputFilter = $this->inputFilter = new InputFilter();
        $factory = new InputFactory();

        // Taxonomy
        $inputFilter->add($factory->createInput(
            self::getDefaultInputOptionsByKey('short-alias', array(
                    'name' => 'taxonomy',
                    'required' => true,
                )
            )));

        // Access Group
        $inputFilter->add($factory->createInput(
            self::getDefaultInputOptionsByKey('short-alias', array(
                    'name' => 'accessGroup',
                    'required' => false,
                )
            )));

        // Alias
        $inputFilter->add($factory->createInput(
            self::getDefaultInputOptionsByKey('alias', array(
                    'name' => 'term_alias',
                    'required' => true
                )
            )));

        // Description
        $inputFilter->add($factory->createInput(
            self::getDefaultInputOptionsByKey('description', array(
                    'name' => 'description',
                    'required' => false
                )
            )));

        // List Order
        $inputFilter->add($factory->createInput(
            self::getDefaultInputOptionsByKey('int', array(
                    'name' => 'listOrder',
                    'required' => false
                )
            )));

        // Parent Id
        $inputFilter->add($factory->createInput(
            self::getDefaultInputOptionsByKey('id', array(
                    'name' => 'parent_id',
                    'required' => false
                )
            )));

        return $this->inputFilter;
    }

}
