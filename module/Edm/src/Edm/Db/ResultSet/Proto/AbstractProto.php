<?php

namespace Edm\Db\ResultSet\Proto;

use Zend\Config\Config,
    Edm\InputFilter\DefaultInputOptions;

/**
 * Abstract Model
 * @author ElyDeLaCruz
 */
class AbstractProto extends \ArrayObject implements ProtoInterface{
    
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
     * Proto names to use when calling to array to generate values.
     * @var array
     */
    protected $protoNames;
    
    /**
     * Constructor
     * @param array $data
     * @param int $flags
     */
    public function __construct(array $data = null, $flags = 0) {
        parent::__construct($data === null ? array() : $data, $flags == 0 ? \ArrayObject::ARRAY_AS_PROPS : $flags);
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
            if (!$this->has($key)) {
                continue;
            }
            $val = $this->{$key};
            if ($omitNull && !isset($val)) {
                continue;
            }
            $retVal[$key] = $val;
        }
        return $retVal;
    }

    /**
     * Check if key exists on object.
     * @param $key string
     * @return bool
     */
    public function has ($key) {
        return array_key_exists($key, $this) === 1
            || isset($this->{$key});
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

}
