<?php
/**
 * @author ElyDeLaCruz
 */
abstract class Edm_View_Helper_Abstract
extends Zend_View_Helper_Abstract implements
Edm_Db_DbDataHelperAccess,
        Edm_Service_Internal_TermTaxonomyAccess
{
    /**
     * Composed Edm Term Taxonomy Service
     * @var Edm_Service_Internal_TermTaxonomyService
     */
    protected $_termTaxService;
    
    /**
     * Our Db Data Helper
     * @var Edm_Db_DbDataHelper
     */
    protected $_dbDataHelper;
    
    /**
     * Holds the valid key names for the standardHtmlDiv
     * function's $options array
     * @var array $_valid_key_names
     */
    protected $_valid_key_names;
    
    public function __construct() {}

    /**
     * @return array
     */
    public function getValidKeyNames() {
        return $this->_valid_key_names;
    }

    /**
     * @param <type> $validKeyNames
     * @return Edm_View_Helper_Abstract 
     */
    public function setValidKeyNames($validKeyNames) {
        $this->_valid_key_names = $validKeyNames;
        return $this;
    }

    /**
     * Set element attribute
     *
     * @param  string $name
     * @param  mixed $value
     * @return Edm_View_Helper_Abstract
     * @throws Exception for invalid $name values
     */
    public function setAttrib($name, $value)
    {
        $name = (string) $name;
        if ('_' == $name[0]) {
            throw new Exception(sprintf('Invalid attribute "%s"; must not '.
                    'contain a leading underscore', $name));
        }

        if (null === $value) {
            unset($this->$name);
        } else {
            $this->$name = $value;
        }

        return $this;
    }

    /**
     * Set multiple attributes at once
     *
     * @param  array $attribs
     * @return Edm_View_Helper_Abstract
     */
    public function setAttribs(array $attribs)
    {
        foreach ($attribs as $key => $value) {
            $this->setAttrib($key, $value);
        }

        return $this;
    }

    /**
     * Retrieve element attribute
     *
     * @param  string $name
     * @return string
     */
    public function getAttrib($name)
    {
        $name = (string) $name;
        if (isset($this->$name)) {
            return $this->$name;
        }

        return null;
    }

    /**
     * Return all attributes
     *
     * @return array
     */
    public function getAttribs()
    {
        $attribs = get_object_vars($this);
        foreach ($attribs as $key => $value) {
            if ('_' == substr($key, 0, 1)) {
                unset($attribs[$key]);
            }
        }

        return $attribs;
    }

    /**
     * Overloading: retrieve object property
     *
     * Prevents access to properties beginning with '_'.
     *
     * @param  string $key
     * @return mixed
     */
    public function __get($key)
    {
        if ('_' == $key[0]) {
            throw new Exception(sprintf('Cannot retrieve value for '.
                    'protected/private property "%s"', $key));
        }

        if (!isset($this->$key)) {
            return null;
        }

        return $this->$key;
    }

    /**
     * Overloading: set object property
     *
     * @param  string $key
     * @param  mixed $value
     * @return voide
     */
    public function __set($key, $value)
    {
        $this->setAttrib($key, $value);
    }
    
    /**
     * Set object state from options array
     *
     * @param  array $options
     * @return Edm_View_Helper_Abstract
     */
    public function setOptions(array $options)
    {
        foreach ($options as $key => $value) {
            $method = 'set' . ucfirst($key);

            if (method_exists($this, $method)) {
                // Setter exists; use it
                $this->$method($value);
            } else {
                // Assume it's metadata
                $this->setAttrib($key, $value);
            }
        }
        return $this;
    }

    /**
     * Validate `$options` keys against
     * $_valid_value_keys
     * @param array $options
     * @throws Exception on failure notifying about invalid key
     */
    public function validateKeyNames(array $options)
    {
        /**
         * Validate $options
         */
        Edm_Util_ArrayHelper::compareKeys(
            $options, $this->getValidKeyNames());
    }

    /**
     * Convinience function/another way to call appendValidKeyName from
     * outside;  'addValidKeyName' => 'keyname' in options array
     * @param string $validKeyName
     * @return Edm_View_Helper_Abstract 
     */
    public function setAppendValidKeyName($validKeyName)
    {
        if(is_string($validKeyName)) {
            $this->_valid_key_names[] = $validKeyName;
            return $this;
        }
        throw new Exception('Invalid value type passed into '.
                'setAppendValidKeyName. Strings only allowed.');
    }

    /**
     * Convinience function/another way to call appendValidKeyNames from
     * outside;  'addValidKeyNames' => ['string'] in options array
     * @param array $validKeyNames
     * @return Edm_View_Helper_Abstract 
     */
    public function setAppendValidKeyNames($validKeyNames)
    {
        if (is_array($validKeyNames)) {
            foreach ($validKeyNames as $val) {
                $this->_valid_key_names[] = $val;
            }

            return $this;
        }
        throw new Exception('Invalid value type passed into '.
                'setAppendValidKeyNames. Allowed var type: array.');
    }

    /**
     * Delete keys from the valid key names in the view helper
     * @param array $keyNames
     * @return Edm_View_Helper_Abstract 
     */
    public function setDeleteValidKeyNames($keyNames)
    {
        if (is_array($keyNames)) {
            foreach ($keyNames as $val) {
                if (key_exists($val, $this->_valid_key_names)) {
                    unset($this->_valid_key_names[$val]);
                }
            }
            return $this;
        }
        throw new Exception('Invalid value type passed into '.
                'setDeleteKeyNames. Expected type: array.');
    }

    /**
     * Takes a json string of user params and converts into view helper options/public variables
     */
    public function setOptionsFromUserParams($userParams) {
        
        // Convert user params to usable array
        $userParams = Zend_Json::decode($userParams, Zend_Json::TYPE_ARRAY);
        
        // Set options
        $options = array();
        
        // Set our param name and value tracking params
        $paramName = $paramVal = null;
        
        // Loop through user params and sort things out
        foreach ($userParams as $key => $val) {
            
            if (strpos($key, 'name') !== false) {
                $paramName = $val;
            }
            else if (strpos($key, 'value') !== false) {
                $paramVal = $val;
            }
            
            if (!empty($paramVal) && !empty($paramName)) {
                $options[$paramName] = $paramVal;
            }
            
            $paramVal = null;
        }
        
        $this->setOptions($options);
        
        return $options;
    }
    
    public function getDbDataHelper() {
        $dbdh = $this->_dbDataHelper;
        if (empty($dbdh)) {
            if (Zend_Registry::isRegistered('edm-dbDataHelper')) {
                $dbdh = Zend_Registry::get('edm-dbDataHelper');
            }
            else {
                $dbdh = new Edm_Db_DatabaseDataHelper();
                Zend_Registry::set('edm-dbDataHelper', $dbdh);
            }
        }
        $this->_dbDataHelper = $dbdh;
        return $this->_dbDataHelper;
    }

    public function setDbDataHelper(Edm_Db_DbDataHelper $dbDataHelper) {
        $this->_dbDataHelper = $dbDataHelper;
    }
    
    public function termTaxonomyService() {
        if (empty($this->_termTaxService)) {
            if (Zend_Registry::isRegistered('edm-termTax-service')) {
                $_termTaxonomyService = Zend_Registry::get('edm-termTax-service');
            }
            else {
                    $_termTaxonomyService =
                        new Edm_Service_Internal_TermTaxonomyService();
                Zend_Registry::set('edm-termTax-service', $_termTaxonomyService);
            }
            $this->_termTaxService = $_termTaxonomyService;
        }
        return $this->_termTaxService;
    }
    
    public function resetVars() {
        foreach (get_class_vars(get_class(__CLASS__)) as $name => $default) {
            $this->{$name} = null;
        }
    }
}
