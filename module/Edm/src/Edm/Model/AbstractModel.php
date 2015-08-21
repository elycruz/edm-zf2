<?php

namespace Edm\Model;

use Zend\Config\Config,
    Edm\InputFilter\DefaultInputOptions;

/**
 * Abstract Model
 * @author ElyDeLaCruz
 */
class AbstractModel extends \ArrayObject {
    
    /**
     * Valid keys for model
     * @var array
     */
    protected $validKeys;
    
    /**
     * Not allowed for updates (ommitted from updates)
     * @var array
     */
    protected $notAllowedForUpdate;
    
    /**
     * Default input options
     * @var Edm\InputFilter\DefaultInputOptions
     */
    protected static $defaultInputOptions;
    
    /**
     * Constructor
     * @param array $data
     */
    public function __construct(array $data = null) {
        
        // Set default values
        if (!empty($data)) {
            $this->exchangeArray($data);
        }
    }

    /**
     * Exchange array
     * @param array $data
     * @return \Edm\Model\AbstractModel
     */
    public function exchangeArray(array $data) {
        foreach ($data as $key => $val) {
            if (in_array($key, $this->validKeys)) {
                $this->{$key} = $val;
            }
        }
        return $this;
    }
    
    /**
     * Setter
     * @param string $name
     * @param mixed $value
     */
    public function __set($name, $value) {
        $this->{$name} = $value;
    }

    /**
     * Getter
     * @param string $name
     */
    public function __get($name) {
        if (!isset($this->{$name})) {
            $this->{$name} = null;
        }
        return $this->{$name};
    }

    /**
     * Get models valid keys
     * @return array
     */
    public function getValidKeys () {
        return $this->validKeys;
    }
    
    /**
     * Returns model as array with only set values.
     * Any fields which do not have set values won't be returned in the array.
     * @param bool $omitNull default true
     * @return array
     */
    public function toArray ($omitNull = true) {
        $retVal = array();
        foreach ($this->validKeys as $key) {
            $val = $this->{$key};
            if ($omitNull && !isset($val)) {
                continue;
            }
            $retVal[$key] = $val;
        }
        return $retVal;
    }
    
    public static function setDefaultInputOptions (Config $options) {
        self::$defaultInputOptions = $options;
    }
    
    public static function getDefaultInputOptions () {
        if (empty(self::$defaultInputOptions)) {
            self::$defaultInputOptions = new DefaultInputOptions();
        }
        return self::$defaultInputOptions;
    }
    
    /**
     * Gets default shared input options
     * @param string $key
     * @param array $defaults
     * @return array
     */
    public static function getDefaultInputOptionsByKey ($key, array $defaults) {
        $retVal = null;
        $options = self::getDefaultInputOptions();
        
        // Get the offset for key
        if ($options->offsetExists($key)) {
            $retVal = $options->get($key);
            $output = array();
            if (!empty($retVal)) {
                if ($retVal->offsetExists('validators')) {
                    $output['validators'] = $retVal->validators->toArray();
                }
                if ($retVal->offsetExists('filters')) {
                    $output['filters'] = $retVal->filters->toArray();
                }
                $retVal = $output;
            }
        }
        
        // Merge return value with defaults if necessary
        if (!empty($defaults) && is_array($retVal)) {
            $retVal = array_replace($retVal, $defaults);
        }
        
        return $retVal;
    }
    
    public function getArrayCopy () {}
    
}
