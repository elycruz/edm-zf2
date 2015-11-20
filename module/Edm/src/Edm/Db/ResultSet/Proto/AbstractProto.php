<?php

namespace Edm\Db\ResultSet\Proto;

use Zend\Config\Config,
    Edm\InputFilter\DefaultInputOptions,
    Edm\InputFilter\DefaultInputOptionsAware,
    Zend\InputFilter\InputFilterAwareInterface,
    Zend\InputFilter\InputFilterInterface;

/**
 * Abstract Model
 * @author ElyDeLaCruz
 */
abstract class AbstractProto extends \ArrayObject
    implements
    ProtoInterface,
    DefaultInputOptionsAware,
    InputFilterAwareInterface {

    /**
     * Default input options
     * @var \Edm\InputFilter\DefaultInputOptions
     */
    protected static $defaultInputOptions;
    
    /**
     * Valid keys allowed for proto.
     * @var array
     */
    protected $allowedKeysForProto;
    
    /**
     * Not allowed for RDBMS updates.
     * @var array
     */
    protected $notAllowedKeysForUpdate;

    /**
     * Keys that should be unset before exporting (toArray) array to db;
     * @var array
     */
    protected $notAllowedKeysForInsert;

    /**
     * Keys to omit on export to array.
     * @var array
     */
    protected $notAllowedForDb;

    /**
     * Proto names to use when calling to array to generate values.
     * @var array
     */
    protected $nestedProtoGetters;

    /**
     * Input Filter.
     * @var \Zend\InputFilter\InputFilterInterface
     */
    protected $inputFilter = null;

    /**
     * Key used when returning from `toArray` for form usage.
     * @var string
     */
    protected $_formKey;

    /**
     * Constructor
     * @param array $data
     * @param int $flags
     */
    public function __construct(array $data = null, $flags = 0) {
        parent::__construct($data === null ? array() : $data, $flags == 0 ? \ArrayObject::ARRAY_AS_PROPS : $flags);
    }

    /**
     * @return string
     */
    public function getFormKey() {
        return $this->_formKey;
    }

    /**
     * Get models valid keys
     * @return array
     */
    public function getAllowedKeysForProto () {
        return $this->allowedKeysForProto;
    }

    /**
     * Returns a list of fields not allowed for RDBMS update.
     * @return array
     */
    public function getNotAllowedKeysForUpdate () {
        return $this->notAllowedKeysForUpdate;
    }

    /**
     * Returns a list of fields not allowed for database (should be keys not in defined database).
     * @return array
     */
    public function getNotAllowedKeysForInsert () {
        return $this->notAllowedKeysForUpdate;
    }

    /**
     * Returns model as array with only set values.
     * Any fields which do not have set values won't be returned in the array.
     * @param string $operation - Operation [Update,Insert,Db,Form].  If set removes the 'notAllowedForUpdate', 'notAllowedForInsert', 'notAllowedForDb' keys from the exported array.
     * @param int $mode - Default AbstractProto::TO_ARRAY_SHALLOW (returns immediate key => value pairs but not nested ones (sub/own protos etc.)).
     * @return array
     */
    public function toArray ($operation = null, $mode = AbstractProto::TO_ARRAY_SHALLOW) {
        $retVal = array();
        switch ($mode) {
            case AbstractProto::TO_ARRAY_SHALLOW :
                $retVal = $this->toArrayShallow($retVal, $mode);
                break;
            case AbstractProto::TO_ARRAY_FLATTENED :
                $retVal = $this->toArrayFlattened($retVal, $mode);
                break;
            case AbstractProto::TO_ARRAY_NESTED :
                $retVal = $this->toArrayNested($retVal, $mode);
                break;
            default:
                $retVal = $this->toArrayShallow($retVal, $mode);
                break;
        }

        // Nest self data in an array for usage with our forms which use fieldsets
        // and require us to set the data on our forms in the form of
        // [ 'model' => [...], 'otherModel' => [...] ] // Where 'model' is also the name of our fieldsets
        if ($operation === AbstractProto::FOR_OPERATION_FORM) {
            $retVal = [
                $this->getFormKey() => $retVal
            ];
        }
        return $retVal;
    }

    /**
     * @param array $outArray
     * @param string $operation - Db operation alias if data is needed for db
     * @return array
     */
    public function toArrayShallow ($outArray, $operation) {
        foreach ($this->allowedKeysForProto as $key) {
            if (!$this->has($key)) {
                continue;
            }
            $outArray[$key] = $this->{$key};
        }
        return $outArray;
    }

    /**
     * @param array $outArray
     * @param string $operation - Db operation alias if data is needed for db
     * @return array
     */
    public function toArrayFlattened ($outArray, $operation) {

    }

    /**
     * @param array $outArray
     * @param string $operation - Db operation alias if data is needed for db
     * @return array
     */
    public function toArrayNested ($outArray, $operation) {

    }

    /**
     * Check if key exists on object.
     * @param $key string
     * @return bool
     */
    public function has ($key) {
        return isset($this->{$key});
    }

    /**
     * @param array $inputData
     * @param ProtoInterface $proto
     * @return ProtoInterface $proto
     */
    public function setAllowedKeysOnProto($inputData, $proto) {
        $validKeys = $proto->getAllowedKeysForProto();
        foreach ($validKeys as $key) {
            if (isset($inputData[$key])) {
                $proto->{$key} = $inputData[$key];
            }
        }
        return $proto;
    }

    /**
     * @param Config $options
     */
    public static function setDefaultInputOptions (Config $options) {
        self::$defaultInputOptions = $options;
    }

    /**
     * @return DefaultInputOptions
     */
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

    /**
     * @param InputFilterInterface $inputFilter
     * @return \Zend\InputFilter\InputFilterAwareInterface
     */
    public function setInputFilter(InputFilterInterface $inputFilter) {
        $this->inputFilter = $inputFilter;
        return $this;
    }

    /**
     * @return \Zend\InputFilter\InputFilterInterface
     */
    public function getInputFilter () {
        return $this->inputFilter;
    }

}
