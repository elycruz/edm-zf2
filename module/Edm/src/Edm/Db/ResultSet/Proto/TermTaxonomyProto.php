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

    /**
     * Allowed keys for setting properties on this array object
     * @var array
     */
    protected $_allowedKeysForProto = array(
        'term_taxonomy_id',
        'term_alias',
        'taxonomy',
        'description',
        'accessGroup',
        'listOrder',
        'parent_id',
        'term_name',
        'name',
        'alias',
        'term_group_alias'
    );

    /**
     * @var array
     */
    protected $_notAllowedKeysForInsert = [
        'term_taxonomy_id',
        'name',
        'alias',
        'term_group_alias',
        'term_name'
    ];

    /**
     * @var array
     */
    protected $_notAllowedKeysForUpdate = [
        'term_taxonomy_id',
        'name',
        'alias',
        'term_group_alias',
        'term_name'
    ];

    /**
     * @var TermProto
     */
    protected $termProto;

    /**
     * @var TermTaxonomyProxyProto
     */
    protected $termTaxonomyProxyProto;

    /**
     * @var string
     */
    protected $_formKey = 'termTaxonomy';

    /**
     * @var array
     */
    protected $_subProtoGetters = [
        'getTermProto',
        'getTermTaxonomyProxyProto',
    ];

    /**
     * @return InputFilter|\Zend\InputFilter\InputFilterInterface
     */
    public function getInputFilter() {

        if ($this->_inputFilter !== null) {
            return $this->_inputFilter;
        }

        $inputFilter = $this->_inputFilter = new InputFilter();
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

        return $inputFilter;
    }

    /**
     * @param TermProto $termProto
     * @return TermProto
     */
    public function getTermProto ($termProto = null) {
        if (isset($termProto)) {
            $this->termProto = $termProto;
        }
        else if (!isset($this->termProto)) {
            $this->termProto = new TermProto();
        }
        return $this->termProto;
    }

    /**
     * @param TermTaxonomyProxyProto $termTaxonomyProxyProto
     * @return TermTaxonomyProxyProto
     */
    public function getTermTaxonomyProxyProto ($termTaxonomyProxyProto = null) {
        if (isset($termTaxonomyProxyProto)) {
            $this->termTaxonomyProxyProto = $termTaxonomyProxyProto;
        }
        else if (!isset($this->termTaxonomyProxyProto)) {
            $this->termTaxonomyProxyProto = new TermTaxonomyProxyProto();
        }
        return $this->termTaxonomyProxyProto;
    }
    
}
